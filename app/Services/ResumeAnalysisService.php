<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AnalysisReport;
use App\Models\AppNotification;
use App\Models\ExtractedResumeData;
use App\Models\Resume;
use App\Models\ResumeScore;
use App\Models\ResumeSkillMap;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class ResumeAnalysisService
{
    public function __construct(
        protected ResumeTextExtractorService $textExtractorService,
        protected ResumeParserService $resumeParserService,
        protected SkillMatcherService $skillMatcherService,
        protected ResumeScorerService $resumeScorerService
    ) {
    }

    public function startLiveAnalysis(Resume $resume, ?string $manualText = null, ?string $ipAddress = null): void
    {
        $resume->loadMissing(['user', 'jobRole']);

        if ($resume->analysis_status === 'in_progress') {
            return;
        }

        $manualText = trim((string) $manualText);

        if ($manualText !== '') {
            Cache::put($this->getManualTextCacheKey($resume), $manualText, now()->addMinutes(15));
        } else {
            Cache::forget($this->getManualTextCacheKey($resume));
        }

        $this->updateResumeProgress($resume, 'processing', 'in_progress', 25);

        ActivityLog::create([
            'user_id' => $resume->user_id,
            'resume_id' => $resume->id,
            'activity_type' => 'resume_analysis_started',
            'activity_message' => 'Resume analysis started. Live progress will update automatically.',
            'ip_address' => $ipAddress,
        ]);

        $this->notifyUser(
            $resume->user_id,
            'Analysis Started',
            'Your resume analysis has started for ' . $resume->file_name . '.',
            'info'
        );

        $this->notifyAdmins(
            'Candidate Analysis Started',
            ($resume->user?->name ?? 'A candidate') . ' started analysis for ' . $resume->file_name . '.',
            'info'
        );
    }

    public function advanceLiveAnalysis(Resume $resume, ?string $ipAddress = null): void
    {
        $resume->refresh();

        if ($resume->analysis_status !== 'in_progress') {
            return;
        }

        if ($resume->progress_percent < 50) {
            $this->updateResumeProgress($resume, 'processing', 'in_progress', 50);

            return;
        }

        if ($resume->progress_percent < 75) {
            $this->updateResumeProgress($resume, 'processing', 'in_progress', 75);

            return;
        }

        $manualText = Cache::pull($this->getManualTextCacheKey($resume));

        $this->processAnalysis($resume, is_string($manualText) ? $manualText : null, $ipAddress, false);
    }

    public function analyze(Resume $resume, ?string $manualText = null): array
    {
        return $this->processAnalysis($resume, $manualText, request()->ip(), true);
    }

    protected function processAnalysis(
        Resume $resume,
        ?string $manualText = null,
        ?string $ipAddress = null,
        bool $manageProgress = true
    ): array
    {
        $resume->loadMissing(['user', 'jobRole']);

        if ($manageProgress) {
            $this->updateResumeProgress($resume, 'processing', 'in_progress', 25);
        }

        try {
            $extractedText = $this->textExtractorService->extract($resume, $manualText);
            $rawText = trim($extractedText['text']);

            if (mb_strlen($rawText) < 40) {
                throw new RuntimeException(
                    'Could not read enough text from this file. Open the resume details page, paste resume text in the fallback box, and click Analyze again.'
                );
            }

            if ($manageProgress) {
                $this->updateResumeProgress($resume, 'processing', 'in_progress', 50);
            }

            $parsedData = $this->resumeParserService->parse($rawText, $resume->user?->name);
            $matchResults = $this->skillMatcherService->match(
                $rawText . "\n" . ($parsedData['skills_section'] ?? ''),
                $resume->jobRole
            );

            if ($manageProgress) {
                $this->updateResumeProgress($resume, 'processing', 'in_progress', 75);
            }

            $scores = $this->resumeScorerService->calculate($parsedData, $matchResults);
            $reportData = $this->buildAnalysisReportData($resume, $scores, $matchResults);

            DB::transaction(function () use ($resume, $parsedData, $matchResults, $scores, $reportData): void {
                ExtractedResumeData::updateOrCreate(
                    ['resume_id' => $resume->id],
                    [
                        'full_name' => $parsedData['full_name'],
                        'email' => $parsedData['email'],
                        'phone' => $parsedData['phone'],
                        'address' => $parsedData['address'],
                        'education' => $parsedData['education'],
                        'experience' => $parsedData['experience'],
                        'projects' => $parsedData['projects'],
                        'certifications' => $parsedData['certifications'],
                        'extracted_skills' => $matchResults['detected_skills'],
                        'summary' => $parsedData['summary'],
                        'raw_text' => $parsedData['raw_text'],
                    ]
                );

                ResumeSkillMap::where('resume_id', $resume->id)->delete();

                foreach ($matchResults['resume_skill_rows'] as $row) {
                    ResumeSkillMap::create([
                        'resume_id' => $resume->id,
                        'skill_id' => $row['skill_id'],
                        'matched_type' => $row['matched_type'],
                    ]);
                }

                ResumeScore::updateOrCreate(
                    ['resume_id' => $resume->id],
                    $scores
                );

                AnalysisReport::updateOrCreate(
                    ['resume_id' => $resume->id],
                    $reportData
                );

                $resume->update([
                    'upload_status' => 'analyzed',
                    'analysis_status' => 'completed',
                    'progress_percent' => 100,
                ]);
            });

            Cache::forget($this->getManualTextCacheKey($resume));

            ActivityLog::create([
                'user_id' => $resume->user_id,
                'resume_id' => $resume->id,
                'activity_type' => 'resume_analysis',
                'activity_message' => 'Resume analyzed successfully with total score ' . ($scores['total_score'] ?? 0) . '/100.',
                'ip_address' => $ipAddress,
            ]);

            $this->notifyUser(
                $resume->user_id,
                'Analysis Completed',
                'Your resume report is ready with score ' . ($scores['total_score'] ?? 0) . '/100.',
                'success'
            );

            $this->notifyAdmins(
                'Analysis Completed',
                ($resume->user?->name ?? 'Candidate') . ' completed analysis for ' . $resume->file_name . '.',
                'success'
            );

            return [
                'parsed_data' => $parsedData,
                'match_results' => $matchResults,
                'scores' => $scores,
                'report_data' => $reportData,
            ];
        } catch (Throwable $exception) {
            Cache::forget($this->getManualTextCacheKey($resume));

            $safeErrorMessage = $this->sanitizeLogMessage($exception->getMessage());

            $resume->update([
                'upload_status' => 'failed',
                'analysis_status' => 'error',
                'progress_percent' => 0,
            ]);

            ActivityLog::create([
                'user_id' => $resume->user_id,
                'resume_id' => $resume->id,
                'activity_type' => 'resume_analysis_error',
                'activity_message' => 'Resume analysis failed. ' . $safeErrorMessage,
                'ip_address' => $ipAddress,
            ]);

            $this->notifyUser(
                $resume->user_id,
                'Analysis Failed',
                'Resume analysis could not be completed. ' . $safeErrorMessage,
                'danger'
            );

            $this->notifyAdmins(
                'Analysis Failed',
                'Resume analysis failed for ' . $resume->file_name . '.',
                'danger'
            );

            throw new RuntimeException($safeErrorMessage);
        }
    }

    protected function updateResumeProgress(Resume $resume, string $uploadStatus, string $analysisStatus, int $progress): void
    {
        $resume->update([
            'upload_status' => $uploadStatus,
            'analysis_status' => $analysisStatus,
            'progress_percent' => $progress,
        ]);
    }

    protected function buildAnalysisReportData(Resume $resume, array $scores, array $matchResults): array
    {
        $recommendation = $this->getRecommendationLabel((int) ($scores['total_score'] ?? 0));
        $matchedSkills = implode(', ', $matchResults['matched_skills'] ?? []);
        $missingSkills = implode(', ', $matchResults['missing_skills'] ?? []);

        $reportText = "Recommendation: {$recommendation}\n";
        $reportText .= 'Total Score: ' . ($scores['total_score'] ?? 0) . "/100\n";
        $reportText .= 'Job Match Percentage: ' . (count($matchResults['job_role_required_skills'] ?? []) > 0
            ? (int) round((count($matchResults['matched_skills'] ?? []) / count($matchResults['job_role_required_skills'])) * 100)
            : 0) . "%\n\n";
        $reportText .= 'Matched Skills: ' . ($matchedSkills !== '' ? $matchedSkills : 'None') . "\n";
        $reportText .= 'Missing Skills: ' . ($missingSkills !== '' ? $missingSkills : 'None') . "\n\n";
        $reportText .= 'Strengths:' . "\n" . ($scores['strengths'] ?: 'No strengths generated.') . "\n\n";
        $reportText .= 'Improvements:' . "\n" . ($scores['improvements'] ?: 'No improvements generated.') . "\n\n";
        $reportText .= 'Feedback:' . "\n" . ($scores['feedback'] ?: 'No feedback generated.');

        return [
            'report_title' => 'Resume Analysis Report - ' . $resume->file_name,
            'report_text' => $reportText,
            'report_file' => null,
            'generated_by' => $resume->user_id,
        ];
    }

    protected function sanitizeLogMessage(string $message): string
    {
        $safeMessage = @iconv('UTF-8', 'UTF-8//IGNORE', $message);
        $safeMessage = $safeMessage !== false ? $safeMessage : $message;
        $safeMessage = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $safeMessage) ?? $safeMessage;
        $safeMessage = preg_replace('/\s+/', ' ', $safeMessage) ?? $safeMessage;

        return trim(mb_substr($safeMessage, 0, 500));
    }

    protected function getRecommendationLabel(int $totalScore): string
    {
        return match (true) {
            $totalScore >= 80 => 'Excellent',
            $totalScore >= 60 => 'Good',
            $totalScore >= 40 => 'Average',
            default => 'Needs Improvement',
        };
    }

    protected function getManualTextCacheKey(Resume $resume): string
    {
        return 'resume_manual_text_' . $resume->id;
    }

    protected function notifyUser(int $userId, string $title, string $message, string $type = 'info'): void
    {
        AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);
    }

    protected function notifyAdmins(string $title, string $message, string $type = 'info'): void
    {
        User::query()
            ->admins()
            ->active()
            ->pluck('id')
            ->each(function (int $adminId) use ($title, $message, $type): void {
                AppNotification::create([
                    'user_id' => $adminId,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => false,
                ]);
            });
    }
}

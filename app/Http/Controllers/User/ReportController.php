<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Resume::query()
            ->with(['jobRole', 'resumeScore', 'analysisReport'])
            ->where('user_id', auth()->id())
            ->where('analysis_status', 'completed')
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('user.reports.index', compact('reports'));
    }

    public function show(Resume $resume): View|RedirectResponse
    {
        $resume = $this->findOwnedResume($resume->id);

        if ($resume->analysis_status !== 'completed') {
            return redirect()
                ->route('user.resumes.show', $resume)
                ->with('error', 'This resume report is not ready yet. Please complete analysis first.');
        }

        $resume->loadMissing([
            'jobRole',
            'resumeScore',
            'analysisReport',
            'extractedResumeData',
            'resumeSkillMaps.skill',
        ]);

        return view('user.reports.show', $this->prepareReportViewData($resume));
    }

    public function print(Resume $resume): View|RedirectResponse
    {
        $resume = $this->findOwnedResume($resume->id);

        if ($resume->analysis_status !== 'completed') {
            return redirect()
                ->route('user.resumes.show', $resume)
                ->with('error', 'This resume report is not ready yet. Please complete analysis first.');
        }

        $resume->loadMissing([
            'jobRole',
            'resumeScore',
            'analysisReport',
            'extractedResumeData',
            'resumeSkillMaps.skill',
        ]);

        return view('user.reports.print', $this->prepareReportViewData($resume));
    }

    protected function findOwnedResume(int $resumeId): Resume
    {
        return Resume::query()
            ->where('user_id', auth()->id())
            ->findOrFail($resumeId);
    }

    protected function prepareReportViewData(Resume $resume): array
    {
        $detectedSkillMaps = $resume->resumeSkillMaps
            ->where('matched_type', 'detected')
            ->filter(fn ($item) => $item->skill !== null)
            ->values();

        $matchedSkillMaps = $resume->resumeSkillMaps
            ->where('matched_type', 'matched')
            ->filter(fn ($item) => $item->skill !== null)
            ->values();

        $missingSkillMaps = $resume->resumeSkillMaps
            ->where('matched_type', 'missing')
            ->filter(fn ($item) => $item->skill !== null)
            ->values();

        $extraSkillMaps = $resume->resumeSkillMaps
            ->where('matched_type', 'extra')
            ->filter(fn ($item) => $item->skill !== null)
            ->values();

        $score = $resume->resumeScore;
        $report = $resume->analysisReport;

        return [
            'resume' => $resume,
            'report' => $report,
            'score' => $score,
            'detectedSkillMaps' => $detectedSkillMaps,
            'matchedSkillMaps' => $matchedSkillMaps,
            'missingSkillMaps' => $missingSkillMaps,
            'extraSkillMaps' => $extraSkillMaps,
            'requiredSkills' => $resume->jobRole?->required_skills_list ?? [],
            'recommendationLabel' => $score?->recommendation_label ?? 'Needs Improvement',
            'recommendationBadgeClass' => $score?->recommendation_badge_class ?? 'text-bg-danger',
            'jobMatchPercentage' => $score?->job_match_percentage ?? 0,
        ];
    }
}

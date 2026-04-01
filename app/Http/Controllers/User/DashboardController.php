<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $stats = $this->buildDashboardPayload($user->id);

        return view('user.dashboard', array_merge(
            ['user' => $user],
            $stats
        ));
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->buildDashboardPayload((int) Auth::id()));
    }

    protected function buildDashboardPayload(int $userId): array
    {
        $recentResumes = Resume::query()
            ->with(['jobRole', 'resumeScore', 'analysisReport'])
            ->where('user_id', $userId)
            ->latest('uploaded_at')
            ->take(5)
            ->get();

        $averageScore = Resume::query()
            ->where('user_id', $userId)
            ->join('resume_scores', 'resumes.id', '=', 'resume_scores.resume_id')
            ->avg('resume_scores.total_score');

        return [
            'totalResumes' => Resume::query()->where('user_id', $userId)->count(),
            'completedAnalyses' => Resume::query()->where('user_id', $userId)->where('analysis_status', 'completed')->count(),
            'pendingAnalyses' => Resume::query()->where('user_id', $userId)->where('analysis_status', '!=', 'completed')->count(),
            'averageScore' => (int) round($averageScore ?? 0),
            'recentResumes' => $recentResumes,
            'total_resumes' => Resume::query()->where('user_id', $userId)->count(),
            'completed_analyses' => Resume::query()->where('user_id', $userId)->where('analysis_status', 'completed')->count(),
            'pending_analyses' => Resume::query()->where('user_id', $userId)->where('analysis_status', '!=', 'completed')->count(),
            'average_score' => (int) round($averageScore ?? 0),
            'recent_resumes' => $recentResumes->map(function (Resume $resume): array {
                return [
                    'id' => $resume->id,
                    'file_name' => $resume->file_name,
                    'job_role' => $resume->jobRole?->title ?? 'Not Selected',
                    'analysis_status' => $resume->analysis_status,
                    'analysis_status_label' => str($resume->analysis_status)->replace('_', ' ')->title()->toString(),
                    'analysis_status_badge_class' => $resume->analysis_status_badge_class,
                    'total_score' => $resume->resumeScore?->total_score ?? 0,
                    'uploaded_at' => optional($resume->uploaded_at)->format('d M Y') ?? '-',
                    'resume_url' => route('user.resumes.show', $resume),
                    'report_url' => $resume->analysis_status === 'completed' ? route('user.reports.show', $resume) : null,
                ];
            })->values()->all(),
            'last_updated' => now()->format('d M Y, h:i:s A'),
        ];
    }
}

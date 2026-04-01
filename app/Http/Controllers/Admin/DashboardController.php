<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobRole;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', $this->buildDashboardPayload());
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->buildDashboardPayload());
    }

    protected function buildDashboardPayload(): array
    {
        $totalUsers = User::count();
        $totalCandidates = User::candidates()->count();
        $totalAdmins = User::admins()->count();
        $totalResumes = Resume::count();
        $completedAnalyses = Resume::completed()->count();
        $pendingAnalyses = Resume::query()->where('analysis_status', '!=', 'completed')->count();
        $activeJobRoles = JobRole::active()->count();
        $recentResumes = Resume::query()
            ->with(['user', 'jobRole', 'resumeScore'])
            ->latest('uploaded_at')
            ->take(5)
            ->get();

        return [
            'totalUsers' => $totalUsers,
            'totalCandidates' => $totalCandidates,
            'totalAdmins' => $totalAdmins,
            'totalResumes' => $totalResumes,
            'completedAnalyses' => $completedAnalyses,
            'pendingAnalyses' => $pendingAnalyses,
            'activeJobRoles' => $activeJobRoles,
            'recentResumes' => $recentResumes,
            'total_users' => $totalUsers,
            'total_candidates' => $totalCandidates,
            'total_admins' => $totalAdmins,
            'total_resumes' => $totalResumes,
            'completed_analyses' => $completedAnalyses,
            'pending_analyses' => $pendingAnalyses,
            'active_job_roles' => $activeJobRoles,
            'recent_resumes' => $recentResumes->map(function (Resume $resume): array {
                return [
                    'candidate_name' => $resume->user?->name ?? 'Unknown User',
                    'file_name' => $resume->file_name,
                    'analysis_status_label' => str($resume->analysis_status)->replace('_', ' ')->title()->toString(),
                    'analysis_status_badge_class' => $resume->analysis_status_badge_class,
                    'total_score' => $resume->resumeScore?->total_score ?? '-',
                ];
            })->values()->all(),
            'last_updated' => now()->format('d M Y, h:i:s A'),
        ];
    }
}

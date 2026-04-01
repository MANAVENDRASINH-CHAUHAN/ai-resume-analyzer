<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\View\View;

class ReportManagementController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $reports = Resume::query()
            ->with(['user', 'jobRole', 'resumeScore', 'analysisReport'])
            ->where('analysis_status', 'completed')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('file_name', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('jobRole', function ($jobRoleQuery) use ($search) {
                            $jobRoleQuery->where('title', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('uploaded_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Resume $resume): View
    {
        $resume->load([
            'user',
            'jobRole',
            'resumeScore',
            'analysisReport',
            'extractedResumeData',
            'resumeSkillMaps.skill',
        ]);

        $detectedSkillMaps = $resume->resumeSkillMaps->where('matched_type', 'detected')->filter(fn ($item) => $item->skill !== null)->values();
        $matchedSkillMaps = $resume->resumeSkillMaps->where('matched_type', 'matched')->filter(fn ($item) => $item->skill !== null)->values();
        $missingSkillMaps = $resume->resumeSkillMaps->where('matched_type', 'missing')->filter(fn ($item) => $item->skill !== null)->values();
        $extraSkillMaps = $resume->resumeSkillMaps->where('matched_type', 'extra')->filter(fn ($item) => $item->skill !== null)->values();

        return view('admin.reports.show', [
            'resume' => $resume,
            'report' => $resume->analysisReport,
            'score' => $resume->resumeScore,
            'detectedSkillMaps' => $detectedSkillMaps,
            'matchedSkillMaps' => $matchedSkillMaps,
            'missingSkillMaps' => $missingSkillMaps,
            'extraSkillMaps' => $extraSkillMaps,
            'requiredSkills' => $resume->jobRole?->required_skills_list ?? [],
            'recommendationLabel' => $resume->resumeScore?->recommendation_label ?? 'Needs Improvement',
            'recommendationBadgeClass' => $resume->resumeScore?->recommendation_badge_class ?? 'text-bg-danger',
            'jobMatchPercentage' => $resume->resumeScore?->job_match_percentage ?? 0,
        ]);
    }
}

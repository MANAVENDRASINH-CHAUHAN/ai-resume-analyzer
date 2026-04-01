<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Resume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeManagementController extends Controller
{
    public function index(Request $request): View
    {
        $resumes = Resume::query()
            ->with(['user', 'jobRole', 'resumeScore'])
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
            ->when($request->filled('analysis_status'), function ($query) use ($request) {
                $query->where('analysis_status', $request->string('analysis_status')->toString());
            })
            ->orderByDesc('uploaded_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.resumes.index', compact('resumes'));
    }

    public function show(Resume $resume): View
    {
        return view('admin.resumes.show', [
            'resume' => $resume->load([
                'user',
                'jobRole',
                'extractedResumeData',
                'resumeScore',
                'analysisReport',
                'resumeSkillMaps.skill',
            ]),
        ]);
    }

    public function download(Resume $resume): StreamedResponse
    {
        abort_unless($resume->file_path && Storage::disk('public')->exists($resume->file_path), 404);

        return Storage::disk('public')->download($resume->file_path, $resume->file_name);
    }

    public function updateStatus(Request $request, Resume $resume): RedirectResponse
    {
        $validated = $request->validate([
            'upload_status' => ['required', Rule::in(['uploaded', 'processing', 'analyzed', 'failed'])],
            'analysis_status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'error'])],
            'progress_percent' => ['required', 'integer', 'between:0,100'],
        ]);

        $resume->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'resume_id' => $resume->id,
            'activity_type' => 'admin_resume_status_update',
            'activity_message' => 'Admin updated resume status for ' . $resume->file_name . '.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.resumes.show', $resume)
            ->with('success', 'Resume status updated successfully.');
    }

    public function destroy(Request $request, Resume $resume): RedirectResponse
    {
        if ($resume->file_path && Storage::disk('public')->exists($resume->file_path)) {
            Storage::disk('public')->delete($resume->file_path);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'resume_id' => $resume->id,
            'activity_type' => 'admin_resume_delete',
            'activity_message' => 'Admin deleted resume ' . $resume->file_name . '.',
            'ip_address' => $request->ip(),
        ]);

        $resume->delete();

        return redirect()
            ->route('admin.resumes.index')
            ->with('success', 'Resume deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JobRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobRoleController extends Controller
{
    public function index(Request $request): View
    {
        $jobRoles = JobRole::query()
            ->withCount('resumes')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.job_roles.index', compact('jobRoles'));
    }

    public function create(): View
    {
        return view('admin.job_roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateJobRole($request);
        $jobRole = JobRole::create($data);

        $this->logActivity($request, 'admin_job_role_create', 'Admin created job role ' . $jobRole->title . '.');

        return redirect()
            ->route('admin.job-roles.index')
            ->with('success', 'Job role created successfully.');
    }

    public function edit(JobRole $jobRole): View
    {
        return view('admin.job_roles.edit', compact('jobRole'));
    }

    public function update(Request $request, JobRole $jobRole): RedirectResponse
    {
        $data = $this->validateJobRole($request, $jobRole);
        $jobRole->update($data);

        $this->logActivity($request, 'admin_job_role_update', 'Admin updated job role ' . $jobRole->title . '.');

        return redirect()
            ->route('admin.job-roles.index')
            ->with('success', 'Job role updated successfully.');
    }

    public function destroy(Request $request, JobRole $jobRole): RedirectResponse
    {
        $title = $jobRole->title;
        $jobRole->delete();

        $this->logActivity($request, 'admin_job_role_delete', 'Admin deleted job role ' . $title . '.');

        return redirect()
            ->route('admin.job-roles.index')
            ->with('success', 'Job role deleted successfully.');
    }

    protected function validateJobRole(Request $request, ?JobRole $jobRole = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100', Rule::unique('job_roles', 'title')->ignore($jobRole?->id)],
            'description' => ['nullable', 'string'],
            'required_skills_text' => ['nullable', 'string'],
            'preferred_experience' => ['nullable', 'string', 'max:100'],
            'min_score' => ['required', 'integer', 'between:0,100'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'required_skills' => $this->convertSkillsTextToArray($validated['required_skills_text'] ?? ''),
            'preferred_experience' => $validated['preferred_experience'] ?? null,
            'min_score' => $validated['min_score'],
            'status' => $validated['status'],
        ];
    }

    protected function convertSkillsTextToArray(string $skillsText): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $skillsText))));
    }

    protected function logActivity(Request $request, string $type, string $message): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'resume_id' => null,
            'activity_type' => $type,
            'activity_message' => $message,
            'ip_address' => $request->ip(),
        ]);
    }
}

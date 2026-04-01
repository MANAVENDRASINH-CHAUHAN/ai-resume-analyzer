@extends('layouts.app')

@section('title', 'Manage Job Roles | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Manage Job Roles',
        'subtitle' => 'Create, edit, activate, or delete job roles used for resume analysis.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Job Roles'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <form method="GET" action="{{ route('admin.job-roles.index') }}" class="row g-3 flex-grow-1">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by title or description">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            <a href="{{ route('admin.job-roles.create') }}" class="btn btn-outline-dark rounded-pill px-4">Add Job Role</a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Required Skills</th>
                        <th>Preferred Experience</th>
                        <th>Min Score</th>
                        <th>Status</th>
                        <th>Resumes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobRoles as $jobRole)
                        <tr>
                            <td>{{ $jobRole->title }}</td>
                            <td>
                                @forelse ($jobRole->required_skills_list as $skill)
                                    <span class="skill-pill">{{ $skill }}</span>
                                @empty
                                    <span class="text-muted">No skills</span>
                                @endforelse
                            </td>
                            <td>{{ $jobRole->preferred_experience ?: '-' }}</td>
                            <td>{{ $jobRole->min_score }}</td>
                            <td><span class="badge rounded-pill {{ $jobRole->status_badge_class }}">{{ ucfirst($jobRole->status) }}</span></td>
                            <td>{{ $jobRole->resumes_count }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.job-roles.edit', $jobRole) }}" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a>
                                    <form action="{{ route('admin.job-roles.destroy', $jobRole) }}" method="POST" onsubmit="return confirm('Delete this job role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No job roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($jobRoles->hasPages())
            <div class="mt-4">
                {{ $jobRoles->links() }}
            </div>
        @endif
    </div>
@endsection

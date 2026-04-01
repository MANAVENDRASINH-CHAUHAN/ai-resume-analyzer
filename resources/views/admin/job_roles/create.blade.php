@extends('layouts.app')

@section('title', 'Create Job Role | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Create Job Role',
        'subtitle' => 'Add a new target role for resume matching and scoring.',
        'badge' => 'Admin Form',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Job Roles', 'url' => route('admin.job-roles.index')],
            ['label' => 'Create'],
        ],
    ])

    <div class="dashboard-card">
        <form action="{{ route('admin.job-roles.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Preferred Experience</label>
                    <input type="text" name="preferred_experience" class="form-control" value="{{ old('preferred_experience') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Min Score</label>
                    <input type="number" name="min_score" class="form-control" value="{{ old('min_score', 60) }}" min="0" max="100" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Required Skills</label>
                    <textarea name="required_skills_text" rows="3" class="form-control" placeholder="Example: PHP, Laravel, MySQL, API, Git">{{ old('required_skills_text') }}</textarea>
                    <div class="form-text">Enter skills separated by commas.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Create Job Role</button>
                <a href="{{ route('admin.job-roles.index') }}" class="btn btn-outline-dark rounded-pill px-4">Back</a>
            </div>
        </form>
    </div>
@endsection

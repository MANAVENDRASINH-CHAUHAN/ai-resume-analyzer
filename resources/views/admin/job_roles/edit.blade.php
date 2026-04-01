@extends('layouts.app')

@section('title', 'Edit Job Role | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Edit Job Role',
        'subtitle' => 'Update job-role details used for analysis and skill matching.',
        'badge' => 'Admin Form',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Job Roles', 'url' => route('admin.job-roles.index')],
            ['label' => 'Edit'],
        ],
    ])

    <div class="dashboard-card">
        <form action="{{ route('admin.job-roles.update', $jobRole) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $jobRole->title) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Preferred Experience</label>
                    <input type="text" name="preferred_experience" class="form-control" value="{{ old('preferred_experience', $jobRole->preferred_experience) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Min Score</label>
                    <input type="number" name="min_score" class="form-control" value="{{ old('min_score', $jobRole->min_score) }}" min="0" max="100" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $jobRole->description) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Required Skills</label>
                    <textarea name="required_skills_text" rows="3" class="form-control">{{ old('required_skills_text', implode(', ', $jobRole->required_skills_list)) }}</textarea>
                    <div class="form-text">Enter skills separated by commas.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" @selected(old('status', $jobRole->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $jobRole->status) === 'inactive')>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Update Job Role</button>
                <a href="{{ route('admin.job-roles.index') }}" class="btn btn-outline-dark rounded-pill px-4">Back</a>
            </div>
        </form>
    </div>
@endsection

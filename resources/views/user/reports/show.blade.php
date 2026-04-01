@extends('layouts.app')

@section('title', 'Analysis Report | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Analysis Report',
        'subtitle' => 'Detailed result view for the selected analyzed resume.',
        'badge' => 'Completed Report',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'Resume Reports', 'url' => route('user.reports.index')],
            ['label' => 'Analysis Report'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1">{{ $resume->file_name }}</h5>
                <p class="text-muted mb-0">Professional result report with score summary, skills, and improvement guidance.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('user.reports.print', $resume) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4">Print Report</a>
                <a href="{{ route('user.resumes.show', $resume) }}" class="btn btn-outline-primary rounded-pill px-4">Resume Details</a>
            </div>
        </div>
    </div>

    @include('user.reports.partials.report-content')
@endsection

@extends('layouts.app')

@section('title', 'Admin Report View | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Report Details',
        'subtitle' => 'Admin review of candidate analysis result, extracted data, and scoring output.',
        'badge' => 'Admin Report View',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Reports', 'url' => route('admin.reports.index')],
            ['label' => 'Report Details'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1">{{ $resume->file_name }}</h5>
                <p class="text-muted mb-0">Candidate: {{ $resume->user?->name ?? 'Unknown User' }} | Job Role: {{ $resume->jobRole?->title ?? 'Not Selected' }}</p>
            </div>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4" onclick="window.print()">Print Report</button>
        </div>
    </div>

    @include('user.reports.partials.report-content')
@endsection

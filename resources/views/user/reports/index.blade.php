@extends('layouts.app')

@section('title', 'Resume Reports | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Resume Reports',
        'subtitle' => 'View all completed analysis reports for your uploaded resumes.',
        'badge' => 'Candidate Reports',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'Resume Reports'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1">Completed Analysis History</h5>
                <p class="text-muted mb-0">Only resumes with completed analysis appear here. Each report is linked with score, skills, and extracted details.</p>
            </div>
            <a href="{{ route('user.resumes.index') }}" class="btn btn-outline-dark rounded-pill px-4">Go to Resume History</a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Resume File</th>
                        <th>Target Job Role</th>
                        <th>Total Score</th>
                        <th>Analysis Status</th>
                        <th>Uploaded Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $resume)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $resume->file_name }}</div>
                                <div class="small text-muted text-uppercase">{{ $resume->file_type }}</div>
                            </td>
                            <td>{{ $resume->jobRole?->title ?? 'Not Selected' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $resume->resumeScore?->recommendation_badge_class ?? 'text-bg-secondary' }}">
                                    {{ $resume->resumeScore?->total_score ?? 0 }}/100
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}">
                                    {{ ucfirst($resume->analysis_status) }}
                                </span>
                            </td>
                            <td>{{ optional($resume->uploaded_at)->format('d M Y, h:i A') ?? '-' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('user.reports.show', $resume) }}" class="btn btn-sm btn-outline-primary rounded-pill">View Report</a>
                                    <a href="{{ route('user.reports.print', $resume) }}" class="btn btn-sm btn-outline-dark rounded-pill" target="_blank">Print Report</a>
                                    <a href="{{ route('user.resumes.show', $resume) }}" class="btn btn-sm btn-outline-success rounded-pill">Resume Details</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <div class="empty-state-box">
                                    <div class="mb-2 fs-3 text-primary"><i class="bi bi-file-earmark-bar-graph"></i></div>
                                    <div class="fw-semibold mb-1">No completed reports available</div>
                                    <div class="text-muted mb-3">Upload and analyze a resume first to generate a full report.</div>
                                    <a href="{{ route('user.resumes.index') }}" class="btn btn-primary rounded-pill px-4">Open Resume History</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
@endsection

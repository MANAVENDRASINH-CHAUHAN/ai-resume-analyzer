@extends('layouts.app')

@section('title', 'Welcome ' . ($user->name ?? 'User'))

@section('content')
    <div data-candidate-dashboard data-stats-url="{{ route('user.dashboard.stats') }}">
    @include('partials.page-header', [
        'title' => 'Welcome ' . ($user->name ?? 'User'),
        'subtitle' => 'Manage your resumes, follow live analysis progress, and access detailed reports from one clean workspace.',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h4 class="fw-semibold mb-2">Welcome to your analysis workspace</h4>
                <p class="text-muted mb-0">
                    Upload your resumes, review analysis reports, and compare your profile against target job roles.
                    This dashboard is designed to stay simple, clean, and easy to extend in later phases.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('user.resumes.create') }}" class="btn btn-primary rounded-pill px-4 me-2 mb-2 mb-lg-0">Upload Resume</a>
                <a href="{{ route('user.resumes.index') }}" class="btn btn-outline-dark rounded-pill px-4">My Resumes</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-file-earmark-arrow-up"></i></div>
                <div class="stat-label">Total Resumes Uploaded</div>
                <div class="stat-value" data-dashboard-stat="total_resumes">{{ $totalResumes ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></div>
                <div class="stat-label">Completed Analyses</div>
                <div class="stat-value" data-dashboard-stat="completed_analyses">{{ $completedAnalyses ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-label">Pending Analyses</div>
                <div class="stat-value" data-dashboard-stat="pending_analyses">{{ $pendingAnalyses ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-bar-chart-line"></i></div>
                <div class="stat-label">Average Score</div>
                <div class="stat-value" data-dashboard-stat="average_score">{{ $averageScore ?? 0 }}%</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="card-title fw-semibold mb-0">Recent Resumes</h5>
                        <small class="text-muted" data-dashboard-last-updated>Last updated: {{ now()->format('d M Y, h:i:s A') }}</small>
                    </div>
                    <a href="{{ route('user.resumes.index') }}" class="badge text-bg-light border text-decoration-none">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table dashboard-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Resume File</th>
                                <th>Job Role</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Uploaded</th>
                            </tr>
                        </thead>
                        <tbody data-dashboard-recent-resumes>
                            @forelse (($recentResumes ?? collect()) as $resume)
                                <tr>
                                    <td>
                                        <a href="{{ route('user.resumes.show', $resume) }}" class="fw-semibold text-dark">
                                            {{ $resume->file_name }}
                                        </a>
                                    </td>
                                    <td>{{ $resume->jobRole?->title ?? 'Not Selected' }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}">
                                            {{ str($resume->analysis_status ?? 'pending')->replace('_', ' ')->title() }}
                                        </span>
                                    </td>
                                    <td>{{ $resume->resumeScore->total_score ?? 0 }}%</td>
                                    <td>{{ optional($resume->uploaded_at)->format('d M Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No resumes uploaded yet. Use the upload button to get started.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <h5 class="card-title fw-semibold mb-3">Quick Actions</h5>
                <div class="d-grid gap-3">
                    <a href="{{ route('user.resumes.create') }}" class="quick-action-card">
                        <div class="quick-action-icon"><i class="bi bi-upload"></i></div>
                        <div>
                            <div class="fw-semibold">Upload Resume</div>
                            <small class="text-muted">Start a new analysis flow</small>
                        </div>
                    </a>
                    <a href="{{ route('user.resumes.index') }}" class="quick-action-card">
                        <div class="quick-action-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <div class="fw-semibold">My Resume History</div>
                            <small class="text-muted">Review uploaded resume records</small>
                        </div>
                    </a>
                    <a href="{{ route('user.reports.index') }}" class="quick-action-card">
                        <div class="quick-action-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                        <div>
                            <div class="fw-semibold">View Reports</div>
                            <small class="text-muted">See report and score history</small>
                        </div>
                    </a>
                    <a href="{{ route('user.profile') }}" class="quick-action-card">
                        <div class="quick-action-icon"><i class="bi bi-person-circle"></i></div>
                        <div>
                            <div class="fw-semibold">Profile</div>
                            <small class="text-muted">Review your account details</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

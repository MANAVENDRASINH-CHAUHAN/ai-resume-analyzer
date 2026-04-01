@extends('layouts.app')

@section('title', 'Admin Dashboard | AI Resume Analyzer System')

@section('content')
    <div data-admin-dashboard data-stats-url="{{ route('admin.dashboard.stats') }}">
    @include('partials.page-header', [
        'title' => 'Admin Dashboard',
        'subtitle' => 'Central analytics overview for resumes, reports, and job roles.',
        'badge' => 'Administrator Access',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Admin Dashboard'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1">Project Administration Panel</h5>
                <p class="text-muted mb-0">Use the admin sidebar to manage job roles, resumes, and analysis reports.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.job-roles.index') }}" class="btn btn-outline-dark rounded-pill px-4">Manage Job Roles</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-label">Total Users</div>
                <div class="stat-value" data-dashboard-stat="total_users">{{ $totalUsers ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-label">Candidates</div>
                <div class="stat-value" data-dashboard-stat="total_candidates">{{ $totalCandidates ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-label">Admins</div>
                <div class="stat-value" data-dashboard-stat="total_admins">{{ $totalAdmins ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-label">Active Job Roles</div>
                <div class="stat-value" data-dashboard-stat="active_job_roles">{{ $activeJobRoles ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="stat-card h-100">
                <div class="stat-label">Total Resumes</div>
                <div class="stat-value" data-dashboard-stat="total_resumes">{{ $totalResumes ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="stat-card h-100">
                <div class="stat-label">Completed Analyses</div>
                <div class="stat-value" data-dashboard-stat="completed_analyses">{{ $completedAnalyses ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4">
            <div class="stat-card h-100">
                <div class="stat-label">Pending Analyses</div>
                <div class="stat-value" data-dashboard-stat="pending_analyses">{{ $pendingAnalyses ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="dashboard-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-semibold mb-0">Recent Resumes</h5>
                        <small class="text-muted" data-dashboard-last-updated>Last updated: {{ now()->format('d M Y, h:i:s A') }}</small>
                    </div>
                    <a href="{{ route('admin.resumes.index') }}" class="btn btn-sm btn-outline-dark rounded-pill">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table dashboard-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody data-dashboard-recent-resumes>
                            @forelse ($recentResumes as $resume)
                                <tr>
                                    <td>{{ $resume->user?->name ?? 'Unknown User' }}</td>
                                    <td>{{ $resume->file_name }}</td>
                                    <td><span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}">{{ ucfirst($resume->analysis_status) }}</span></td>
                                    <td>{{ $resume->resumeScore?->total_score ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No resumes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

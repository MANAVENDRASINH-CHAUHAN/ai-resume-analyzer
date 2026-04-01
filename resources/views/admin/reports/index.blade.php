@extends('layouts.app')

@section('title', 'Analysis Reports | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Analysis Reports',
        'subtitle' => 'Search and review completed analysis reports across all users.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Reports'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by file name, candidate, email, or job role">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Candidate</th>
                        <th>Job Role</th>
                        <th>Total Score</th>
                        <th>Recommendation</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $resume)
                        <tr>
                            <td>{{ $resume->file_name }}</td>
                            <td>{{ $resume->user?->name ?? 'Unknown User' }}</td>
                            <td>{{ $resume->jobRole?->title ?? 'Not Selected' }}</td>
                            <td>{{ $resume->resumeScore?->total_score ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $resume->resumeScore?->recommendation_badge_class ?? 'text-bg-secondary' }}">
                                    {{ $resume->resumeScore?->recommendation_label ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ optional($resume->uploaded_at)->format('d M Y') ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.reports.show', $resume) }}" class="btn btn-sm btn-outline-primary rounded-pill">View Report</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No reports found.</td>
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

@extends('layouts.app')

@section('title', 'Manage Resumes | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Manage Resumes',
        'subtitle' => 'Search and monitor all uploaded resumes across the system.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Resumes'],
        ],
    ])

    <div class="dashboard-card mb-4">
        <form method="GET" action="{{ route('admin.resumes.index') }}">
            <div class="row g-3">
                <div class="col-md-7">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by file name, user, or job role">
                </div>
                <div class="col-md-3">
                    <select name="analysis_status" class="form-select">
                        <option value="">All Analysis Status</option>
                        <option value="pending" @selected(request('analysis_status') === 'pending')>Pending</option>
                        <option value="in_progress" @selected(request('analysis_status') === 'in_progress')>In Progress</option>
                        <option value="completed" @selected(request('analysis_status') === 'completed')>Completed</option>
                        <option value="error" @selected(request('analysis_status') === 'error')>Error</option>
                    </select>
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
                        <th>Upload Status</th>
                        <th>Analysis Status</th>
                        <th>Score</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resumes as $resume)
                        <tr>
                            <td>{{ $resume->file_name }}</td>
                            <td>{{ $resume->user?->name ?? 'Unknown User' }}</td>
                            <td>{{ $resume->jobRole?->title ?? 'Not Selected' }}</td>
                            <td><span class="badge rounded-pill {{ $resume->upload_status_badge_class }}">{{ ucfirst($resume->upload_status) }}</span></td>
                            <td><span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}">{{ ucfirst($resume->analysis_status) }}</span></td>
                            <td>{{ $resume->resumeScore?->total_score ?? '-' }}</td>
                            <td>{{ optional($resume->uploaded_at)->format('d M Y') ?? '-' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.resumes.show', $resume) }}" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                                    <a href="{{ route('admin.resumes.download', $resume) }}" class="btn btn-sm btn-outline-dark rounded-pill">Download</a>
                                    <form action="{{ route('admin.resumes.destroy', $resume) }}" method="POST" onsubmit="return confirm('Delete this resume?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No resumes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($resumes->hasPages())
            <div class="mt-4">
                {{ $resumes->links() }}
            </div>
        @endif
    </div>
@endsection

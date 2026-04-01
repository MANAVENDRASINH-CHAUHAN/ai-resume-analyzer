@extends('layouts.app')

@section('title', 'My Resume History | AI Resume Analyzer System')

@section('content')
    <div data-resume-list data-status-list-url="{{ route('user.resumes.status-list') }}">
    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1">Uploaded Resume List</h5>
                <p class="text-muted mb-0">Each uploaded file is stored safely in Laravel public storage and now supports live AJAX status refresh.</p>
            </div>
            <div class="text-end">
                <a href="{{ route('user.resumes.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-circle me-2"></i>Upload New Resume
                </a>
                <div class="small text-muted mt-2" data-resume-list-last-updated>Last updated: {{ now()->format('d M Y, h:i:s A') }}</div>
            </div>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm d-none" data-resume-list-feedback></div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Job Role</th>
                        <th>Upload Date</th>
                        <th>Upload Status</th>
                        <th>Analysis Status</th>
                        <th>Total Score</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resumes as $resume)
                        <tr data-resume-row data-resume-id="{{ $resume->id }}">
                            <td>
                                <div class="fw-semibold">{{ $resume->file_name }}</div>
                                <div class="small text-muted text-uppercase">{{ $resume->file_type }}</div>
                            </td>
                            <td>{{ $resume->jobRole?->title ?? 'Not Selected' }}</td>
                            <td>{{ optional($resume->uploaded_at)->format('d M Y, h:i A') ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $resume->upload_status_badge_class }}" data-upload-status-badge>
                                    {{ str($resume->upload_status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}" data-analysis-status-badge>
                                    {{ str($resume->analysis_status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td data-total-score>{{ $resume->resumeScore?->total_score ?? '-' }}</td>
                            <td>
                                <div class="small fw-medium mb-1" data-progress-label>{{ $resume->progress_percent }}%</div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" data-progress-bar role="progressbar" style="width: {{ $resume->progress_percent }}%;" aria-valuenow="{{ $resume->progress_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('user.resumes.show', $resume) }}" class="btn btn-sm btn-outline-primary rounded-pill">View</a>

                                    @if ($resume->download_url)
                                        <a href="{{ $resume->download_url }}" class="btn btn-sm btn-outline-dark rounded-pill">Download</a>
                                    @endif

                                    <div data-report-actions class="{{ $resume->analysis_status === 'completed' ? '' : 'd-none' }}">
                                        <a href="{{ route('user.reports.show', $resume) }}" class="btn btn-sm btn-outline-success rounded-pill" data-view-report-link>View Report</a>
                                        <a href="{{ route('user.reports.print', $resume) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill" data-print-report-link>Print</a>
                                    </div>

                                    <form action="{{ route('user.resumes.analyze', $resume) }}" method="POST" data-analyze-form class="{{ $resume->analysis_status === 'completed' ? 'd-none' : '' }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill" data-analyze-button>Analyze</button>
                                    </form>

                                    <form action="{{ route('user.resumes.destroy', $resume) }}" method="POST" onsubmit="return confirm('Delete this resume record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center">
                                <div class="empty-state-box">
                                    <div class="mb-2 fs-3 text-primary"><i class="bi bi-folder2-open"></i></div>
                                    <div class="fw-semibold mb-1">No resumes uploaded yet</div>
                                    <div class="text-muted mb-3">Start by uploading your first resume.</div>
                                    <a href="{{ route('user.resumes.create') }}" class="btn btn-primary rounded-pill px-4">Upload Resume</a>
                                </div>
                            </td>
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
    </div>
@endsection

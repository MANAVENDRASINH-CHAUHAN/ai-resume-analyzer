@extends('layouts.app')

@section('title', 'Resume Details | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => 'Resume Details',
        'subtitle' => 'Admin review page for uploaded resume metadata, extracted data, and analysis state.',
        'badge' => 'Admin Resume View',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Resumes', 'url' => route('admin.resumes.index')],
            ['label' => 'Resume Details'],
        ],
    ])

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h4 class="fw-semibold mb-1">{{ $resume->file_name }}</h4>
                        <p class="text-muted mb-0">Candidate: {{ $resume->user?->name ?? 'Unknown User' }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.resumes.download', $resume) }}" class="btn btn-primary rounded-pill px-4">Download</a>
                        @if ($resume->analysis_status === 'completed')
                            <a href="{{ route('admin.reports.show', $resume) }}" class="btn btn-outline-success rounded-pill px-4">Open Report</a>
                        @endif
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Job Role</span>
                            <span class="detail-value">{{ $resume->jobRole?->title ?? 'Not Selected' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">File Size</span>
                            <span class="detail-value">{{ $resume->formatted_file_size }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Upload Status</span>
                            <span class="detail-value"><span class="badge rounded-pill {{ $resume->upload_status_badge_class }}">{{ ucfirst($resume->upload_status) }}</span></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Analysis Status</span>
                            <span class="detail-value"><span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}">{{ ucfirst($resume->analysis_status) }}</span></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Progress</span>
                            <span class="detail-value">{{ $resume->progress_percent }}%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Total Score</span>
                            <span class="detail-value">{{ $resume->resumeScore?->total_score ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-semibold mb-2">Extracted Summary</h6>
                    <p class="text-muted mb-0">{{ $resume->extractedResumeData?->summary ?? 'No summary available.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <h5 class="fw-semibold mb-3">Update Status</h5>
                <form action="{{ route('admin.resumes.update-status', $resume) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Upload Status</label>
                        <select name="upload_status" class="form-select" required>
                            @foreach (['uploaded', 'processing', 'analyzed', 'failed'] as $status)
                                <option value="{{ $status }}" @selected(old('upload_status', $resume->upload_status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Analysis Status</label>
                        <select name="analysis_status" class="form-select" required>
                            @foreach (['pending', 'in_progress', 'completed', 'error'] as $status)
                                <option value="{{ $status }}" @selected(old('analysis_status', $resume->analysis_status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Progress Percent</label>
                        <input type="number" name="progress_percent" class="form-control" value="{{ old('progress_percent', $resume->progress_percent) }}" min="0" max="100" required>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill w-100">Update Resume Status</button>
                </form>
            </div>
        </div>
    </div>
@endsection

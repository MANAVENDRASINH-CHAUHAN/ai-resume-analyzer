@extends('layouts.app')

@section('title', 'Resume Details | AI Resume Analyzer System')

@section('content')
    <div
        data-resume-detail
        data-status-url="{{ route('user.resumes.status', $resume) }}"
        data-resume-id="{{ $resume->id }}"
        data-current-status="{{ $resume->analysis_status }}"
    >
    @include('partials.page-header', [
        'title' => 'Resume Details',
        'subtitle' => 'This page shows the metadata of the uploaded resume and its current upload and analysis state.',
        'badge' => 'Resume Record',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'My Resumes', 'url' => route('user.resumes.index')],
            ['label' => 'Resume Details'],
        ],
    ])

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="alert alert-info border-0 shadow-sm d-none mb-4" data-analysis-feedback></div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h4 class="fw-semibold mb-1">{{ $resume->file_name }}</h4>
                        <p class="text-muted mb-0">Uploaded for {{ $resume->jobRole?->title ?? 'No Job Role Selected' }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($resume->download_url)
                            <a href="{{ $resume->download_url }}" class="btn btn-primary rounded-pill px-4">Download Resume</a>
                        @endif
                        <div data-report-actions class="d-flex flex-wrap gap-2 {{ $resume->analysis_status === 'completed' ? '' : 'd-none' }}">
                            <a href="{{ route('user.reports.show', $resume) }}" class="btn btn-outline-success rounded-pill px-4" data-view-report-link>View Full Report</a>
                            <a href="{{ route('user.reports.print', $resume) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-4" data-print-report-link>Print Report</a>
                        </div>
                        <a href="{{ route('user.resumes.index') }}" class="btn btn-outline-dark rounded-pill px-4">Back</a>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">File Name</span>
                            <span class="detail-value">{{ $resume->file_name }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">File Type</span>
                            <span class="detail-value text-uppercase">{{ $resume->file_type }}</span>
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
                            <span class="detail-label">Target Job Role</span>
                            <span class="detail-value">{{ $resume->jobRole?->title ?? 'Not Selected' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Upload Status</span>
                            <span class="detail-value">
                                <span class="badge rounded-pill {{ $resume->upload_status_badge_class }}" data-upload-status-badge>
                                    {{ str($resume->upload_status)->replace('_', ' ')->title() }}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Analysis Status</span>
                            <span class="detail-value">
                                <span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}" data-analysis-status-badge>
                                    {{ str($resume->analysis_status)->replace('_', ' ')->title() }}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Uploaded Date</span>
                            <span class="detail-value">{{ optional($resume->uploaded_at)->format('d M Y, h:i A') ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Progress</span>
                            <span class="detail-value" data-progress-label>{{ $resume->progress_percent }}%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-medium">Current Progress</span>
                        <span class="small text-muted" data-last-updated>Last updated: {{ now()->format('d M Y, h:i:s A') }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" data-progress-bar role="progressbar" style="width: {{ $resume->progress_percent }}%;" aria-valuenow="{{ $resume->progress_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <h5 class="fw-semibold mb-3">Analyze Resume</h5>
                <p class="text-muted small mb-3">
                    Click analyze to run the rule-based parser. If the file text is difficult to read, you can paste resume text below as a fallback.
                </p>

                <form action="{{ route('user.resumes.analyze', $resume) }}" method="POST" class="mb-4" data-analyze-form>
                    @csrf
                    <div class="mb-3">
                        <label for="manual_text" class="form-label fw-medium">Fallback Resume Text (Optional)</label>
                        <textarea name="manual_text" id="manual_text" rows="8" class="form-control" placeholder="Paste resume text here if the uploaded file cannot be read properly...">{{ old('manual_text') }}</textarea>
                        <div class="form-text">This helps when some PDF or DOC files do not return readable text during extraction.</div>
                    </div>
                    <button type="submit" class="btn btn-success rounded-pill w-100" data-analyze-button>
                        {{ $resume->analysis_status === 'completed' ? 'Re-analyze Resume' : 'Analyze Resume' }}
                    </button>
                </form>

                <h6 class="fw-semibold mb-2">Status Summary</h6>
                <ul class="text-muted ps-3 mb-4">
                    <li>During analysis the system moves progress to 25%, 50%, 75%, and 100%.</li>
                    <li>Detected skills are compared with skills stored in the database.</li>
                    <li>Scores are calculated using transparent rule-based logic out of 100.</li>
                </ul>

                <form action="{{ route('user.resumes.destroy', $resume) }}" method="POST" onsubmit="return confirm('Delete this resume record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                        <i class="bi bi-trash me-2"></i>Delete Resume
                    </button>
                </form>
            </div>
        </div>
    </div>
    </div>

    @if ($resume->analysis_status === 'completed' && $resume->resumeScore)
        @php
            $detectedSkills = $resume->extractedResumeData?->extracted_skills ?? [];
            $missingSkills = $resume->resumeSkillMaps->where('matched_type', 'missing');
            $matchedSkills = $resume->resumeSkillMaps->where('matched_type', 'matched');
            $jobMatchPercent = ($resume->resumeScore->job_match_score ?? 0) * 10;
        @endphp

        <div class="row g-4 mt-1">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-label">Total Score</div>
                    <div class="stat-value">{{ $resume->resumeScore->total_score ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-label">Job Match</div>
                    <div class="stat-value">{{ $jobMatchPercent }}%</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-label">Matched Skills</div>
                    <div class="stat-value">{{ $matchedSkills->count() }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-label">Missing Skills</div>
                    <div class="stat-value">{{ $missingSkills->count() }}</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <h5 class="fw-semibold mb-3">Extracted Contact Details</h5>
                    <p class="mb-2"><strong>Name:</strong> {{ $resume->extractedResumeData?->full_name ?? '-' }}</p>
                    <p class="mb-2"><strong>Email:</strong> {{ $resume->extractedResumeData?->email ?? '-' }}</p>
                    <p class="mb-2"><strong>Phone:</strong> {{ $resume->extractedResumeData?->phone ?? '-' }}</p>
                    <p class="mb-0"><strong>Address:</strong> {{ $resume->extractedResumeData?->address ?? '-' }}</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <h5 class="fw-semibold mb-3">Detected Skills</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($detectedSkills as $skill)
                            <span class="skill-pill matched">{{ $skill }}</span>
                        @empty
                            <span class="text-muted">No skills detected.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <h5 class="fw-semibold mb-3">Score Breakdown</h5>
                    @php
                        $scoreItems = [
                            ['label' => 'Contact Score', 'value' => $resume->resumeScore->contact_score, 'max' => 10],
                            ['label' => 'Education Score', 'value' => $resume->resumeScore->education_score, 'max' => 15],
                            ['label' => 'Skills Score', 'value' => $resume->resumeScore->skills_score, 'max' => 25],
                            ['label' => 'Experience Score', 'value' => $resume->resumeScore->experience_score, 'max' => 20],
                            ['label' => 'Projects Score', 'value' => $resume->resumeScore->projects_score, 'max' => 10],
                            ['label' => 'Formatting Score', 'value' => $resume->resumeScore->formatting_score, 'max' => 10],
                            ['label' => 'Job Match Score', 'value' => $resume->resumeScore->job_match_score, 'max' => 10],
                        ];
                    @endphp

                    @foreach ($scoreItems as $item)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $item['label'] }}</span>
                                <span>{{ $item['value'] }}/{{ $item['max'] }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($item['value'] / $item['max']) * 100 }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <h5 class="fw-semibold mb-3">Missing Skills</h5>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @forelse ($missingSkills as $skillMap)
                            <span class="skill-pill missing">{{ $skillMap->skill?->skill_name }}</span>
                        @empty
                            <span class="text-muted">No missing skills detected.</span>
                        @endforelse
                    </div>

                    <h6 class="fw-semibold">Strengths</h6>
                    <p class="text-muted">{{ $resume->resumeScore->strengths ?: 'No strengths generated.' }}</p>

                    <h6 class="fw-semibold">Improvements</h6>
                    <p class="text-muted mb-0">{{ $resume->resumeScore->improvements ?: 'No improvements generated.' }}</p>
                </div>
            </div>
        </div>
    @endif
@endsection

@php
    $scoreItems = [
        ['label' => 'Contact Score', 'value' => $score?->contact_score ?? 0, 'max' => 10],
        ['label' => 'Education Score', 'value' => $score?->education_score ?? 0, 'max' => 15],
        ['label' => 'Skills Score', 'value' => $score?->skills_score ?? 0, 'max' => 25],
        ['label' => 'Experience Score', 'value' => $score?->experience_score ?? 0, 'max' => 20],
        ['label' => 'Projects Score', 'value' => $score?->projects_score ?? 0, 'max' => 10],
        ['label' => 'Formatting Score', 'value' => $score?->formatting_score ?? 0, 'max' => 10],
        ['label' => 'Job Match Score', 'value' => $score?->job_match_score ?? 0, 'max' => 10],
    ];
    $detectedSkills = $resume->extractedResumeData?->extracted_skills ?? [];
@endphp

<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="dashboard-card report-highlight-card h-100 text-center">
            <h5 class="fw-semibold mb-3">Total Score</h5>
            <div class="score-circle mx-auto" style="--score-angle: {{ ($score?->total_score ?? 0) * 3.6 }}deg;">
                <span>{{ $score?->total_score ?? 0 }}</span>
            </div>
            <div class="mt-3">
                <span class="badge rounded-pill px-3 py-2 {{ $recommendationBadgeClass }}">
                    {{ $recommendationLabel }}
                </span>
            </div>
            <p class="text-muted small mt-3 mb-0">Overall score calculated out of 100 based on rule-based resume analysis.</p>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Resume Information</h5>
            <div class="report-meta-list">
                <div class="report-meta-item">
                    <span class="report-meta-label">File Name</span>
                    <span class="report-meta-value">{{ $resume->file_name }}</span>
                </div>
                <div class="report-meta-item">
                    <span class="report-meta-label">File Type</span>
                    <span class="report-meta-value text-uppercase">{{ $resume->file_type }}</span>
                </div>
                <div class="report-meta-item">
                    <span class="report-meta-label">File Size</span>
                    <span class="report-meta-value">{{ $resume->formatted_file_size }}</span>
                </div>
                <div class="report-meta-item">
                    <span class="report-meta-label">Uploaded Date</span>
                    <span class="report-meta-value">{{ optional($resume->uploaded_at)->format('d M Y, h:i A') ?? '-' }}</span>
                </div>
                <div class="report-meta-item">
                    <span class="report-meta-label">Job Role</span>
                    <span class="report-meta-value">{{ $resume->jobRole?->title ?? 'Not Selected' }}</span>
                </div>
                <div class="report-meta-item">
                    <span class="report-meta-label">Analysis Status</span>
                    <span class="report-meta-value">
                        <span class="badge rounded-pill {{ $resume->analysis_status_badge_class }}">{{ ucfirst($resume->analysis_status) }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Job Match Summary</h5>
            <div class="mini-stat-card mb-3">
                <div class="mini-stat-label">Matched Skill Percentage</div>
                <div class="mini-stat-value">{{ $jobMatchPercentage }}%</div>
            </div>
            <div class="progress mb-3" style="height: 10px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $jobMatchPercentage }}%;" aria-valuenow="{{ $jobMatchPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="text-muted small mb-3">This percentage is based on how many required job-role skills were matched in the resume.</p>
            <div class="small text-muted">
                <strong>Report Title:</strong> {{ $report?->report_title ?? 'Resume Analysis Report' }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Score Breakdown</h5>
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
            <h5 class="fw-semibold mb-3">Extracted Resume Details</h5>
            <div class="report-text-block">
                <p><strong>Full Name:</strong> {{ $resume->extractedResumeData?->full_name ?? '-' }}</p>
                <p><strong>Email:</strong> {{ $resume->extractedResumeData?->email ?? '-' }}</p>
                <p><strong>Phone:</strong> {{ $resume->extractedResumeData?->phone ?? '-' }}</p>
                <p><strong>Address:</strong> {{ $resume->extractedResumeData?->address ?? '-' }}</p>
                <p class="mb-0"><strong>Summary:</strong> {{ $resume->extractedResumeData?->summary ?? 'No summary generated.' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Detected Skills</h5>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($detectedSkills as $skill)
                    <span class="skill-pill matched">{{ $skill }}</span>
                @empty
                    <span class="text-muted">No detected skills available.</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Required Skills</h5>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($requiredSkills as $requiredSkill)
                    <span class="skill-pill">{{ $requiredSkill }}</span>
                @empty
                    <span class="text-muted">No job-role skills defined.</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Missing Skills</h5>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($missingSkillMaps as $skillMap)
                    <span class="skill-pill missing">{{ $skillMap->skill?->skill_name }}</span>
                @empty
                    <span class="text-muted">No missing skills found.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Section Details</h5>
            <div class="report-text-block">
                <h6 class="fw-semibold">Education</h6>
                <p>{{ $resume->extractedResumeData?->education ?: 'Education section not found.' }}</p>

                <h6 class="fw-semibold">Experience</h6>
                <p>{{ $resume->extractedResumeData?->experience ?: 'Experience section not found.' }}</p>

                <h6 class="fw-semibold">Projects</h6>
                <p>{{ $resume->extractedResumeData?->projects ?: 'Projects section not found.' }}</p>

                <h6 class="fw-semibold">Certifications</h6>
                <p class="mb-0">{{ $resume->extractedResumeData?->certifications ?: 'Certifications section not found.' }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="dashboard-card h-100">
            <h5 class="fw-semibold mb-3">Feedback and Recommendation</h5>
            <div class="mb-3">
                <span class="badge rounded-pill px-3 py-2 {{ $recommendationBadgeClass }}">{{ $recommendationLabel }}</span>
            </div>

            <h6 class="fw-semibold">Strengths</h6>
            <p class="text-muted">{{ $score?->strengths ?: 'No strengths generated.' }}</p>

            <h6 class="fw-semibold">Improvements</h6>
            <p class="text-muted">{{ $score?->improvements ?: 'No improvements generated.' }}</p>

            <h6 class="fw-semibold">Feedback</h6>
            <p class="text-muted mb-0">{{ $score?->feedback ?: 'No feedback generated.' }}</p>
        </div>
    </div>
</div>

<div class="dashboard-card">
    <h5 class="fw-semibold mb-3">Saved Report Text</h5>
    <div class="report-text-block">
        @forelse (($report?->paragraphs ?? []) as $paragraph)
            <p>{{ $paragraph }}</p>
        @empty
            <p class="mb-0 text-muted">No saved report text found.</p>
        @endforelse
    </div>
</div>

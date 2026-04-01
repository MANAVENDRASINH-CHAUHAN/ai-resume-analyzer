@extends('layouts.app')

@section('title', 'Job Match Result')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Job Match Analysis</h2>
            <p class="text-muted mb-0">{{ $resume->title }} vs {{ $resume->jobRole?->title }}</p>
        </div>
        <a href="{{ route('user.resumes.show', $resume) }}" class="btn btn-outline-dark rounded-pill px-4">Back to Report</a>
    </div>

    <div class="report-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1">Job Match Percentage</h5>
                <p class="text-muted mb-0">Transparent skill comparison based on the selected role.</p>
            </div>
            <div class="display-6 fw-bold text-success">{{ $resume->score?->job_match_percentage ?? 0 }}%</div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="report-card h-100">
                <h5 class="fw-bold">Required Skills</h5>
                @foreach ($resume->jobRole?->skills ?? [] as $skill)
                    <span class="skill-pill">{{ $skill->name }}</span>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-card h-100">
                <h5 class="fw-bold">Matched Skills</h5>
                @forelse ($matchedSkills as $skillMap)
                    <span class="skill-pill matched">{{ $skillMap->skill?->name ?? $skillMap->notes }}</span>
                @empty
                    <p class="text-muted mb-0">No matched skills found.</p>
                @endforelse
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-card h-100">
                <h5 class="fw-bold">Missing Skills</h5>
                @forelse ($missingSkills as $skillMap)
                    <span class="skill-pill missing">{{ $skillMap->skill?->name ?? $skillMap->notes }}</span>
                @empty
                    <p class="text-muted mb-0">No missing skills found.</p>
                @endforelse
            </div>
        </div>
        <div class="col-12">
            <div class="report-card">
                <h5 class="fw-bold">Extra Skills in Resume</h5>
                @forelse ($extraSkills as $skillMap)
                    <span class="skill-pill extra">{{ $skillMap->skill?->name ?? $skillMap->notes }}</span>
                @empty
                    <p class="text-muted mb-0">No extra skills detected beyond the selected role.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

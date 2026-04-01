@extends('layouts.app')

@section('title', 'Home | AI Resume Analyzer System')

@section('content')
    <section class="hero-section mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-light text-dark rounded-pill mb-3">Advanced Web Technology Mini Project</span>
                <h1 class="display-5 fw-bold mb-3">Upload a resume, analyze it instantly, and match it to the right job role.</h1>
                <p class="lead mb-4">This Laravel-based system uses PHP, MySQL, Bootstrap, Blade, JavaScript, and AJAX polling to simulate live resume analysis inside a MAMP-friendly college project.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-4">Create User Account</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">User Login</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="soft-card text-dark">
                    <h5 class="fw-bold mb-3">Project Highlights</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card h-100">
                                <div class="stat-label">Job Roles</div>
                                <div class="stat-value">{{ $stats['job_roles'] }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card h-100">
                                <div class="stat-label">Skills</div>
                                <div class="stat-value">{{ $stats['skills'] }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-card">
                                <div class="stat-label">Completed Reports</div>
                                <div class="stat-value">{{ $stats['completed_reports'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="soft-card h-100">
                    <h5 class="fw-bold">Rule-Based Resume Parsing</h5>
                    <p class="text-muted mb-0">Detects name, email, phone, education, skills, experience, and projects using regex, arrays, and string handling.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="soft-card h-100">
                    <h5 class="fw-bold">AJAX Live Status Simulation</h5>
                    <p class="text-muted mb-0">Shows Uploaded, Parsing, Analyzing, and Completed progress using JavaScript polling and Laravel JSON endpoints.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="soft-card h-100">
                    <h5 class="fw-bold">Admin Analytics Dashboard</h5>
                    <p class="text-muted mb-0">Includes user management, job-role CRUD, report viewing, missing-skill insights, and activity logs.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title mb-1">Sample Job Roles</h2>
                <p class="text-muted mb-0">Preloaded roles help students test the analyzer immediately after setup.</p>
            </div>
            <a href="{{ route('admin.login') }}" class="btn btn-outline-dark rounded-pill">Admin Login</a>
        </div>

        <div class="row g-4">
            @foreach ($jobRoles as $jobRole)
                <div class="col-md-6 col-xl-4">
                    <div class="soft-card h-100">
                        <h5 class="fw-bold">{{ $jobRole->title }}</h5>
                        <p class="text-muted">{{ $jobRole->description }}</p>
                        <div>
                            @foreach ($jobRole->required_skills ?? [] as $skill)
                                <span class="skill-pill">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

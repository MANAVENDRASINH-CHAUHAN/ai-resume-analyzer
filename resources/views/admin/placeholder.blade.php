@extends('layouts.app')

@section('title', ($title ?? 'Admin Module') . ' | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => $title ?? 'Admin Module',
        'subtitle' => $subtitle ?? 'This admin module UI is ready and will be connected with CRUD functionality in a later phase.',
        'badge' => 'Admin Section',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => $title ?? 'Admin Module'],
        ],
    ])

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <h5 class="card-title fw-semibold mb-3">Section Overview</h5>
                <p class="text-muted mb-4">
                    {{ $subtitle ?? 'This section is prepared as part of the common admin UI structure in Phase 4.' }}
                </p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="mini-stat-card">
                            <div class="mini-stat-label">Status</div>
                            <div class="mini-stat-value">Ready</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mini-stat-card">
                            <div class="mini-stat-label">Design</div>
                            <div class="mini-stat-value">Completed</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mini-stat-card">
                            <div class="mini-stat-label">Next Step</div>
                            <div class="mini-stat-value">CRUD</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <h5 class="card-title fw-semibold mb-3">What Comes Next</h5>
                <ul class="text-muted ps-3 mb-0">
                    <li>Add database listing and filters.</li>
                    <li>Connect create, edit, and delete forms.</li>
                    <li>Show report and activity details.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

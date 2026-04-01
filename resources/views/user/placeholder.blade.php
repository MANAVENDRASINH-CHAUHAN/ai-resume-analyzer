@extends('layouts.app')

@section('title', ($title ?? 'Candidate Feature') . ' | AI Resume Analyzer System')

@section('content')
    @include('partials.page-header', [
        'title' => $title ?? 'Candidate Feature',
        'subtitle' => $subtitle ?? 'This feature page will be connected in the next phase.',
        'badge' => 'Candidate Section',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => $title ?? 'Candidate Feature'],
        ],
    ])

    <div class="dashboard-card">
        <h5 class="card-title fw-semibold mb-3">Feature Placeholder</h5>
        <p class="text-muted mb-4">
            {{ $subtitle ?? 'This page is kept ready so the candidate dashboard buttons already have a complete UI flow in Phase 4.' }}
        </p>
        <a href="{{ $buttonRoute ?? route('user.dashboard') }}" class="btn btn-primary rounded-pill px-4">
            {{ $buttonLabel ?? 'Back to Dashboard' }}
        </a>
    </div>
@endsection

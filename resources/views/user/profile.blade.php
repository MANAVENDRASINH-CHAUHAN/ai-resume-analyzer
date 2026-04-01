@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    @include('partials.page-header', [
        'title' => 'Profile',
        'subtitle' => 'This placeholder profile page keeps the interface consistent while full editing features are added later.',
        'badge' => 'Candidate Account',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'Profile'],
        ],
    ])

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->email }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->phone }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control text-capitalize" value="{{ auth()->user()->role }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

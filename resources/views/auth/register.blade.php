@extends('layouts.app')

@section('title', $pageTitle ?? 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="form-card">
                <div class="text-center mb-4">
                    <div class="brand-mark mx-auto mb-3">AI</div>
                    <div class="d-inline-flex flex-wrap gap-2 mb-3">
                        <a href="{{ route('register') }}" class="btn {{ ($registrationRole ?? 'candidate') === 'candidate' ? 'btn-dark' : 'btn-outline-dark' }} rounded-pill px-4">Candidate Register</a>
                        <a href="{{ route('register.admin') }}" class="btn {{ ($registrationRole ?? 'candidate') === 'admin' ? 'btn-dark' : 'btn-outline-dark' }} rounded-pill px-4">Admin Register</a>
                    </div>
                    <h2 class="fw-bold mb-2">{{ $pageTitle ?? 'Registration' }}</h2>
                    <p class="text-muted mb-0">{{ $pageSubtitle ?? 'Create your account.' }}</p>
                </div>

                <form action="{{ $submitRoute ?? route('register.submit') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 w-100 mt-4">Register</button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">Already registered? <a href="{{ route('login') }}">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="form-card">
                <h2 class="fw-bold mb-2">Admin Login</h2>
                <p class="text-muted mb-4">Access the dashboard to manage users, resumes, job roles, and reports.</p>

                <form action="{{ route('admin.login.attempt') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Admin Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-3 @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 w-100">Login as Admin</button>
                </form>

                <div class="text-center mt-4 text-muted">
                    Demo Admin: <strong>admin@resumeanalyzer.com</strong> / <strong>password</strong>
                </div>
            </div>
        </div>
    </div>
@endsection

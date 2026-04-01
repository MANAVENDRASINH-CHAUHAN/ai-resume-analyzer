@extends('layouts.app')

@section('title', 'User Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="form-card">
                <h2 class="fw-bold mb-2">User Login</h2>
                <p class="text-muted mb-4">Sign in to upload your resume and view analysis history.</p>

                <form action="{{ route('login.attempt') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-3 @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 w-100">Login</button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">New student? <a href="{{ route('register') }}">Create an account</a></small>
                </div>
            </div>
        </div>
    </div>
@endsection

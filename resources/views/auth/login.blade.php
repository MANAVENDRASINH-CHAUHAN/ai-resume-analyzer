@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="form-card">
                <div class="text-center mb-4">
                    <div class="brand-mark mx-auto mb-3">AI</div>
                    <h2 class="fw-bold mb-2">Login</h2>
                    <p class="text-muted mb-0">Use the same login form for both candidate and admin users.</p>
                </div>

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 w-100">Login</button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        No account?
                        <a href="{{ route('register') }}">Candidate Register</a>
                        or
                        <a href="{{ route('register.admin') }}">Admin Register</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'User Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="form-card">
                <h2 class="fw-bold mb-2">Create User Account</h2>
                <p class="text-muted mb-4">Register to upload resumes and get transparent job-role matching results.</p>

                <form action="{{ route('register.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control rounded-3 @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control rounded-3 @error('phone') is-invalid @enderror">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-3 @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 w-100 mt-4">Register</button>
                </form>
            </div>
        </div>
    </div>
@endsection

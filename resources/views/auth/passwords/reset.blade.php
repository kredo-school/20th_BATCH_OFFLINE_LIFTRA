@extends('layouts.register')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-register">

    <div class="register-wrapper text-center py-5">

        <!-- Logo -->
        <div class="mb-4">
            <img src="{{ asset('favicon.png') }}" alt="App Logo" class="logo-box p-1 mx-auto mb-3">
            <h2 class="fw-bold">Reset Password</h2>
            <p class="text-muted">Enter your new password below</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.update') }}" class="text-start">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label mb-0">Email</label>
                <input 
                    id="email"
                    type="email" 
                    class="form-control custom-input @error('email') is-invalid @enderror" 
                    name="email" 
                    placeholder="you@example.com"
                    value="{{ $email ?? old('email') }}" 
                    required 
                    autocomplete="email" 
                    autofocus
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label mb-0">New Password</label>
                <input 
                    id="password"
                    type="password" 
                    class="form-control custom-input @error('password') is-invalid @enderror" 
                    name="password" 
                    placeholder="••••••••" 
                    required 
                    autocomplete="new-password"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Must be at least 8 characters</small>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label class="form-label mb-0">Confirm Password</label>
                <input 
                    id="password-confirm"
                    type="password" 
                    class="form-control custom-input" 
                    name="password_confirmation" 
                    placeholder="••••••••" 
                    required 
                    autocomplete="new-password"
                >
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 register-btn">
                Reset Password
            </button>
        </form>

    </div>
</div>

@endsection

@extends('layouts.register')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-register">

    <div class="register-wrapper text-center py-5">

        <!-- Logo -->
        <div class="mb-4">
            <img src="{{ asset('favicon.png') }}" alt="App Logo" class="logo-box p-1 mx-auto mb-3">
            <h2 class="fw-bold">Forgot your password?</h2>
            <p class="text-muted">
                Enter your email address and we’ll send you a reset link
            </p>
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="alert alert-success text-start">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" class="text-start">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label mb-0">Email</label>
                <input 
                    id="email"
                    type="email" 
                    class="form-control custom-input @error('email') is-invalid @enderror" 
                    name="email" 
                    placeholder="you@example.com"
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email" 
                    autofocus
                >

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Back to Login -->
            <div class="text-center my-3">
                <small class="text-muted">
                    Remember your password?
                    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">
                        Sign in
                    </a>
                </small>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 register-btn">
                Send Reset Link
            </button>
        </form>

    </div>
</div>

@endsection

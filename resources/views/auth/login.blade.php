@extends('layouts.register')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-register">

    <div class="register-wrapper text-center py-5">

        <!-- Logo -->
        <div class="mb-4">
            <img src="{{ asset('favicon.png') }}" alt="App Logo" class="logo-box p-1 mx-auto mb-3">
            <h2 class="fw-bold">Welcome back</h2>
            <p class="text-muted">Continue your growth journey</p>
        </div>

        <!-- Social Login -->
        <div class="mb-3">
            <a href="{{ url('auth/google') }}" class="btn social-btn w-100 mb-3">
                Continue with Google
            </a>
        </div>

        <!-- Divider -->
        <div class="divider my-4">
            or sign in with email
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="text-start">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label mb-0">Email</label>
                <input 
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
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-2">
                <label class="form-label mb-0">Password</label>
                <input 
                    type="password" 
                    class="form-control custom-input @error('password') is-invalid @enderror" 
                    name="password" 
                    placeholder="••••••••" 
                    required 
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember + Forgot -->
            <div class="d-flex justify-content-between align-items-center my-3">
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label text-muted small" for="remember">
                        Remember me
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small text-decoration-none">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Don't have account -->
            <div class="text-center my-3">
                <small class="text-muted">
                    Don’t have an account?
                    <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">
                        Sign up
                    </a>
                </small>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 register-btn">
                Sign In
            </button>
        </form>

    </div>
</div>
@endsection
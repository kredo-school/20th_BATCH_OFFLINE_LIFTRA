@extends('layouts.register')

@section('content')

{{-- <style>
.bg-register {
    background: linear-gradient(
        to bottom,
        #ffffff 0%,
        #e1ddfb 20%,
        #cec9f9 40%,
        #c7c4f5 60%,
        #dbd7fa 80%,
        #ffffff 100%
    );

    .register-wrapper {
    width: 100%;
    max-width: 420px;
    }

    .logo-box {
    width: 60px;
    height: 60px;
    background: #ffffff;
    border-radius: 18px;
    }

    .social-btn {
        background: #4285F4; /* Googleブルー */
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: 0.2s ease;
    }

    .social-btn:hover {
        background: #357ae8; /* 少し暗めに */
        transform: translateY(-1px);
    }

    .divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #6c757d;
    font-size: 14px;
}

    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #dee2e6; /* 薄いグレー */
    }

    .divider::before {
        margin-right: 15px;
    }

    .divider::after {
        margin-left: 15px;
    }

    .custom-input::placeholder {
    color: #9ca3af;
    }

    .register-btn {
    background: #7c83fd;
    border: none;
    border-radius: 12px;
    padding: 14px;
    font-weight: 600;
    transition: 0.2s ease;
    }

    .register-btn:hover {
        background: #6366f1;
    }
}
</style> --}}

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-register">

    <div class="register-wrapper text-center py-5">

        <!-- Logo -->
        <div class="mb-4">
            <img src="{{ asset('favicon.png') }}" alt="App Logo" class="logo-box p-1 mx-auto mb-3">
            <h2 class="fw-bold">Create your account</h2>
            <p class="text-muted">Start your growth journey today</p>
        </div>

        <!-- Social Buttons -->
        <div class="mb-3">
            <a href="{{ url('auth/google') }}" class="btn social-btn w-100 mb-3">
                Continue with Google
            </a>
        </div>

        <!-- Divider -->
        <div class="divider my-4">
            or sign up with email
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="text-start">
            @csrf

            <!-- First Name -->
            <div class="mb-3">
                <label class="form-label mb-0">First Name</label>
                <input type="text" class="form-control custom-input @error('name') is-invalid @enderror" name="name" placeholder="Your first name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Last Name -->
            <div class="mb-3">
                <label class="form-label mb-0">Last Name</label>
                <input type="text" class="form-control custom-input" name="last_name" placeholder="Your last name">
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label mb-0">Email</label>
                <input type="email" class="form-control custom-input @error('email') is-invalid @enderror" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-2">
                <label class="form-label mb-0">Password</label>
                <input type="password" class="form-control custom-input @error('password') is-invalid @enderror" name="password" placeholder="••••••••" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Must be at least 8 characters</small>
            </div>

            <!-- Already have account -->
            <div class="text-center my-3">
                <small class="text-muted">
                    Already have an account?
                    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Sign in</a>
                </small>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 register-btn">
                Create Account
            </button>
        </form>

    </div>
</div>
@endsection
@extends('layouts.app')

@push('styles')
<style>
    .settings-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .password-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .settings-icon {
        width: 40px;
        height: 40px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 20px;
    }
    
    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border-color: #e2e8f0;
    }
    
    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    
    .btn-save {
        background: #6366f1;
        color: white;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: 0.2s;
    }
    
    .btn-save:hover {
        background: #4f46e5;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99,102,241,0.2);
    }
</style>
@endpush

@section('content')
<x-page-header title="Settings" subtitle="Manage Password" />

<div class="container settings-container pb-5 mt-4">
    
    <div class="password-card p-4">
        
        <div class="settings-icon">
            <i class="fa-solid fa-lock"></i>
        </div>
        
        <h4 class="fw-bold mb-1">Update Password</h4>
        <p class="text-muted small mb-4">Ensure your account is using a long, random password to stay secure.</p>
        
        <div class="alert bg-light border text-muted small rounded-3 mb-4">
            <i class="fa-solid fa-circle-info me-1"></i> <strong>Registered with Google?</strong><br>
            If you signed up via Google and have not set a password, please use the 
            <a href="{{ route('password.request') }}" class="text-primary text-decoration-none">Forgot Password</a> 
            link on the login page after logging out.
        </div>
        
        @if (session('status'))
            <div class="alert alert-success rounded-3 mb-4">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.password.update') }}">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div class="mb-4">
                <label for="current_password" class="form-label fw-semibold text-dark">Current Password</label>
                <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password">
                @error('current_password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="new_password" class="form-label fw-semibold text-dark">New Password</label>
                <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required autocomplete="new-password">
                @error('new_password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="new_password_confirmation" class="form-label fw-semibold text-dark">Confirm New Password</label>
                <input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" required autocomplete="new-password">
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('settings.index') }}" class="text-muted text-decoration-none fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Settings
                </a>
                <button type="submit" class="btn btn-save px-4">
                    Update Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

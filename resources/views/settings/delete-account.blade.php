@extends('layouts.app')

@push('styles')
<style>
    .settings-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .danger-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #fee2e2;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .danger-icon {
        width: 50px;
        height: 50px;
        background: #fef2f2;
        color: #ef4444;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }
    
    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border-color: #e2e8f0;
    }
    
    .form-control:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
    }
    
    .btn-danger-confirm {
        background: #ef4444;
        color: white;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: 0.2s;
        border: none;
    }
    
    .btn-danger-confirm:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.2);
    }
</style>
@endpush

@section('content')
<x-page-header title="Settings" subtitle="Delete Account" />

<div class="container settings-container pb-5 mt-4">
    
    <div class="danger-card p-4">
        
        <div class="danger-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        
        <h4 class="fw-bold mb-2 text-danger">Delete Account</h4>
        <p class="text-muted small mb-4">
            Once you delete your account, there is no going back. Please be certain.
            All your data, habits, actions, and tasks will be permanently erased from our servers.
        </p>

        <form method="POST" action="{{ route('settings.destroy-account') }}">
            @csrf
            @method('DELETE')



            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('settings.index') }}" class="text-muted text-decoration-none fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-danger-confirm px-4" onclick="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.');">
                    <i class="fa-solid fa-trash-can me-2"></i> Delete My Account
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

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
        background: #ef4444 !important;
        color: white !important;
        border: none !important;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: 0.2s;
    }
    
    .btn-danger-confirm:hover {
        background: #dc2626 !important;
        color: white !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.2);
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="page-header shadow-sm mt-0 mx-0 w-100" style="padding-top:20px; padding-bottom: 20px;">
    <div class="container-fluid px-2 px-md-4">
        <div class="d-flex align-items-center">
            
            <a href="{{ route('settings.index') }}" class="text-white text-decoration-none me-3 ms-2">
                <i class="fa-solid fa-chevron-left fs-5"></i>
            </a>
            
            <div>
                <h3 class="mb-0 fw-bold">Delete Account</h3>
            </div>
        </div>
    </div>
</div>

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



            <div class="d-flex justify-content-end align-items-center mt-4 gap-2">
                <a href="{{ route('settings.index') }}" class="btn btn-light px-4 fw-semibold border shadow-sm text-decoration-none text-dark">
                    Cancel
                </a>
                <button type="button" class="btn btn-danger-confirm px-4" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="fa-solid fa-trash-can me-2"></i> Delete My Account
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
        <div class="modal-content p-3 border-0 shadow-lg rounded-4 text-start">
            <div class="modal-body text-center pt-4">
                <div class="mb-3 text-danger">
                    <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-3">Permanent Account Deletion</h5>
                <p class="text-muted mb-4">Are you absolutely sure? All your data will be permanently deleted. This action cannot be undone.</p>
            </div>
            <div class="text-center px-3 pb-3">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('settings.destroy-account') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Confirm Deletion</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

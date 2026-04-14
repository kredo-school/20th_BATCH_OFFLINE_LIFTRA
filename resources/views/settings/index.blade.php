@extends('layouts.app')

@push('styles')
<style>
    /* Settings Page Specific Styles */
    /* Removed max-width to unify with Task page width */
    
    .settings-profile-card {
        margin-top: -30px; /* Slight overlap with header similar to the mockup */
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .settings-profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    
    .settings-avatar {
        width: 60px;
        height: 60px;
        background: #8b5cf6;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 500;
    }
    
    .settings-section-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        margin-top: 2rem;
    }
    
    .settings-list-group {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    
    .settings-list-item {
        border: 1px solid #f1f5f9;
        margin-bottom: -1px; /* Remove double borders */
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        color: #334155;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    
    .settings-list-item:first-child {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    
    .settings-list-item:last-child {
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        margin-bottom: 0;
    }
    
    .settings-list-item:hover {
        background-color: #f8fafc;
        color: #1e293b;
    }
    
    .settings-icon {
        width: 24px;
        color: #64748b;
        margin-right: 12px;
        text-align: center;
    }
    
    .settings-chevron {
        color: #cbd5e1;
        font-size: 0.875rem;
    }
    
    .btn-logout {
        color: #ef4444;
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 1rem;
        width: 100%;
        font-weight: 500;
        transition: all 0.2s;
        margin-top: 2rem;
    }
    
    .btn-logout:hover {
        background: #fef2f2;
        border-color: #fee2e2;
    }
    
    .btn-delete {
        color: #ef4444;
        background: transparent;
        border: 1px solid #fca5a5;
        border-radius: 12px;
        padding: 1rem;
        width: 100%;
        font-weight: 500;
        transition: all 0.2s;
        margin-top: 1rem;
    }
    
    .btn-delete:hover {
        background: #fef2f2;
    }
    
    .settings-footer {
        text-align: center;
        margin-top: 2.5rem;
        margin-bottom: 2rem;
        font-size: 0.75rem;
        color: #94a3b8;
    }
</style>
@endpush

@section('content')
<x-page-header title="{{ __('Settings') }}" />

<div class="container-fluid px-3 px-md-5 pb-5">
    <div class="row justify-content-center mt-3">
        <div class="col-12">
    <!-- Profile Overlapping Card -->
    <a href="{{ route('profile.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm settings-profile-card p-2 mb-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="settings-avatar">
                        @if(Auth::user()->profile_image)
                            <img src="{{ Auth::user()->profile_image }}" alt="User Avatar" class="rounded-circle w-100 h-100" style="object-fit:cover;">
                        @else
                            {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">{{ Auth::user()->name }}</h5>
                        <div class="text-muted small">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-semibold d-none d-sm-inline">{{ __('View Profile') }}</span>
                        <i class="fa-solid fa-chevron-right settings-chevron"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>


    <!-- PREFERENCES -->
    <div class="settings-section-title">{{ __('Preferences') }}</div>
    <div class="settings-list-group">
        <!-- Language Select Dropdown -->
        <a href="#" class="settings-list-item text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-language settings-icon" style="opacity: 0.7;"></i>
                <span>{{ __('Language') }}</span>
            </div>
            
            <!-- Right side selected value + chevron -->
            <div class="d-flex align-items-center gap-2 text-muted">
                <span style="font-size: 0.95rem;">{{ (auth()->user()->language ?? 'en') === 'ja' ? '日本語' : 'English' }}</span>
                <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem;"></i>
            </div>
        </a>
        
        <form id="language-form" action="{{ route('settings.language.update') }}" method="POST" class="d-none">
            @csrf
            @method('PUT')
            <input type="hidden" name="language" id="language-input" value="">
        </form>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1 py-2">
            <li><a class="dropdown-item {{ (auth()->user()->language ?? 'en') === 'en' ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('language-input').value='en'; document.getElementById('language-form').submit();">English</a></li>
            <li><a class="dropdown-item {{ (auth()->user()->language ?? 'en') === 'ja' ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('language-input').value='ja'; document.getElementById('language-form').submit();">日本語</a></li>
        </ul>
    </div>

    <!-- SUPPORT & INFO -->
    <div class="settings-section-title">{{ __('Support & Info') }}</div>
    <div class="settings-list-group">
        <a href="{{ route('settings.help') }}" class="settings-list-item">
            <div class="d-flex align-items-center">
                <i class="fa-regular fa-circle-question settings-icon"></i>
                <span>{{ __('Help Center') }}</span>
            </div>
            <i class="fa-solid fa-chevron-right settings-chevron"></i>
        </a>
    </div>

    <!-- ACCOUNT -->
    @if(!auth()->user()->google_access_token)
    <div class="settings-section-title">{{ __('Account') }}</div>
    <div class="settings-list-group">
        <a href="{{ route('settings.password.edit') }}" class="settings-list-item">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-lock settings-icon" style="opacity: 0.7;"></i>
                <span>{{ __('Manage Password') }}</span>
            </div>
            <i class="fa-solid fa-chevron-right settings-chevron"></i>
        </a>
    </div>
    @endif


    <!-- ACTIONS -->
    <button type="button" class="btn-logout d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        {{ __('Log Out') }}
    </button>

    <a href="{{ route('settings.delete-account') }}" class="btn-delete d-flex align-items-center justify-content-center gap-2 text-decoration-none mt-3">
        <i class="fa-regular fa-trash-can"></i>
        {{ __('Delete Account') }}
    </a>
    
    <div class="settings-footer">
        Liftra v1.0.0
    </div>

        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
        <div class="modal-content p-3 border-0 shadow-lg rounded-4 text-start">
            <div class="modal-body text-center pt-4">
                <div class="mb-3 text-danger">
                    <i class="fa-solid fa-arrow-right-from-bracket fa-3x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-3">{{ __('Log Out') }}</h5>
                <p class="text-muted mb-4">{{ __('Are you sure you want to log out from Liftra?') }}</p>
            </div>
            <div class="text-center px-3 pb-3">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">{{ __('Log Out') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

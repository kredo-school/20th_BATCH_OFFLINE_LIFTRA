@extends('layouts.app')

@push('styles')
<style>
    /* Settings Page Specific Styles */
    .settings-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
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
<x-page-header title="Settings" />

<div class="container settings-container pb-5">
    
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
                        <span class="small fw-semibold d-none d-sm-inline">View Profile</span>
                        <i class="fa-solid fa-chevron-right settings-chevron"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>


    <!-- PREFERENCES -->
    <div class="settings-section-title">Preferences</div>
    <div class="settings-list-group">
        <a href="#" class="settings-list-item">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-palette settings-icon" style="opacity: 0.7;"></i>
                <span>Colour</span>
            </div>
            <i class="fa-solid fa-chevron-right settings-chevron"></i>
        </a>
        <!-- Language Select Dropdown -->
        <a href="#" class="settings-list-item text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-language settings-icon" style="opacity: 0.7;"></i>
                <span>Language</span>
            </div>
            
            <!-- Right side selected value + chevron -->
            <div class="d-flex align-items-center gap-2 text-muted">
                <span style="font-size: 0.95rem;">English</span>
                <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem;"></i>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1 py-2">
            <li><a class="dropdown-item active" href="#">English</a></li>
            <li><a class="dropdown-item" href="#">日本語</a></li>
        </ul>

    <!-- SUPPORT & INFO -->
    <div class="settings-section-title">Support & Info</div>
    <div class="settings-list-group">
        <a href="{{ route('settings.help') }}" class="settings-list-item">
            <div class="d-flex align-items-center">
                <i class="fa-regular fa-circle-question settings-icon"></i>
                <span>Help Center</span>
            </div>
            <i class="fa-solid fa-chevron-right settings-chevron"></i>
        </a>
        <a href="#" class="settings-list-item">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-info settings-icon" style="opacity: 0.7;"></i>
                <span>About Liftra</span>
            </div>
            <i class="fa-solid fa-chevron-right settings-chevron"></i>
        </a>
    </div>

    <!-- ACCOUNT -->
    <div class="settings-section-title">Account</div>
    <div class="settings-list-group">
        <a href="#" class="settings-list-item">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-lock settings-icon" style="opacity: 0.7;"></i>
                <span>Manage Password</span>
            </div>
            <i class="fa-solid fa-chevron-right settings-chevron"></i>
        </a>
    </div>


    <!-- ACTIONS -->
    <form action="{{ route('logout') }}" method="POST" id="logout-form">
        @csrf
        <button type="submit" class="btn-logout d-flex align-items-center justify-content-center gap-2">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            Log Out
        </button>
    </form>

    <button type="button" class="btn-delete d-flex align-items-center justify-content-center gap-2">
        <i class="fa-regular fa-trash-can"></i>
        Delete Account
    </button>
    
    <div class="settings-footer">
        Liftra v1.0.0
    </div>

</div>
@endsection

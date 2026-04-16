@extends('layouts.app')

@section('content')

<x-page-header 
    title="{{ __('Personal Information') }}"
    subtitle="{{ __('Update your personal details') }}"
/>

<div class="profile-wrapper mt-3">
    <div class="container profile-card-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-lg border-0 profile-card">
                    <div class="card-body p-4">

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- 1️⃣ Image --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('Profile Image') }}</label>
                                <div class="d-flex align-items-center gap-4">

                                    <div class="avatar-circle flex-shrink-0"
                                        style="width:90px;height:90px;overflow:hidden;">
                                        @if($user->profile_image)
                                            <img src="{{ $user->profile_image }}" 
                                                class="rounded-circle w-100 h-100" 
                                                style="object-fit:cover;">
                                        @else
                                            <span class="avatar-initial fs-3">
                                                {{ mb_strtoupper(mb_substr($user->name,0,1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        <input type="file" name="profile_image" class="form-control rounded-3">
                                        <small class="text-muted">{{ __('JPG, PNG or GIF. Max 2MB.') }}</small>
                                    </div>

                                </div>
                            </div>

                            {{-- 2️⃣ User Name --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('User Name') }}</label>
                                <input type="text" 
                                    name="name" 
                                    class="form-control rounded-3"
                                    value="{{ old('name', $user->name) }}">
                            </div>

                            {{-- 3️⃣ Email --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Email') }}</label>
                                <input type="email" 
                                    name="email" 
                                    class="form-control rounded-3"
                                    value="{{ old('email', $user->email) }}">
                            </div>

                            {{-- 4️⃣ Birthday --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Birthday') }}</label>
                                <input type="date" 
                                    name="birthday" 
                                    class="form-control rounded-3"
                                    value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}">
                            </div>

                            {{-- 5️⃣ LinkedIn --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('LinkedIn') }}</label>
                                <input type="url" 
                                    name="linkedin" 
                                    class="form-control rounded-3"
                                    placeholder="https://linkedin.com/in/yourprofile"
                                    value="{{ old('linkedin', $user->linkedin) }}">
                            </div>

                            {{-- 6️⃣ Portfolio --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Portfolio') }}</label>
                                <input type="url" 
                                    name="portfolio" 
                                    class="form-control rounded-3"
                                    placeholder="https://yourwebsite.com"
                                    value="{{ old('portfolio', $user->portfolio) }}">
                            </div>

                            {{-- 7️⃣ User Goal --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('User Goal') }}</label>
                                <textarea name="usersgoal" 
                                        id="usersgoal"
                                        rows="2"
                                        class="form-control rounded-3"
                                        maxlength="100">{{ old('usersgoal', $user->usersgoal) }}</textarea>
                                <div class="text-end small text-muted">
                                    {{ __('Max 100 characters') }}
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('profile.index') }}" 
                                class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2 border shadow-sm">
                                    {{ __('Cancel') }}
                                </a>

                                <button type="submit" 
                                        class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                    {{ __('Update') }}
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if(window.location.hash === '#usersgoal') {
            const el = document.getElementById('usersgoal');
            if(el) {
                setTimeout(() => {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.focus();
                }, 100);
            }
        }
    });
</script>

@endsection
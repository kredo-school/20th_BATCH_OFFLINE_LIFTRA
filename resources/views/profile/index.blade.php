@extends('layouts.app')
@include('profile.modals.education-add')
@include('profile.modals.education-edit')
@include('profile.modals.education-delete')
@include('profile.modals.experience-add')
@include('profile.modals.experience-edit')
@include('profile.modals.experience-delete')
@include('profile.modals.certification-add')
@include('profile.modals.certification-edit')
@include('profile.modals.certification-delete')
@include('profile.modals.skill-add')
@include('profile.modals.skill-edit')
@include('profile.modals.skill-delete')

@section('content')
<x-page-header 
    title="Professional Profile"
    subtitle="Resume & Career Management"
>
    <a href="{{ route('profile.edit') }}" class="btn btn-light rounded-3 px-4">
        <i class="bi bi-pencil"></i>
        Edit Profile
    </a>
</x-page-header>

<div class="profile-wrapper mt-3">
    <!-- 👤 Profile Card -->
    <div class="container profile-card-container">
        <div class="card shadow-lg border-0 profile-card">
            <div class="card-body d-flex align-items-center">

                <div class="d-flex align-items-start w-100">

                    {{-- アバター --}}
                    <div class="avatar-circle flex-shrink-0 me-4" style="width:80px; height:80px;">
                        @if($user->profile_image)
                            <img src="{{ $user->profile_image }}" class="rounded-circle w-100 h-100" style="object-fit:cover;">
                        @else
                            <span class="avatar-initial" style="font-size:2rem;">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    {{-- 右側 --}}
                    <div class="flex-grow-1">

                        {{-- 名前 --}}
                        <h4 class="mb-3 fw-bold">{{ $user->name }}</h4>

                        {{-- 上段: メール / LinkedIn --}}
                        <div class="row mb-2">
                            {{-- メール --}}
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <div class="d-flex">
                                    <small class="text-muted me-1">Email:</small>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </div>

                            {{-- LinkedIn --}}
                            <div class="col-12 col-md-6">
                                <div class="d-flex">
                                    <small class="text-muted me-1">LinkedIn:</small>
                                    <span>
                                        @if(!empty($user->linkedin))
                                            <a href="{{ $user->linkedin }}" target="_blank" class="text-decoration-none">View profile</a>
                                        @else
                                            Not registered
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- 下段: 誕生日 / Portfolio --}}
                        <div class="row">
                            {{-- 誕生日 --}}
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <div class="d-flex">
                                    <small class="text-muted me-1">Birthday:</small>
                                    <span>
                                        @if(!empty($user->birthday))
                                            {{ \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') }}
                                        @else
                                            Not registered
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Portfolio --}}
                            <div class="col-12 col-md-6">
                                <div class="d-flex">
                                    <small class="text-muted me-1">Portfolio:</small>
                                    <span>
                                        @if(!empty($user->portfolio))
                                            <a href="{{ $user->portfolio }}" target="_blank" class="text-decoration-none">Visit site</a>
                                        @else
                                            Not registered
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-muted">
                    <i class="bi bi-pencil"></i>
                </a>

            </div>
        </div>
    </div>

    <!-- 📄 Content -->
    <div class="container py-4">

        {{-- Education --}}
        <div class="card shadow-sm border-0 mb-4 section-card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold section-title text-primary">
                        <i class="bi bi-mortarboard me-2"></i>Education
                    </h6>
                    <a href="#" class="small text-decoration-none" data-bs-toggle="modal" data-bs-target="#addEducationModal">+ Add</a>
                </div>
                @foreach($user->education ?? [] as $edu)
                <div class="mb-3">
                    <h6 class="mb-1">{{ $edu['degree'] }}</h6>
                    <small class="text-muted">
                        {{ $edu['school'] }} • {{ $edu['years'] }}
                    </small>

                    {{-- 編集・削除ボタン --}}
                    <div>
                        <a href="#" class="text-primary me-3" data-bs-toggle="modal" data-bs-target="#educationEditModal" data-id="{{ $edu['id'] }}">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#educationDeleteModal" data-id="{{ $edu['id'] }}">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Work Experience --}}
        <div class="card shadow-sm border-0 mb-4 section-card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold text-success">
                        <i class="bi bi-briefcase me-2"></i>Work Experience
                    </h6>
                    <a href="#" class="small text-decoration-none">+ Add</a>
                </div>
                @foreach($user->experience ?? [] as $exp)
                    <div class="mb-3">
                        <h6 class="mb-1">{{ $exp['position'] }}</h6>
                        <small class="text-muted">
                            {{ $exp['company'] }} • {{ $exp['years'] }}
                        </small>
                        <p class="small text-muted mt-2">{{ $exp['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Certifications --}}
        <div class="card shadow-sm border-0 mb-4 section-card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold text-purple">
                        <i class="bi bi-patch-check me-2"></i>Certifications
                    </h6>
                    <a href="#" class="small text-decoration-none">+ Add</a>
                </div>
                <ul class="list-unstyled small">
                    @foreach($user->certifications ?? [] as $cert)
                        <li class="mb-2">{{ $cert }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Skills --}}
        <div class="card shadow-sm border-0 section-card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold text-warning">
                        <i class="bi bi-lightning-charge me-2"></i>Skills
                    </h6>
                    <a href="#" class="small text-decoration-none">+ Add</a>
                </div>
                @foreach($user->skills ?? [] as $skill)
                    <span class="badge bg-primary-subtle text-primary me-2 mb-2">
                        {{ $skill }}
                    </span>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
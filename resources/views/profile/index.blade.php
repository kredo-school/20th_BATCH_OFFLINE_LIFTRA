@extends('layouts.app')

@push('styles')
<style>
    .header-action-btn {
        transition: all 0.2s ease;
    }
    .header-action-btn:hover, .header-action-btn:hover i, .header-action-btn:hover span {
        color: #6366F1 !important;
    }
    .skill-tag {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .skill-tag:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .skill-action-link {
        color: #94a3b8;
        transition: color 0.2s ease;
        font-size: 0.8rem;
    }
    .skill-action-link:hover {
        color: #64748b;
    }
    .skill-delete-link:hover {
        color: #ef4444 !important;
    }
    .section-title i {
        width: 24px;
        text-align: center;
    }
</style>
@endpush
@push('modals')
    @include('profile.modals.education-add')
    @include('profile.modals.experience-add')
    @include('profile.modals.certification-add')
    @include('profile.modals.skill-add')
@endpush

@section('content')
<x-page-header 
    title="{{ __('Professional Profile') }}"
    subtitle="{{ __('Resume & Career Management') }}"
>
    <a href="{{ route('profile.edit') }}" class="btn btn-light text-primary-6366F1 rounded-3 px-4 fw-semibold shadow-sm header-action-btn d-none d-md-inline-block">
        <i class="fa-solid fa-pen-to-square me-1"></i>
        <span class="btn-text">{{ __('Edit Profile') }}</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="btn btn-light rounded-3 d-flex align-items-center justify-content-center d-md-none shadow-sm header-action-btn" style="color: #6366F1; width:42px; height:42px;">
        <i class="fa-solid fa-pen-to-square fs-5"></i>
    </a>
</x-page-header>

<div class="profile-wrapper mt-3">
    <!-- 👤 Profile Card -->
    <div class="container-fluid px-3 px-md-5 profile-card-container mb-4">
        <div class="card shadow-sm border-0 rounded-3 profile-card">
            <div class="card-body d-flex align-items-center">

                <div class="d-flex align-items-start w-100">

                    {{-- アバター --}}
                    <div class="avatar-circle flex-shrink-0 me-4" style="width:80px; height:80px;">
                        @if($user->profile_image)
                            <img src="{{ $user->profile_image }}" class="rounded-circle w-100 h-100" style="object-fit:cover;">
                        @else
                            <span class="avatar-initial fs-2">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    {{-- 右側 --}}
                    <div class="flex-grow-1">

                        {{-- 名前 --}}
                        <h3 class="mb-2 fw-bold">{{ $user->name }}</h3>

                        {{-- 上段: メール / LinkedIn --}}
                        <div class="row mb-2">
                            <div class="col-12 col-md-6 mb-1 mb-md-0">
                                <small class="text-muted">{{ __('Email') }}:</small>
                                <div>{{ $user->email }}</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <small class="text-muted">{{ __('LinkedIn') }}:</small>
                                <div>
                                    @if(!empty($user->linkedin))
                                        <a href="{{ $user->linkedin }}" target="_blank" class="text-decoration-none">
                                            {{ __('View profile') }}
                                        </a>
                                    @else
                                        {{ __('Not registered') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 下段: 誕生日 / Portfolio --}}
                        <div class="row">
                            <div class="col-12 col-md-6 mb-1 mb-md-0">
                                <small class="text-muted">{{ __('Birthday') }}:</small>
                                <div>
                                    @if(!empty($user->birthday))
                                        {{ \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') }}
                                    @else
                                        {{ __('Not registered') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <small class="text-muted">{{ __('Portfolio') }}:</small>
                                <div>
                                    @if(!empty($user->portfolio))
                                        <a href="{{ $user->portfolio }}" target="_blank" class="text-decoration-none">
                                            {{ __('Visit site') }}
                                        </a>
                                    @else
                                        {{ __('Not registered') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="text-muted ms-3">
                    <i class="bi bi-pencil fs-4"></i>
                </a>

            </div>
        </div>
    </div>

    <!-- 📄 Content -->
    <div class="container-fluid px-3 px-md-5 py-4">

        {{-- Education --}}
        <div class="card shadow-sm border-0 mb-4 rounded-3 section-card">
            <div class="card-body">

                {{-- header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold section-title m-0">
                        <i class="fa-solid fa-graduation-cap text-primary me-2"></i> {{ __('Education') }}
                    </h5>

                    <a href="#" class="small text-decoration-none text-dark"
                    data-bs-toggle="modal"
                    data-bs-target="#addEducationModal">
                        {{ __('+ Add') }}
                    </a>
                </div>
                @foreach($user->education ?? [] as $edu)

                <div class="d-flex justify-content-between mb-4">

                    {{-- 左側 --}}
                    <div>

                        {{-- School --}}
                        <div class="fw-semibold mb-1">
                            {{ $edu->school_name }}
                        </div>
                        
                        {{-- Degree --}}
                        <div class="text-primary small fw-semibold">
                            {{ $edu->degree }} {{ $edu->field ? __('in') . " " . $edu->field : "" }}
                        </div>

                        {{-- Location + year --}}
                        <div class="text-muted small">
                            {{ $edu->country }} •
                            {{ \Carbon\Carbon::parse($edu->start_date)->format('Y,M') }}
                            -
                            {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('Y,M') : __('Present') }}
                        </div>
                    </div>

                    {{-- 右側アイコン --}}
                    <div class="d-flex align-items-start gap-2">

                        <a href="#"
                        class="text-dark"
                        data-bs-toggle="modal"
                        data-bs-target="#editEducationModal-{{ $edu->id }}"
                        data-id="{{ $edu->id }}">
                            <i class="fa-solid fa-pen-to-square text-secondary"></i>
                        </a>

                        <a href="#"
                        class="text-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteEducationModal"
                        data-id="{{ $edu->id }}">
                            <i class="fa-solid fa-trash-can small"></i>
                        </a>
                    </div>
                </div>

                @if(!$loop->last)
                <hr class="my-3">
                @endif

                @push('modals')
                    @include('profile.modals.education-edit', ['edu' => $edu])
                    @include('profile.modals.education-delete', ['edu' => $edu])
                @endpush
                @endforeach
            </div>
        </div>

        {{-- Work Experience --}}
        <div class="card shadow-sm border-0 mb-4 rounded-3 section-card">
            <div class="card-body">

                {{-- header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold section-title m-0">
                        <i class="fa-solid fa-briefcase text-success me-2"></i> {{ __('Work Experience') }}
                    </h5>

                    <a href="#"
                    class="small text-decoration-none text-dark"
                    data-bs-toggle="modal"
                    data-bs-target="#addExperienceModal">
                        {{ __('+ Add') }}
                    </a>
                </div>

                @foreach($user->experience ?? [] as $exp)

                <div class="d-flex justify-content-between mb-4">

                    {{-- 左 --}}
                    <div>

                        <div class="fw-semibold mb-1">
                            {{ $exp->job_title }}
                        </div>

                        <div class="text-primary fw-semibold small">
                            {{ $exp->company_name }} - <span class="text-muted">{{ $exp->employment_type }}</span>
                        </div>

                        <div class="text-muted small">
                            {{ \Carbon\Carbon::parse($exp->start_date)->format('Y,M') }}
                            -
                            {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('Y,M') : __('Present') }}
                        </div>

                        @if($exp->description)
                        <div class="small text-muted mt-2">
                            {{ $exp->description }}
                        </div>
                        @endif

                    </div>

                    {{-- icons --}}
                    <div class="d-flex align-items-start gap-2">

                        <a href="#"
                        class="text-dark"
                        data-bs-toggle="modal"
                        data-bs-target="#editExperienceModal-{{ $exp->id }}">
                            <i class="fa-solid fa-pen-to-square text-secondary"></i>
                        </a>

                        <a href="#"
                        class="text-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteExperienceModal-{{ $exp->id }}">
                            <i class="fa-solid fa-trash-can small"></i>
                        </a>

                    </div>

                </div>

                @if(!$loop->last)
                <hr class="my-3">
                @endif

                @push('modals')
                    @include('profile.modals.experience-edit', ['exp' => $exp])
                    @include('profile.modals.experience-delete', ['exp' => $exp])
                @endpush

                @endforeach

            </div>
        </div>

        {{-- Certifications --}}
        <div class="card shadow-sm border-0 mb-4 rounded-3 section-card">
            <div class="card-body">

                {{-- header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold section-title m-0">
                        <i class="fa-solid fa-certificate text-purple me-2"></i> {{ __('Certifications') }}
                    </h5>

                    <a href="#"
                    class="small text-decoration-none text-dark"
                    data-bs-toggle="modal"
                    data-bs-target="#addCertificationModal">
                        {{ __('+ Add') }}
                    </a>
                </div>

                <div class="row g-3">

                @foreach($user->userCertifications ?? [] as $cert)

                <div class="col-12 col-md-6">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="d-flex justify-content-between">

                            <div>

                                {{-- title --}}
                                <div class="fw-semibold">
                                    {{ $cert['title'] }}
                                </div>

                                {{-- issuer --}}
                                <div class="text-primary small">
                                    {{ $cert->issuer }}
                                </div>

                                {{-- date --}}
                                <div class="text-muted small mt-2">
                                    {{ __('Issued') }}:
                                    {{ \Carbon\Carbon::parse($cert->obtained_date)->format('F Y') }}
                                </div>

                            </div>

                            {{-- icons --}}
                            <div class="d-flex gap-2">

                                <a href="#"
                                class="text-dark"
                                data-bs-toggle="modal"
                                data-bs-target="#editCertificationModal-{{ $cert->id }}">
                                    <i class="fa-solid fa-pen-to-square text-secondary"></i>
                                </a>

                                <a href="#"
                                class="text-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteCertificationModal-{{ $cert->id }}">
                                    <i class="fa-solid fa-trash-can small"></i>
                                </a>

                            </div>

                        </div>

                    </div>

                </div>
                
                @push('modals')
                    @include('profile.modals.certification-edit',['cert'=>$cert])
                    @include('profile.modals.certification-delete',['cert'=>$cert])
                @endpush

                @endforeach

                </div>

            </div>
        </div>

        {{-- Skills --}}
        <div class="card shadow-sm border-0 rounded-3 section-card">
            <div class="card-body">

                {{-- header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold section-title m-0">
                        <i class="fa-solid fa-bolt me-2" style="color: #f59e0b;"></i> {{ __('Skills') }}
                    </h5>
                    <a href="#" class="small text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                        {{ __('+ Add') }}
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @foreach($user->userSkills ?? [] as $skill)
                        <div class="skill-tag rounded-pill shadow-sm">
                            <span class="skill-name">{{ $skill->skill_name }}</span>
                            
                            <div class="d-flex align-items-center gap-2 ms-2 border-start ps-2">
                                <a href="#"
                                class="skill-action-link"
                                data-bs-toggle="modal"
                                data-bs-target="#editSkillModal-{{ $skill->id }}"
                                title="Edit Skill">
                                    <i class="fa-solid fa-pen-to-square text-secondary"></i>
                                </a>
                                <a href="#"
                                class="skill-action-link skill-delete-link"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteSkillModal-{{ $skill->id }}"
                                title="Delete Skill">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            </div>
                        </div>

                        {{-- モーダル include --}}
                        @push('modals')
                            @include('profile.modals.skill-edit', ['skill' => $skill])
                            @include('profile.modals.skill-delete', ['skill' => $skill])
                        @endpush
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
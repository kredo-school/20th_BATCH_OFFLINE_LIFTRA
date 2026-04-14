@extends('layouts.app')

@section('content')

<style>
    .btn-add-category:hover {
        color: #6366F1 !important;
    }
    .btn-add-category:hover i {
        color: #6366F1 !important;
    }
    @media (max-width: 991.98px) {
        .btn-responsive.hide-on-mobile {
            display: none !important;
        }
    }
</style>

<x-page-header 
    title="LifePlan"
    subtitle="Your roadmap to fulfilling life"
>
    <!-- Desktop Only: Add Categories -->
    <a href="#" class="btn btn-light rounded-3 px-4 text-primary-6366F1 btn-responsive btn-add-category hide-on-mobile" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fa-solid fa-plus text-primary-6366F1 me-1"></i>
        {{ __('Add Categories') }}
    </a>

    <!-- Mobile Only: Add/Edit Primary Goal -->
    <!-- Removed per user request -->
</x-page-header>

@if(Auth::check() && empty(Auth::user()->birthday))
    <div class="container-fluid px-3 px-md-5 mt-3 mb-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center justify-content-between p-3 px-4 mb-0">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-cake-candles fs-5 text-danger"></i>
                        <span class="fw-medium text-dark">{{ __('Please enter your birthday to use the goal feature.') }}</span>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-danger rounded-3 px-4 fw-semibold shadow-sm">
                        {{ __('Enter Birthday') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

@include('lifeplan.modals.add-category')

<div class="container-fluid px-3 px-md-5">
    <div class="row justify-content-center mt-3">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3 rounded-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3 rounded-4" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm rounded-3 p-4 mb-4 mt-2">
                <div class="d-flex align-items-start justify-content-between mb-4 gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-star fs-4 text-primary mt-1"></i>
                        <div>
                            <div class="text-muted small">{{ __('My Primary Life Goal') }}</div>
                            <div class="fw-bold text-dark">
                                {{ Auth::user()->usersgoal ?: __('No primary life goal set yet.') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0 d-none d-md-block">
                        <a href="{{ route('profile.edit') }}#usersgoal" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-medium">
                            @if(Auth::user()->usersgoal)
                                {{ __('Edit Primary Goal') }}
                            @else
                                {{ __('Add Primary Goal') }}
                            @endif
                        </a>
                    </div>
                    
                    <!-- Mobile Only: Edit Primary Goal Icon -->
                    <div class="flex-shrink-0 d-md-none">
                        <a href="{{ route('profile.edit') }}#usersgoal" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; color: #6366F1;">
                            <i class="fa-solid fa-pen m-0"></i>
                        </a>
                    </div>
                </div>

                <!-- Overall Progress -->
                <div class="mb-2 text-muted small">
                    {{ __('Overall Progress') }}
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="progress flex-grow-1" style="height: 8px; border-radius: 10px;">
                        <div 
                            class="progress-bar" 
                            role="progressbar" 
                            style="width: {{ $overallProgress }}%; background: linear-gradient(90deg, #6366F1, #8B5CF6);" 
                            aria-valuenow="{{ $overallProgress }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                        </div>
                    </div>

                    <div class="fw-semibold text-primary-6366F1">
                        {{ $overallProgress }}%
                    </div>
                </div>
            </div>

            <!-- Life Categories Section -->
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0">{{ __('Life Categories') }}</h5>
                </div>

                @if(isset($categories) && $categories->count())
                    <div class="row g-4">
                        @foreach($categories as $category)
                            <div class="col-md-6 col-lg-4">
                                <div class="card shadow-sm rounded-4 p-4 h-100 position-relative" style="transition: transform 0.15s, box-shadow 0.15s;" 
                                     onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)'"
                                     onmouseleave="this.style.transform='';this.style.boxShadow=''">

                                    <a href="{{ route('lifeplan.category.show', $category->id) }}" class="text-decoration-none text-dark stretched-link"></a>

                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center gap-3">

                                            <!-- Icon -->
                                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; background-color: {{ $category->color->code ?? '#EEF2FF' }}{{ !isset($category->color->code) ? '' : '20' }};">
                                                
                                                <i class="fa-solid {{ $category->icon->class ?? 'fa-folder' }}"
                                                   style="color: {{ $category->color->code ?? '#6366F1' }};"></i>
                                            </div>

                                            <!-- Category Name -->
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $category->name }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $category->goals->count() }} {{ $category->goals->count() === 1 ? __('goal') : __('goals') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Percentage -->
                                            <div class="fw-semibold"
                                                 style="color: {{ $category->color->code ?? '#6366F1' }};">
                                                {{ $category->progress }}%
                                            </div>
                                            
                                            <!-- Dropdown menu -->
                                            <div class="dropdown position-relative" style="z-index: 2;">
                                                <button class="btn btn-link text-muted p-0 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end p-0 shadow-sm border-0" style="min-width: 120px;">
                                                    <li><a class="dropdown-item btn btn-light text-secondary py-1" href="#" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}"><i class="fa-solid fa-pen-to-square me-2"></i>{{ __('Edit') }}</a></li>
                                                    <li>
                                                        <form action="{{ route('lifeplan.category.destroy', $category->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item btn btn-light text-danger py-1"><i class="fa-solid fa-trash-can me-2"></i>{{ __('Delete') }}</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="progress" style="height: 8px; border-radius: 10px;">
                                        <div class="progress-bar"
                                             role="progressbar"
                                             style="width: {{ $category->progress }}%; background-color: {{ $category->color->code ?? '#6366F1' }};"
                                             aria-valuenow="{{ $category->progress }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Edit Category Modal -->
                            @include('lifeplan.modals.edit-category', ['category' => $category])
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">
                        {{ __('No categories found. Please add a category.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
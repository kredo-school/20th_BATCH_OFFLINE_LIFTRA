@extends('layouts.app')

@push('styles')
<style>
    .goal-card-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .goal-card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush

@section('content')

@include('lifeplan.modals.add-goal')

{{-- Header with category color background --}}
<div class="page-header shadow-sm" style="background: {{ $category->color->code ?? '#6366F1' }};">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-lg-7 col-md-6 col-12 ps-lg-5 ps-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none">
                        <i class="fa-solid fa-chevron-left fs-5"></i>
                    </a>
                    <h1 class="mb-0 fw-bold">{{ $category->name }}</h1>
                </div>
            </div>

            <div class="col-lg-5 col-md-6 col-12 text-md-end text-start mt-md-0 mt-3 pe-lg-5 pe-3">
                <div class="header-actions d-flex justify-content-md-end justify-content-start align-items-center gap-2">
                    <a href="#" class="btn btn-white rounded-3 px-4 fw-semibold" style="background: white; color: {{ $category->color->code ?? '#6366F1' }};" data-bs-toggle="modal" data-bs-target="#addGoalModal">
                        <i class="fa-solid fa-plus me-2"></i> Add Goals
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@if(is_null($userAge))
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center justify-content-between p-3 px-4 mb-0">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-cake-candles fs-5 text-danger"></i>
                        <span class="fw-medium text-dark">Please enter your birthday to use the goal feature.</span>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-danger rounded-3 px-4 fw-semibold shadow-sm">
                        Enter Birthday
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="container mt-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Overall Progress Card --}}
            <div class="card shadow-sm rounded-4 p-4 mb-5">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background-color: {{ $category->color->code ?? '#6366F1' }}20;">
                            <i class="fa-solid {{ $category->icon->class ?? 'fa-folder' }}"
                               style="color: {{ $category->color->code ?? '#6366F1' }};"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $category->name }} Overall Progress</div>
                            <div class="text-muted small">{{ $category->goals->count() }} {{ Str::plural('goal', $category->goals->count()) }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold fs-5" style="color: {{ $category->color->code ?? '#6366F1' }};">
                            {{ $overallProgress }}%
                        </span>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 8px; border-radius: 10px;">
                    <div class="progress-bar"
                         role="progressbar"
                         style="width: {{ $overallProgress }}%; background-color: {{ $category->color->code ?? '#6366F1' }};"
                         aria-valuenow="{{ $overallProgress }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>
            </div>

            {{-- Goals Timeline Section --}}
            <h5 class="fw-semibold mb-4">{{ $category->name }} Goals</h5>

            @if($goalsByDecade->isEmpty())
                <div class="text-muted">No goals found. Add your first goal!</div>
            @else
                <div class="timeline-container" style="position: relative;">
                    {{-- Vertical line --}}
                    <div style="position: absolute; left: 30px; top: 0; bottom: 0; width: 2px; background: #E5E7EB; z-index: 0;"></div>

                    @foreach($goalsByDecade as $decade => $goals)
                        <div class="mb-5" style="position: relative;">
                            {{-- Decade label with dot on timeline --}}
                            <div class="d-flex align-items-center mb-3" style="position: relative;">
                                <div style="width: 62px; flex-shrink: 0; position: relative; z-index: 1;">
                                    <div style="width: 12px; height: 12px; background: #D1D5DB; border-radius: 50%; margin: 0 auto;"></div>
                                </div>
                                <span class="fw-semibold text-muted">{{ $decade }}</span>
                            </div>

                            {{-- Goals in this decade --}}
                            @foreach($goals as $goal)
                                @php
                                    $milestonesTotal = $goal->milestones->count();
                                    $milestonesCompleted = $goal->milestones->filter(fn($m) => !is_null($m->completed_at))->count();
                                    $goalProgress = $milestonesTotal > 0 ? round(($milestonesCompleted / $milestonesTotal) * 100) : 0;
                                    $latestDue = $goal->milestones->sortByDesc('due_date')->first();
                                @endphp
                                <div class="d-flex align-items-start mb-3" style="position: relative;">
                                    {{-- Spacer matching the timeline --}}
                                    <div style="width: 62px; flex-shrink: 0;"></div>

                                    {{-- Goal Card --}}
                                    <div class="card shadow-sm rounded-4 p-3 flex-grow-1 text-decoration-none text-dark goal-card-hover position-relative">
                                        <a href="{{ route('lifeplan.goal.show', $goal) }}" class="stretched-link"></a>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; background-color: {{ $category->color->code ?? '#6366F1' }}20; flex-shrink: 0;">
                                                    <i class="fa-solid {{ $category->icon->class ?? 'fa-folder' }} small"
                                                       style="color: {{ $category->color->code ?? '#6366F1' }};"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold small">{{ $goal->title }}</div>
                                                    <div class="text-muted" style="font-size: 0.78rem;">
                                                        {{ $milestonesCompleted }}/{{ $milestonesTotal }} {{ Str::plural('milestone', $milestonesTotal) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($goal->target_date)
                                                    <span class="text-muted" style="font-size: 0.78rem;">
                                                        <i class="fa-regular fa-calendar me-1"></i>
                                                        {{ $goal->target_date->format('M Y') }}
                                                    </span>
                                                @elseif($latestDue)
                                                    <span class="text-muted" style="font-size: 0.78rem;">
                                                        <i class="fa-regular fa-calendar me-1"></i>
                                                        {{ $latestDue->due_date->format('M Y') }}
                                                    </span>
                                                @endif
                                                
                                                <!-- Dropdown menu -->
                                                <div class="dropdown position-relative" style="z-index: 5;">
                                                    <button class="btn btn-link text-muted p-0 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editGoalModal{{ $goal->id }}">Edit</a></li>
                                                        <li>
                                                            <form action="{{ route('lifeplan.goal.destroy', $goal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this goal?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Progress bar --}}
                                        <div class="progress mt-1" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar"
                                                 role="progressbar"
                                                 style="width: {{ $goalProgress }}%; background-color: {{ $category->color->code ?? '#6366F1' }};"
                                                 aria-valuenow="{{ $goalProgress }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Edit Goal Modal -->
                                    @include('lifeplan.modals.edit-goal', ['goal' => $goal, 'userCategories' => $userCategories, 'userAge' => $userAge])
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>

@endsection

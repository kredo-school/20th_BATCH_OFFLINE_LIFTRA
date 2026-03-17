@extends('layouts.app')

@section('content')

{{-- Header with category color background --}}
<div style="background-color: {{ $category->color->code ?? '#6366F1' }}; padding: 32px 40px 60px; position: relative;">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('home') }}" class="text-white text-decoration-none">
                <i class="fa-solid fa-chevron-left fs-5"></i>
            </a>
            <h2 class="fw-bold text-white mb-0">{{ $category->name }}</h2>
        </div>
        <a href="#" class="btn btn-white rounded-3 px-4 fw-semibold" style="background: white; color: {{ $category->color->code ?? '#6366F1' }};">
            <i class="fa-solid fa-plus me-2"></i> Add Goals
        </a>
    </div>
</div>

<div class="container" style="position: relative; top: -40px;">
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
                            <div class="text-muted small">{{ $category->goals->count() }} goals</div>
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
                                    <div class="card shadow-sm rounded-4 p-3 flex-grow-1">
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
                                                        {{ $milestonesCompleted }}/{{ $milestonesTotal }} milestones
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($latestDue)
                                                    <span class="text-muted" style="font-size: 0.78rem;">
                                                        <i class="fa-regular fa-calendar me-1"></i>
                                                        {{ $latestDue->due_date->format('M Y') }}
                                                    </span>
                                                @endif
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

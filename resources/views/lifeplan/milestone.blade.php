@extends('layouts.app')

@push('styles')
<style>
    .milestone-card {
        transition: all 0.2s ease;
    }
    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        position: relative;
        z-index: 2;
    }
    .timeline-line {
        position: absolute;
        left: 5px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #f1f5f9;
        z-index: 1;
    }
    .event-item:last-child .timeline-line {
        display: none;
    }
    .milestone-badge {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 20px;
    }
    .action-item {
        padding: 6px 0;
    }
    .action-checkbox {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1.5px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .action-checkbox.checked {
        border-color: currentColor;
        background-color: currentColor;
    }
    .action-checkbox.checked i {
        color: white;
        font-size: 10px;
    }
</style>
@endpush

@section('content')

@include('lifeplan.modals.add-milestone')

{{-- Top Header Section --}}
<div style="background-color: {{ $category->color->code ?? '#6366F1' }}; padding: 24px 48px 80px; position: relative;">
    {{-- Top navigation row --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('lifeplan.category.show', $category) }}" class="text-white text-decoration-none">
            <i class="fa-solid fa-chevron-left fs-5"></i>
        </a>
        <button class="btn btn-white text-primary fw-semibold rounded-pill px-4" 
                style="background: white; border: none; font-size: 0.85rem;" 
                data-bs-toggle="modal" 
                data-bs-target="#addMilestoneModal">
            <i class="fa-solid fa-plus me-2"></i> Add Milestones
        </button>
    </div>

    {{-- Title and description row --}}
    <div>
        <div class="text-white opacity-75 small mb-1">{{ $category->name }}</div>
        <h2 class="fw-bold text-white mb-2" style="font-size: 1.75rem;">{{ $goal->title }}</h2>
        @if($goal->description)
            <p class="text-white opacity-75 small mb-0" style="max-width: 800px;">{{ $goal->description }}</p>
        @endif
    </div>
</div>

<div class="container" style="position: relative; top: -50px;">
    <div class="row justify-content-center">
        <div class="col-lg-11">

            {{-- Summary Stats Bar (Progress Card) --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4 px-5">
                    <div class="d-flex justify-content-around align-items-center mb-4 text-center">
                        <div>
                            <div class="h5 fw-bold mb-0">{{ $milestonesCompleted }}/{{ $milestonesTotal }}</div>
                            <div class="text-muted small">Milestones</div>
                        </div>
                        <div style="width: 1px; height: 30px; background: #EEE;"></div>
                        <div>
                            <div class="h5 fw-bold mb-0">{{ $tasksCompleted }}/{{ $tasksTotal }}</div>
                            <div class="text-muted small">Tasks</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="progress flex-grow-1" style="height: 10px; border-radius: 20px; background-color: #f1f5f9;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $goalProgress }}%; background-color: {{ $category->color->code ?? '#6366F1' }}; border-radius: 20px;" 
                                 aria-valuenow="{{ $goalProgress }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="fw-bold" style="color: {{ $category->color->code ?? '#6366F1' }}; min-width: 40px; text-align: right;">
                            {{ $goalProgress }}%
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab selector (match screenshot style) --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 p-1" style="max-width: 600px; margin: 0 auto; background: #F3F4F6;">
                <div class="d-flex">
                    <button class="btn btn-white flex-grow-1 border-0 shadow-sm fw-semibold rounded-3 py-2" style="font-size: 0.9rem;">Milestones</button>
                    <button class="btn btn-link flex-grow-1 text-muted text-decoration-none fw-semibold" style="font-size: 0.9rem;">Timeline</button>
                </div>
            </div>

            <div class="row g-5">
                {{-- LEFT: Milestones --}}
                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-3">
                        @forelse($milestones as $milestone)
                            @php
                                $isDone = !is_null($milestone->completed_at);
                                $mActions = $milestone->actions;
                                $mActionsDone = $mActions->where('completed', true)->count();
                                $mActionsTotal = $mActions->count();
                            @endphp
                            <div class="card shadow-sm border-0 rounded-4 p-4 milestone-card">
                                <div class="d-flex gap-3 mb-3">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($isDone)
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 24px; height: 24px; background-color: #22c55e;">
                                                <i class="fa-solid fa-check text-white" style="font-size: 10px;"></i>
                                            </div>
                                        @else
                                            <div class="rounded-circle border" style="width: 24px; height: 24px; border-width: 2px !important;"></div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $milestone->title }}</h6>
                                        <div class="text-muted small">{{ $mActionsDone }}/{{ $mActionsTotal }} tasks completed</div>
                                    </div>
                                </div>

                                @if($mActions->isNotEmpty())
                                    <div class="ms-4 d-flex flex-column gap-2">
                                        @foreach($mActions as $action)
                                            <div class="d-flex align-items-center gap-2 action-item">
                                                <div class="action-checkbox {{ $action->completed ? 'checked' : '' }}" 
                                                     style="color: {{ $category->color->code ?? '#6366F1' }};">
                                                    @if($action->completed) <i class="fa-solid fa-check"></i> @endif
                                                </div>
                                                <span class="small {{ $action->completed ? 'text-decoration-line-through text-muted' : '' }}">
                                                    {{ $action->title }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                No milestones found for this goal.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT: Timeline --}}
                <div class="col-lg-6 offset-lg-1">
                    <div class="ps-4">
                        @forelse($timelineEvents as $event)
                            <div class="event-item d-flex gap-4 mb-4 position-relative">
                                <div class="timeline-line"></div>
                                <div class="flex-shrink-0" style="margin-top: 5px;">
                                    <div class="timeline-dot" style="background-color: {{ $event['is_milestone'] ? '#22c55e' : '#6366f1' }};"></div>
                                </div>
                                <div class="flex-grow-1 pb-4">
                                    <div class="text-muted small fw-semibold mb-1">{{ $event['date']->format('M j, Y') }}</div>
                                    <div class="{{ $event['is_milestone'] ? 'fw-bold' : 'small text-dark' }}">
                                        @if($event['is_milestone'])
                                            Milestone: {{ $event['title'] }} completed
                                        @else
                                            Completed '{{ $event['title'] }}'
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                             <div class="event-item d-flex gap-4 mb-4 position-relative">
                                <div class="timeline-line"></div>
                                <div class="flex-shrink-0" style="margin-top: 5px;">
                                    <div class="timeline-dot" style="background-color: #cbd5e1;"></div>
                                </div>
                                <div class="flex-grow-1 pb-4 text-muted">
                                    <div class="text-muted small fw-semibold mb-1">{{ $goal->created_at->format('M j, Y') }}</div>
                                    <div class="small">Goal created</div>
                                </div>
                            </div>
                        @endforelse

                        @if($timelineEvents->isNotEmpty())
                            <div class="event-item d-flex gap-4 position-relative">
                                <div class="timeline-line"></div>
                                <div class="flex-shrink-0" style="margin-top: 5px;">
                                    <div class="timeline-dot" style="background-color: #cbd5e1;"></div>
                                </div>
                                <div class="flex-grow-1 text-muted">
                                    <div class="text-muted small fw-semibold mb-1">{{ $goal->created_at->format('M j, Y') }}</div>
                                    <div class="small">Goal created</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

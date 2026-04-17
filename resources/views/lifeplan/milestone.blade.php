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

    .clickable-checkbox {
        cursor: pointer;
        transition: transform 0.1s;
    }

    .clickable-checkbox:active {
        transform: scale(0.9);
    }

    @media (max-width: 991.98px) {
        .hide-on-mobile {
            display: none !important;
        }
    }

    .milestone-card.completed-milestone {
        opacity: 0.75;
    }

    .milestone-card.completed-milestone h6 {
        color: #9ca3af !important;
        text-decoration: line-through;
    }

    .timeline-connector {
        position: absolute;
        left: 5px;
        top: 14px;
        bottom: -16px;
        width: 2px;
        background: #e2e8f0;
        z-index: 1;
    }

    .event-item:last-child .timeline-connector {
        display: none;
    }

    .milestone-due,
    .action-due {
        font-size: 0.72rem;
        color: #94a3b8;
    }

    .action-due {
        margin-left: auto;
        white-space: nowrap;
    }

    /* Custom uncheck overlay */
    #uncheckOverlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }

    #uncheckOverlay.show {
        display: flex;
    }

    #uncheckOverlay .overlay-box {
        background: white;
        border-radius: 20px;
        padding: 32px 28px;
        max-width: 340px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .milestone-top-header {
        padding: 24px 48px;
    }

    @media (max-width: 991.98px) {
        .milestone-top-header {
            padding: 24px 24px 24px 75px !important;
        }
    }
</style>
@endpush

@section('content')

@include('lifeplan.modals.add-milestone')

{{-- Top Header Section --}}
<div class="milestone-top-header"
    style="background-color: {{ $category->color->code ?? '#6366F1' }}; position: relative;">
    <div class="d-flex align-items-start gap-3">
        {{-- Top navigation row (PC only) --}}
        <div class="d-none d-lg-block flex-shrink-0 pt-1">
            <a href="{{ route('lifeplan.category.show', $category) }}" class="text-white text-decoration-none">
                <i class="fa-solid fa-chevron-left fs-5"></i>
            </a>
        </div>

        {{-- Title and description row --}}
        <div class="flex-grow-1 w-100">
            <div class="text-white opacity-75 small mb-1">{{ $category->name }}</div>

            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold text-white mb-2 pe-3" style="font-size: 1.75rem;">{{ $goal->title }}</h2>

                <div class="flex-shrink-0 d-flex align-items-center gap-2 mb-2" id="tour-add-milestone-wrapper">
                    <!-- Desktop Button -->
                    <button class="btn btn-white rounded-3 px-4 fw-semibold shadow-sm d-none d-md-inline-block"
                        style="background: white; color: {{ $category->color->code ?? '#6366F1' }};" data-bs-toggle="modal"
                        data-bs-target="#addMilestoneModal">
                        <i class="fa-solid fa-plus me-2"></i> {{ __('Add Milestones') }}
                    </button>
                    <!-- Mobile Button -->
                    <button class="btn btn-white shadow-sm d-md-none d-flex align-items-center justify-content-center p-0"
                        style="background: white; color: {{ $category->color->code ?? '#6366F1' }}; width: 42px; height: 42px; border-radius: 12px;"
                        data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                        <i class="fa-solid fa-plus m-0" style="font-size: 1.1rem;"></i>
                    </button>
                </div>
            </div>

            @if($goal->description)
            <p class="text-white opacity-75 small mb-0 mt-1" style="max-width: 800px;">{{ $goal->description }}</p>
            @endif
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-md-5 mt-4">
    <div class="row justify-content-center mt-3">
        <div class="col-12">
            {{-- Summary Stats Bar (Progress Card) --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4 px-5">
                    <div class="d-flex justify-content-around align-items-center mb-4 text-center">
                        <div>
                            <div class="h5 fw-bold mb-0">{{ $milestonesCompleted }}/{{ $milestonesTotal }}</div>
                            <div class="text-muted small">{{ __('Milestones') }}</div>
                        </div>
                        <div style="width: 1px; height: 30px; background: #EEE;"></div>
                        <div>
                            <div class="h5 fw-bold mb-0">{{ $tasksCompleted }}/{{ $tasksTotal }}</div>
                            <div class="text-muted small">{{ __('Tasks') }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="progress flex-grow-1"
                            style="height: 10px; border-radius: 20px; background-color: #f1f5f9;">
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $goalProgress }}%; background-color: {{ $category->color->code ?? '#6366F1' }}; border-radius: 20px;"
                                aria-valuenow="{{ $goalProgress }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="fw-bold"
                            style="color: {{ $category->color->code ?? '#6366F1' }}; min-width: 40px; text-align: right;">
                            {{ $goalProgress }}%
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab selector (match screenshot style) --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 p-1 d-lg-none"
                style="max-width: 600px; margin: 0 auto; background: #F3F4F6;">
                <div class="d-flex" id="viewToggleButtons">
                    <button class="btn btn-white flex-grow-1 border-0 shadow-sm fw-semibold rounded-3 py-2"
                        style="font-size: 0.9rem;" onclick="switchView('milestones')">{{ __('Milestones') }}</button>
                    <button class="btn btn-link flex-grow-1 text-muted text-decoration-none fw-semibold"
                        style="font-size: 0.9rem;" onclick="switchView('timeline')">{{ __('Timeline') }}</button>
                </div>
            </div>


            <div class="row g-5">
                {{-- LEFT: Milestones --}}
                <div class="col-lg-5" id="tour-milestones-column">
                    <div class="d-none d-lg-block mb-3 px-2">
                        <div class="fw-semibold text-muted"
                            style="font-size: 0.75rem; letter-spacing: 0.06em; text-transform: uppercase;">{{ __('Milestones') }}</div>
                    </div>
                    <div class="d-flex flex-column gap-3" id="milestonesView">
                        @forelse($milestones as $milestone)
                        @php
                        $isDone = !is_null($milestone->completed_at);
                        $mActions = $milestone->actions;
                        $mActionsDone = $mActions->where('completed', true)->count();
                        $mActionsTotal = $mActions->count();
                        $canComplete = $mActionsTotal == 0 || $mActionsDone === $mActionsTotal;
                        @endphp
                        <div
                            class="card shadow-sm border-0 rounded-4 p-4 milestone-card {{ $isDone ? 'completed-milestone' : '' }}">
                            <div class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0 mt-1 {{ $isDone || $canComplete ? 'clickable-checkbox' : '' }}"
                                    style="{{ !$isDone && !$canComplete ? 'cursor: not-allowed; opacity: 0.5;' : '' }}"
                                    onclick="{{ !$isDone && !$canComplete ? 'alert(\''.__('Please complete all associated actions before marking this milestone as completed.').'\')' : 'onMilestoneClick('.$milestone->id.', '.($isDone ? 'true' : 'false').')' }}">
                                    @if($isDone)
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 24px; height: 24px; background-color: #22c55e;">
                                        <i class="fa-solid fa-check text-white" style="font-size: 10px;"></i>
                                    </div>
                                    @else
                                    <div class="rounded-circle border"
                                        style="width: 24px; height: 24px; border-width: 2px !important; border-color: #cbd5e1;">
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $milestone->title }}</h6>
                                    @if($milestone->due_date)
                                    <div class="milestone-due mt-1"><i class="fa-regular fa-calendar me-1"></i>{{ __('Due') }} {{
                                        $milestone->due_date->format('M j, Y') }}</div>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0 text-decoration-none" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-0 border-0 shadow-sm rounded-3" style="min-width: 120px;">
                                        <li><a class="dropdown-item btn btn-light text-secondary py-1" href="#" data-bs-toggle="modal" data-bs-target="#editMilestone{{ $milestone->id }}"><i class="fa-solid fa-pen-to-square me-2"></i>{{ __('Edit') }}</a></li>
                                        <li>
                                            <a class="dropdown-item btn btn-light text-danger py-1" href="#" data-bs-toggle="modal" data-bs-target="#deleteMilestoneModal{{ $milestone->id }}"><i class="fa-solid fa-trash-can me-2"></i>{{ __('Delete') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            @include('lifeplan.modals.edit-milestone')
                            @include('lifeplan.modals.delete-milestone', ['milestone' => $milestone])

                            @if($mActions->isNotEmpty())
                            <div class="ms-4 d-flex flex-column gap-2">
                                @foreach($mActions as $action)
                                <div class="d-flex align-items-center gap-2 action-item">
                                    <div class="action-checkbox clickable-checkbox {{ $action->completed ? 'checked' : '' }}"
                                        style="color: {{ $category->color->code ?? '#6366F1' }};"
                                        onclick="toggleAction({{ $action->id }})">
                                        @if($action->completed) <i class="fa-solid fa-check"></i> @endif
                                    </div>
                                    <span
                                        class="small {{ $action->completed ? 'text-decoration-line-through text-muted' : '' }}">
                                        {{ $action->title }}
                                    </span>
                                    @if($action->due_date)
                                    <span class="action-due"><i class="fa-regular fa-calendar me-1"></i>{{
                                        $action->due_date->format('M j') }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted">
                            {{ __('No milestones found for this goal.') }}
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT: Timeline --}}
                <div class="col-lg-6 offset-lg-1 hide-on-mobile" id="tour-timeline-column">
                    <div class="d-none d-lg-block mb-3 px-2">
                        <div class="fw-semibold text-muted"
                            style="font-size: 0.75rem; letter-spacing: 0.06em; text-transform: uppercase;">{{ __('Timeline') }}</div>
                    </div>
                    <div class="ps-4" id="timelineView">
                        @forelse($timelineEvents as $event)
                        <div class="event-item d-flex gap-4 mb-4 position-relative">
                            <div class="flex-shrink-0 position-relative" style="margin-top: 5px;">
                                <div class="timeline-dot"
                                    style="background-color: {{ $event['is_milestone'] ? '#22c55e' : '#6366f1' }};">
                                </div>
                                <div class="timeline-connector"></div>
                            </div>
                            <div class="flex-grow-1 pb-4">
                                <div class="text-muted small fw-semibold mb-1">{{ $event['date']->format('M j, Y') }}
                                </div>
                                <div class="{{ $event['is_milestone'] ? 'fw-bold' : 'small text-dark' }}">
                                    @if($event['is_milestone'])
                                    {{ __('Milestone') }}: {{ $event['title'] }} {{ __('completed') }}
                                    @else
                                    {{ __('Completed') }} '{{ $event['title'] }}'
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="event-item d-flex gap-4 mb-4 position-relative">
                            <div class="flex-shrink-0 position-relative" style="margin-top: 5px;">
                                <div class="timeline-dot" style="background-color: #cbd5e1;"></div>
                            </div>
                            <div class="flex-grow-1 pb-4 text-muted">
                                <div class="text-muted small fw-semibold mb-1">{{ $goal->created_at->format('M j, Y') }}
                                </div>
                                <div class="small">{{ __('Goal created') }}</div>
                            </div>
                        </div>
                        @endforelse

                        @if($timelineEvents->isNotEmpty())
                        <div class="event-item d-flex gap-4 position-relative">
                            <div class="flex-shrink-0 position-relative" style="margin-top: 5px;">
                                <div class="timeline-dot" style="background-color: #cbd5e1;"></div>
                            </div>
                            <div class="flex-grow-1 text-muted">
                                <div class="text-muted small fw-semibold mb-1">{{ $goal->created_at->format('M j, Y') }}
                                </div>
                                <div class="small">{{ __('Goal created') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Custom Uncheck Confirmation Overlay --}}
<div id="uncheckOverlay">
    <div class="overlay-box">
        <div class="mb-3 text-danger">
            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
        </div>
        <h5 class="fw-bold text-dark mb-3">{{ __('Unmark Milestone') }}</h5>
        <p class="text-muted mb-4 small">{{ __('This will mark the milestone as') }} <strong>{{ __('not yet completed') }}</strong><br>{{ __('and remove its timestamp from the Timeline history.') }}</p>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light flex-grow-1 rounded-pill fw-semibold text-muted shadow-sm border" onclick="hideUncheckOverlay()">{{ __('Cancel') }}</button>
            <button type="button" class="btn btn-danger flex-grow-1 rounded-pill fw-bold shadow-sm" onclick="confirmUncheck()">{{ __('Yes, Unmark') }}</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function switchView(view) {
        const mBtn = document.querySelectorAll('#viewToggleButtons button')[0];
        const tBtn = document.querySelectorAll('#viewToggleButtons button')[1];
        const mCol = document.getElementById('tour-milestones-column');
        const tCol = document.getElementById('tour-timeline-column');

        if (view === 'milestones') {
            mBtn.className = "btn btn-white flex-grow-1 border-0 shadow-sm fw-semibold rounded-3 py-2";
            tBtn.className = "btn btn-link flex-grow-1 text-muted text-decoration-none fw-semibold";
            mCol.classList.remove('hide-on-mobile');
            tCol.classList.add('hide-on-mobile');
        } else {
            tBtn.className = "btn btn-white flex-grow-1 border-0 shadow-sm fw-semibold rounded-3 py-2";
            mBtn.className = "btn btn-link flex-grow-1 text-muted text-decoration-none fw-semibold";
            mCol.classList.add('hide-on-mobile');
            tCol.classList.remove('hide-on-mobile');
        }
    }

    let pendingUncheckId = null;

    function onMilestoneClick(id, isDone) {
        if (isDone) {
            pendingUncheckId = id;
            document.getElementById('uncheckOverlay').classList.add('show');
        } else {
            toggleMilestone(id);
        }
    }

    function hideUncheckOverlay() {
        document.getElementById('uncheckOverlay').classList.remove('show');
        pendingUncheckId = null;
    }

    function confirmUncheck() {
        document.getElementById('uncheckOverlay').classList.remove('show');
        if (pendingUncheckId !== null) {
            toggleMilestone(pendingUncheckId);
            pendingUncheckId = null;
        }
    }

    function toggleMilestone(id) {
        fetch(`/lifeplan/milestone/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                window.location.reload();
            } else if (data.message) {
                alert(data.message);
            }
        }).catch(err => console.error(err));
    }

    function toggleAction(id) {
        fetch(`/lifeplan/action/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
</script>
@endpush
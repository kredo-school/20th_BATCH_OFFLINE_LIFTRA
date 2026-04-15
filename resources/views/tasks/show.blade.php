@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/task.css') }}">
    <style>
        .detail-badge {
            font-size: 0.8rem;
        }

        .detail-section-label {
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #9ca3af;
            font-weight: 600;
        }

        .detail-card {
            border-radius: 1rem;
        }

        .task-content-area {
            min-height: 120px;
        }
    </style>
@endpush

@section('content')

    <div class="page-header shadow-sm mt-0 mx-0 w-100">
        <div class="container-fluid px-2 px-md-4">
            <div class="d-flex align-items-center">

                <a href="{{ route('tasks.index') }}"
                    class="text-white text-decoration-none d-none d-lg-inline-block me-3 ms-2">
                    <i class="fa-solid fa-chevron-left fs-5"></i>
                </a>

                <div>
                    <h1 class="mb-0 fw-bold">{{ __('Task Detail') }}</h1>
                    <p class="mb-0 small text-white">You can check details of your task </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3 px-md-5 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">

                {{-- Main Card --}}
                <div
                    class="card detail-card shadow-sm mb-4
                @if ($task->priority_type === 1) border-danger border-2
                @elseif($task->priority_type === 2) border-warning border-2
                @elseif($task->priority_type === 3) border-info border-2
                @elseif($task->priority_type === 4) border-success border-2 @endif">

                    <div class="card-body p-4">

                        {{-- Header Row --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3
                                    class="fw-bold mb-1 {{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}">
                                    {{ $task->title }}
                                </h3>
                                <span class="badge {{ $task->priority_class }} detail-badge rounded-pill">
                                    {{ $task->priority_label }}
                                </span>
                                @if ($task->completed)
                                    <span class="badge bg-success rounded-pill ms-1 detail-badge">✓
                                        {{ __('Completed') }}</span>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex gap-2">
                                <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $task->completed ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                        <i
                                            class="fa-solid {{ $task->completed ? 'fa-rotate-left' : 'fa-check' }} me-1"></i>
                                        <span
                                            class="d-none d-md-inline">{{ $task->completed ? __('Undo') : __('Complete') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <hr>

                        {{-- Date Info --}}
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-4">
                                <div class="detail-section-label">{{ __('Due Date') }}</div>
                                <div class="fw-semibold">{{ $task->due_date ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="detail-section-label">{{ __('Start Date') }}</div>
                                <div class="fw-semibold">{{ $task->start_date ?? '—' }}</div>
                            </div>
                            @if ($task->task_time)
                                <div class="col-6 col-md-4">
                                    <div class="detail-section-label">{{ __('Time') }}</div>
                                    <div class="fw-semibold">{{ $task->task_time }}</div>
                                </div>
                            @endif
                        </div>

                        {{-- Repeat Info --}}
                        @if ($task->repeat_type)
                            <div class="rounded-3 bg-light p-3 mb-4">
                                <div class="detail-section-label mb-2"><i
                                        class="fa-solid fa-repeat me-1"></i>{{ __('Repeat Settings') }}</div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted">{{ __('Type') }}</small>
                                        <div class="fw-semibold">
                                            {{ [1 => __('Daily'), 2 => __('Weekly'), 3 => __('Monthly')][$task->repeat_type] ?? '—' }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">{{ __('Interval') }}</small>
                                        <div class="fw-semibold">{{ __('Every') }} {{ $task->repeat_interval ?? 1 }}
                                            {{ [1 => __('day(s)'), 2 => __('week(s)'), 3 => __('month(s)')][$task->repeat_type] ?? '' }}
                                        </div>
                                    </div>
                                    @if ($task->days_of_week)
                                        <div class="col-12">
                                            <small class="text-muted">{{ __('Days') }}</small>
                                            <div class="fw-semibold">
                                                @php
                                                    $days = is_string($task->days_of_week)
                                                        ? json_decode($task->days_of_week, true)
                                                        : $task->days_of_week;
                                                    $dayNames = [
                                                        'sun' => __('Sun'),
                                                        'mon' => __('Mon'),
                                                        'tue' => __('Tue'),
                                                        'wed' => __('Wed'),
                                                        'thu' => __('Thu'),
                                                        'fri' => __('Fri'),
                                                        'sat' => __('Sat'),
                                                    ];
                                                @endphp
                                                @foreach ((array) $days as $d)
                                                    <span
                                                        class="badge bg-secondary rounded-pill me-1">{{ $dayNames[strtolower($d)] ?? $d }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if ($task->day_of_month)
                                        <div class="col-12">
                                            <small class="text-muted">{{ __('Day of Month') }}</small>
                                            <div class="fw-semibold">{{ __('Day :day', ['day' => $task->day_of_month]) }}
                                            </div>
                                        </div>
                                    @endif
                                    @if ($task->end_date)
                                        <div class="col-6">
                                            <small class="text-muted">{{ __('End Date') }}</small>
                                            <div class="fw-semibold">{{ $task->end_date }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    {{-- Description --}}
                    <div class="detail-section-label mb-2">{{ __('Description') }}</div>
                    <div class="task-content-area text-muted">
                        @if($task->description)
                            {!! nl2br(e($task->description)) !!}
                        @else
                            <span class="fst-italic text-muted">{{ __('No description provided.') }}</span>
                        @endif
                    </div>

                </div>
            </div>

                {{-- Edit & Delete --}}
                <div class="text-end">
                    <a href="#" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#editTaskModal{{ $task->id }}">
                        <i class="fa-solid fa-pen-to-square me-1"></i>
                        <span class="">{{ __('Edit') }}</span>
                    </a>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#deleteTaskModal{{ $task->id }}">
                        <i class="fa-solid fa-trash-can me-1"></i> {{ __('Delete') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    @include('tasks.modals.edit-task', ['task' => $task])
    @include('tasks.modals.delete-task', ['task' => $task])

@endsection

<div id="daily-dashboard-fragment" class="animate-fade-in">
    <div class="mb-2">
        <h5 class="fw-bold d-flex align-items-center gap-2">
            <i class="fa-regular fa-calendar text-primary"></i> 
            {{ $selectedDate->format('l, F j, Y') }}
        </h5>
    </div>

    <!-- Dashboard Columns -->
    <div class="row g-3 g-md-4 align-items-stretch">
        <!-- Google Calendar Events (Mobile Only) -->
        <div class="col-12 d-md-none d-flex flex-column">
            <div class="content-card flex-grow-1 {{ $googleEvents->isEmpty() ? 'empty-card' : '' }}">
                <div class="dashboard-section-header">
                    <div class="dashboard-section-title">
                        <span class="dot dot-purple"></span> {{ __('Google Calendar') }}
                    </div>
                </div>
                @forelse($googleEvents as $event)
                    <div class="item-row">
                        <div class="google-item-compact">
                            <div class="item-title">
                                {{ $event->title }}
                                @if($event->start_date && \Carbon\Carbon::parse($event->start_date)->format('H:i:s') !== '00:00:00')
                                    <span class="item-meta text-muted ms-2" style="font-weight: normal;">{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-2" style="font-size: 0.85rem;">{{ __('No Google events') }}</div>
                @endforelse
            </div>
        </div>

        <!-- Actions -->
        <div class="col-12 col-lg-4 d-flex flex-column">
            <div class="content-card flex-grow-1 {{ $actions->isEmpty() ? 'empty-card' : '' }}">
                <div class="dashboard-section-header">
                    <div class="dashboard-section-title">
                        <span class="dot dot-blue"></span> {{ __('Actions') }}
                    </div>
                </div>
                @forelse($actions as $action)
                    @php
                        $isMilestoneAction = $action instanceof \App\Models\MilestoneAction;
                        $isChecked = $isMilestoneAction 
                            ? $action->logs()->whereDate('date', $selectedDate)->where('is_completed', true)->exists()
                            : $action->completed;
                    @endphp
                    <div class="item-row">
                        <input type="checkbox" class="item-checkbox action-toggle-checkbox"
                               data-id="{{ $action->id }}"
                               data-type="{{ $isMilestoneAction ? 'milestone-action' : 'action' }}"
                               data-date="{{ $selectedDate->format('Y-m-d') }}"
                               {{ $isChecked ? 'checked' : '' }}>
                        <div>
                            <div class="item-title {{ $isChecked ? 'text-decoration-line-through text-muted' : '' }}">{{ $action->title }}</div>
                            <div class="item-meta">
                                <a href="{{ route('lifeplan.goal.show', $action->milestone->goal_id) }}" class="text-primary text-decoration-none d-inline-flex align-items-center gap-1 dashboard-label-link" style="font-size: 0.75rem; font-weight: 600; opacity: 0.7;">
                                    {{ $action->milestone->goal->title ?? __('Goal') }}
                                    <i class="fa-solid fa-chevron-right" style="font-size: 0.6rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-2" style="font-size: 0.85rem;">{{ __('No actions scheduled') }}</div>
                @endforelse
            </div>
        </div>

        <!-- Tasks -->
        <div class="col-12 col-lg-4 d-flex flex-column">
            <div class="content-card flex-grow-1 {{ $tasks->isEmpty() ? 'empty-card' : '' }}">
                <div class="dashboard-section-header">
                    <div class="dashboard-section-title">
                        <span class="dot dot-green"></span> {{ __('Tasks') }}
                    </div>
                    <a href="#" class="text-success text-decoration-none" data-bs-toggle="modal" data-bs-target="#add-task">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </div>
                @forelse($tasks as $task)
                    <div class="item-row">
                        <input type="checkbox" class="item-checkbox task-checkbox"
                               data-task-id="{{ $task->id }}"
                               data-completed="{{ $task->completed ? 'true' : 'false' }}"
                               {{ $task->completed ? 'checked' : '' }}>
                        <div>
                            <div class="item-title {{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}">{{ $task->title }}</div>
                            @if($task->priority_type)
                                <span class="priority-badge {{ $task->priority_class }}">
                                    {{ $task->priority_label }}
                                </span>
                            @endif
                        </div>
                        <div class="ms-auto dropdown">
                            <a href="#" class="text-muted text-decoration-none px-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 text-dark" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">
                                        <i class="fa-solid fa-pen text-muted" style="width: 16px;"></i> {{ __('Edit') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}">
                                        <i class="fa-solid fa-trash-can" style="width: 16px;"></i> {{ __('Delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @include('tasks.modals.edit-task', ['task' => $task])
                    @include('tasks.modals.delete-task', ['task' => $task])
                @empty
                    <div class="text-muted text-center py-2" style="font-size: 0.85rem;">{{ __('No tasks for today') }}</div>
                @endforelse
            </div>
        </div>

        <!-- Habits -->
        <div class="col-12 col-lg-4 d-flex flex-column">
            <div class="content-card flex-grow-1 {{ $habits->isEmpty() ? 'empty-card' : '' }}">
                <div class="dashboard-section-header">
                    <div class="dashboard-section-title">
                        <span class="dot dot-orange"></span> {{ __('Habits') }}
                    </div>
                    <a href="#" class="text-warning text-decoration-none" data-bs-toggle="modal" data-bs-target="#addHabitModal">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </div>
                @forelse($habits as $habit)
                    @php
                        $isCompleted = $habit->logs()->whereDate('date', $selectedDate)->where('is_completed', true)->exists();
                    @endphp
                    <div class="item-row">
                        <input type="checkbox" class="item-checkbox habit-checkbox" 
                               data-habit-id="{{ $habit->id }}"
                               data-date="{{ $selectedDate->format('Y-m-d') }}"
                               {{ $isCompleted ? 'checked' : '' }}>
                        <div>
                            <div class="item-title {{ $isCompleted ? 'text-decoration-line-through text-muted' : '' }}">{{ $habit->title }}</div>
                            <div class="item-meta">{{ $habit->time_text ?? ($habit->habit_time ? \Carbon\Carbon::parse($habit->habit_time)->format('H:i') : __('All Day')) }}</div>
                        </div>
                        <div class="ms-auto dropdown">
                            <a href="#" class="text-muted text-decoration-none px-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 text-dark" href="#" data-bs-toggle="modal" data-bs-target="#editHabitModal{{ $habit->id }}">
                                        <i class="fa-solid fa-pen text-muted" style="width: 16px;"></i> {{ __('Edit') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteHabitModal{{ $habit->id }}">
                                        <i class="fa-solid fa-trash-can" style="width: 16px;"></i> {{ __('Delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @include('habits.modals.habit-edit', ['habit' => $habit])
                    @include('habits.modals.habit-delete', ['habit' => $habit])
                @empty
                    <div class="text-muted text-center py-2" style="font-size: 0.85rem;">{{ __('No habits scheduled') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

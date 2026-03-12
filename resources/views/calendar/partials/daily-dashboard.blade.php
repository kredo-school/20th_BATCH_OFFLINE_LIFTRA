<div id="daily-dashboard-fragment" class="animate-fade-in">
    <div class="mb-4">
        <h5 class="fw-bold d-flex align-items-center gap-2">
            <i class="fa-regular fa-calendar text-primary"></i> 
            {{ $selectedDate->format('l, F j, Y') }}
        </h5>
    </div>

    <!-- Dashboard Columns -->
    <div class="row g-4">
        <!-- Actions -->
        <div class="col-12 col-lg-4">
            <div class="dashboard-section-header">
                <div class="dashboard-section-title">
                    <span class="dot dot-blue"></span> Actions
                </div>
                <a href="#" class="text-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#addActionModal">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>
            <div class="content-card">
                @forelse($actions as $action)
                    <div class="item-row">
                        <input type="checkbox" class="item-checkbox">
                        <div>
                            <div class="item-title">{{ $action->title }}</div>
                            <div class="item-meta">{{ $action->milestone->goal->title ?? 'Goal' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-5">No actions scheduled</div>
                @endforelse
            </div>
        </div>

        <!-- Tasks -->
        <div class="col-12 col-lg-4">
            <div class="dashboard-section-header">
                <div class="dashboard-section-title">
                    <span class="dot dot-green"></span> Tasks
                </div>
                <a href="#" class="text-success text-decoration-none" data-bs-toggle="modal" data-bs-target="#add-task">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>
            <div class="content-card">
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
                    </div>
                @empty
                    <div class="text-muted text-center py-5">No tasks for today</div>
                @endforelse
            </div>
        </div>

        <!-- Habits -->
        <div class="col-12 col-lg-4">
            <div class="dashboard-section-header">
                <div class="dashboard-section-title">
                    <span class="dot dot-orange"></span> Habits
                </div>
                <a href="#" class="text-warning text-decoration-none" data-bs-toggle="modal" data-bs-target="#addHabitModal">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>
            <div class="content-card">
                
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
                            <div class="item-meta">{{ $habit->time_text ?? ($habit->habit_time ? \Carbon\Carbon::parse($habit->habit_time)->format('H:i') : 'All Day') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-5">No habits scheduled</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

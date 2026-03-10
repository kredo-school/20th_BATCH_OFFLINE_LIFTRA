<div class="mb-3 fw-semibold">
    Today's Habits
</div>

@foreach($todayHabits as $habit)
@php
    $completed = isset($todayLogs[$habit->id]) && $todayLogs[$habit->id]->is_completed;
@endphp

<div class="card shadow-sm rounded-4 p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <input type="checkbox" class="habit-checkbox" data-habit-id="{{ $habit->id }}" data-date="{{ $selectedDate->format('Y-m-d') }}" {{ $completed ? 'checked' : '' }}>
            <div>
                <div class="{{ $completed ? 'text-decoration-line-through text-muted' : '' }}">
                    {{ $habit->title }}
                </div>
            </div>
        </div>
        <div class="small text-muted">{{ $habit->time_text }}</div>
    </div>
</div>
@endforeach
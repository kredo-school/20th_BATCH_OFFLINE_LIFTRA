@extends('layouts.app')
@include('habits.modals.habit-add')

@section('content')
<style>
    /* Default dot style */
    .habit-dot {
        background-color: #6366f1; /* primary color */
    }
    
    /* When a day is selected (dark blue background) */
    .date-selection-box.selected-day {
        background-color: #6366f1 !important;
        color: white !important;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    }
    
    /* Dots inside a selected day should be white */
    .date-selection-box.selected-day .habit-dot {
        background-color: white !important;
    }
    
    /* Today styling (light blue) - only applied if NOT selected */
    .date-selection-box.is-today:not(.selected-day) {
        background-color: rgba(99, 102, 241, 0.1) !important;
        color: #6366f1 !important;
    }

    /* All Habits Side Streak Box */
    .streak-side-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        background-color: rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.1);
        border-radius: 12px;
        color: #6366f1;
        flex-shrink: 0;
        line-height: 1.1;
    }

    .streak-side-box .streak-num {
        font-size: 1rem;
        font-weight: 800;
    }

    .streak-side-box i {
        font-size: 0.65rem;
        opacity: 0.7;
    }
</style>
<x-page-header 
title="Habits"
subtitle="Build consistency, one day at a time"
>
<button class="btn btn-light rounded-3 px-4 text-primary-6366F1" data-bs-toggle="modal" data-bs-target="#addHabitModal">
    <i class="fa-solid fa-plus text-primary-6366F1"></i>
    Add Habit
</button>
</x-page-header>

<div class="container mt-3">
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <ul class="mb-0 small fw-semibold">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<div class="container mt-4">
    <div class="row g-4">
        
        <!-- LEFT SIDE -->
        <div class="col-lg-7">

            <!-- WEEK CALENDAR -->
            <div class="card shadow-sm rounded-4 p-4 mb-4 border-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <a href="#" class="calendar-nav text-dark text-opacity-50" data-date="{{ $selectedDate->copy()->subWeek()->toDateString() }}">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                        
                        <div class="fw-bold fs-5">{{ $selectedDate->format('F Y') }}</div>
                        
                        <a href="#" class="calendar-nav text-dark text-opacity-50" data-date="{{ $selectedDate->copy()->addWeek()->toDateString() }}">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>

                    <a href="{{ route('habits.index') }}" class="btn btn-sm btn-light border-0 shadow-sm rounded-pill px-3 fw-semibold text-primary">Today</a>
                </div>

                <div class="d-flex justify-content-between">
                    @foreach($weekDates as $day)
                    <a href="#" class="calendar-day text-center text-decoration-none flex-grow-1" data-date="{{ $day->toDateString() }}">
                        <div class="small fw-bold {{ $day->isToday() ? 'text-primary' : 'text-muted' }} mb-1">{{ $day->format('D') }}</div>
                        @php
                            $isToday = $day->isToday();
                            $isSelected = $selectedDate->isSameDay($day);
                        @endphp
                        <div class="rounded-3 py-2 mx-1 date-selection-box {{ $isToday ? 'is-today' : '' }} {{ $isSelected ? 'selected-day' : '' }}">
                            <div>{{ $day->format('j') }}</div>
                            
                            <!-- HABIT INDICATORS -->
                            <div class="d-flex justify-content-center gap-1 mt-1" style="height: 4px;">
                                @php
                                    $count = $calendarCounts[$day->toDateString()] ?? 0;
                                    $displayDots = min($count, 3);
                                @endphp
                                @for($i=0; $i<$displayDots; $i++)
                                    <div class="rounded-circle habit-dot" style="width: 4px; height: 4px; opacity: {{ 0.4 + ($i * 0.2) }};"></div>
                                @endfor
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- TODAY HABITS -->
            <div id="todayHabitsArea">
                @include('habits.partials.today-habits')
            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-5">
            <div class="card shadow-sm rounded-4 p-4 border-0">
                <div class="fw-bold mb-4 fs-5 text-dark">All Habits</div>

                @foreach($habits as $habit)
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <!-- Left: Side Streak Box -->
                            <div class="streak-side-box shadow-sm">
                                <i class="fa-solid fa-fire"></i>
                                <span class="streak-num">{{ $habit->streak }}</span>
                            </div>

                            <!-- Right: Content Information -->
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $habit->title }}</div>
                                <div class="small text-muted mb-1">
                                    @php
                                        $typeText = $habit->repeat_type == 1 ? 'Daily' : ($habit->repeat_type == 2 ? 'Weekly' : 'Monthly');
                                        $intervalText = $habit->repeat_interval > 1 ? "Every {$habit->repeat_interval} {$typeText}" : $typeText;
                                        if ($habit->repeat_type == 3 && $habit->day_of_month) {
                                            $intervalText .= " (Day {$habit->day_of_month})";
                                        }
                                    @endphp
                                    <span class="badge bg-light text-dark fw-normal border">{{ $intervalText }}</span>
                                    @if($habit->repeat_type == 2 && $habit->days_of_week)
                                        <span class="ms-1">({{ implode(', ', (array)$habit->days_of_week) }})</span>
                                    @endif
                                    <span class="mx-1">•</span> {{ $habit->time_text }}
                                </div>
                                <div class="small text-muted" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i>
                                    {{ \Carbon\Carbon::parse($habit->start_date)->format('M d') }} - 
                                    {{ $habit->end_date ? \Carbon\Carbon::parse($habit->end_date)->format('M d, Y') : 'Ongoing' }}
                                </div>
                            </div>
                        </div>

                        <!-- Action Icons -->
                        <div class="d-flex gap-2">
                            <a href="#" class="text-dark" data-bs-toggle="modal" data-bs-target="#editHabitModal{{ $habit->id }}">
                                <i class="fa-solid fa-pen small"></i>
                            </a>
                            <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#deleteHabitModal{{ $habit->id }}">
                                <i class="fa-solid fa-trash-can small"></i>
                            </a>
                        </div>
                    </div>
                    @include('habits.modals.habit-edit', ['habit' => $habit])
                    @include('habits.modals.habit-delete', ['habit' => $habit])
                @endforeach

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e){

    // 日付クリック
    const dayEl = e.target.closest('.calendar-day');
    if(dayEl){
        e.preventDefault();
        const date = dayEl.dataset.date;

        // Visual Selection Update
        document.querySelectorAll('.date-selection-box.selected-day').forEach(el => {
            el.classList.remove('selected-day');
        });
        
        // Target the rounded div inside the clicked link
        const numberDiv = dayEl.querySelector('.date-selection-box');
        if (numberDiv) {
            numberDiv.classList.add('selected-day');
        }

        loadHabitsByDate(date);
        return;
    }

    // 前週 / 次週
    const navEl = e.target.closest('.calendar-nav');
    if(navEl){
        e.preventDefault();
        const date = navEl.dataset.date;
        window.location.href = `{{ route('habits.index') }}?date=${date}`;
        return;
    }

});

// 今日の日付を文字列として保持しておく (Y-m-d)
const TODAY_DATE = "{{ \Carbon\Carbon::today()->format('Y-m-d') }}";

// チェックボックスUI
document.addEventListener('change', function(e){
    if(!e.target.classList.contains('habit-checkbox')) return;

    const titleDiv = e.target.closest('div.d-flex').querySelector('div > div:first-child');
    if(!titleDiv) return;

    const habitId = e.target.dataset.habitId;
    const date = e.target.dataset.date;
    const isChecked = e.target.checked;

    // Validation for future dates
    if (date > TODAY_DATE) {
        alert("You cannot complete habits for future dates.");
        e.target.checked = !isChecked; // revert
        return;
    }

    // Validation for past dates
    if (date < TODAY_DATE) {
        const confirmPast = confirm("Do you want to complete/incomplete a habit for a past date?");
        if (!confirmPast) {
            e.target.checked = !isChecked; // revert
            return;
        }
    }

    if(isChecked){
        titleDiv.classList.add('text-decoration-line-through','text-muted');
    } else {
        titleDiv.classList.remove('text-decoration-line-through','text-muted');
    }

    e.target.disabled = true;

    fetch(`/habits/${habitId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ date: date })
    })
    .then(res => res.json())
    .then(data => {
        if(!data.success) throw new Error('Update failed');
        // reload the list to update streaks and states
        loadHabitsByDate(date);
    })
    .catch(err => {
        console.error(err);
        // Revert UI if fetch fails
        e.target.checked = !isChecked;
        e.target.disabled = false;
        if(!isChecked){
            titleDiv.classList.add('text-decoration-line-through','text-muted');
        } else {
            titleDiv.classList.remove('text-decoration-line-through','text-muted');
        }
        alert('Failed to save habit state.');
    });
});

// Ajaxで指定日付の習慣を取得
function loadHabitsByDate(date){
    fetch(`{{ route('habits.byDate') }}?date=${date}`, {
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
        .then(res => {
            if(!res.ok) throw new Error('Network error');
            return res.text();
        })
        .then(html => {
            document.getElementById('todayHabitsArea').innerHTML = html;
        })
        .catch(err => console.error(err));
}
</script>

@endsection
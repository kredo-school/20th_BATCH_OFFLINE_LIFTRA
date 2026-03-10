@extends('layouts.app')
@include('habits.modals.habit-add')

@section('content')
<x-page-header 
title="Habits"
subtitle="Build consistency, one day at a time"
>
<button class="btn btn-light rounded-3 px-4 text-primary-6366F1" data-bs-toggle="modal" data-bs-target="#addHabitModal">
    <i class="fa-solid fa-plus text-primary-6366F1"></i>
    Add Habit
</button>
</x-page-header>

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
                        <div class="rounded-3 py-2 mx-1 {{ $selectedDate->isSameDay($day) ? 'bg-primary text-white shadow-sm' : ($day->isToday() ? 'bg-primary bg-opacity-10 text-primary' : '') }}">
                            {{ $day->format('j') }}
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
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="flex-grow-1">
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
                                    <span class="ms-1">({{ implode(', ', json_decode($habit->days_of_week)) }})</span>
                                @endif
                                <span class="mx-1">•</span> {{ $habit->time_text }}
                            </div>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div class="small text-primary fw-semibold"><i class="fa-solid fa-fire me-1"></i>{{ $habit->streak }} day streak</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i>
                                    {{ \Carbon\Carbon::parse($habit->start_date)->format('M d') }} - 
                                    {{ $habit->end_date ? \Carbon\Carbon::parse($habit->end_date)->format('M d, Y') : 'Ongoing' }}
                                </div>
                            </div>
                        </div>

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
        document.querySelectorAll('.calendar-day .bg-primary').forEach(el => {
            el.classList.remove('bg-primary', 'text-white');
        });
        
        // Target the rounded div inside the clicked link
        const numberDiv = dayEl.querySelector('div:nth-child(2)');
        if (numberDiv) {
            numberDiv.classList.add('bg-primary', 'text-white');
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
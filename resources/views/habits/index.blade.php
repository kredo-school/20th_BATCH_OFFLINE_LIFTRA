@extends('layouts.app')
@include('habits.modals.habit-add')
@include('habits.modals.custom-warning')

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
title="{{ __('Habits') }}"
subtitle="{{ __('Build consistency, one day at a time') }}" 
>
<button class="btn btn-light rounded-3 px-4 text-primary-6366F1 btn-responsive" data-bs-toggle="modal" data-bs-target="#addHabitModal">
    <i class="fa-solid fa-plus text-primary-6366F1"></i>
    <span class="btn-text">{{ __('Add Habit') }}</span>
</button>
</x-page-header>

<div class="container-fluid px-3 px-md-5 mt-3">
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

<div class="container-fluid px-3 px-md-5 mt-4">
    <div class="row g-4">
        
        <!-- LEFT SIDE -->
        <div class="col-lg-7">

            <!-- WEEK CALENDAR -->
            <div class="calendar-nav-card shadow-sm border-0">
                {{-- Desktop Header (MD+) --}}
                <div class="d-none d-md-flex align-items-center mb-4 gap-3">
                    {{-- Left side (Today button) --}}
                    <div class="d-flex align-items-center justify-content-start gap-3" style="flex: 1;">
                        <a href="{{ route('habits.index', ['date' => now()->format('Y-m-d')]) }}" class="today-btn shadow-sm">
                            {{ __('Today') }}
                        </a>
                    </div>

                    {{-- Middle (Navigation) --}}
                    <div class="d-flex align-items-center justify-content-center" style="flex: 1;">
                        <a href="#" class="nav-arrow calendar-nav bg-white shadow-sm border" style="width: 36px; height: 36px;" data-date="{{ $selectedDate->copy()->subWeek()->toDateString() }}">
                            <i class="fa-solid fa-chevron-left text-primary"></i>
                        </a>
                        <div class="text-center px-2">
                            <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $selectedDate->format('F Y') }}</h5>
                        </div>
                        <a href="#" class="nav-arrow calendar-nav bg-white shadow-sm border" style="width: 36px; height: 36px;" data-date="{{ $selectedDate->copy()->addWeek()->toDateString() }}">
                            <i class="fa-solid fa-chevron-right text-primary"></i>
                        </a>
                    </div>

                    <div style="flex: 1;"></div>
                </div>

                {{-- Mobile Header (SP) --}}
                <div class="d-md-none mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        {{-- Row 1: Month Nav --}}
                        <div class="d-flex align-items-center gap-2">
                            <a href="#" class="calendar-nav d-flex align-items-center justify-content-center" 
                               style="width: 24px; height: 24px; border: 0.5px solid #ddd; background: white; border-radius: 50%; text-decoration: none;"
                               data-date="{{ $selectedDate->copy()->subWeek()->toDateString() }}">
                                <i class="fa-solid fa-chevron-left text-primary" style="font-size: 10px;"></i>
                            </a>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">{{ $selectedDate->format('F Y') }}</h6>
                            <a href="#" class="calendar-nav d-flex align-items-center justify-content-center" 
                               style="width: 24px; height: 24px; border: 0.5px solid #ddd; background: white; border-radius: 50%; text-decoration: none;"
                               data-date="{{ $selectedDate->copy()->addWeek()->toDateString() }}">
                                <i class="fa-solid fa-chevron-right text-primary" style="font-size: 10px;"></i>
                            </a>
                        </div>

                        {{-- Today Pill (Right) --}}
                        <a href="{{ route('habits.index', ['date' => now()->format('Y-m-d')]) }}" 
                           class="d-flex align-items-center gap-1" 
                           style="background: rgba(107,92,231,0.1); border-radius: 20px; padding: 2px 8px 2px 5px; text-decoration: none;">
                            <div style="width: 6px; height: 6px; background-color: #6B5CE7; border-radius: 50%;"></div>
                            <span style="font-size: 10px; color: #6B5CE7; font-weight: 500;">{{ __('Today') }}</span>
                        </a>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @foreach($weekDates as $day)
                        @php
                            $dateStr = $day->toDateString();
                            $isToday = $day->isToday();
                            $isSelected = $selectedDate->isSameDay($day);
                            $count = $calendarCounts[$dateStr] ?? 0;
                        @endphp
                        <a href="#" class="date-card ajax-date-nav {{ $isToday ? 'is-today' : '' }} {{ $isSelected ? 'active' : '' }}" data-date="{{ $dateStr }}">
                            <div class="day-name">{{ $day->format('D') }}</div>
                            <div class="day-number">{{ $day->format('j') }}</div>
                            
                            <!-- HABIT INDICATORS (INDIGO) -->
                            <div class="indicator-dots">
                                @if($count > 0)
                                    <div class="dot-sm dot-indigo"></div>
                                    @if($count > 1) <div class="dot-sm dot-indigo" style="opacity: 0.7;"></div> @endif
                                    @if($count > 2) <div class="dot-sm dot-indigo" style="opacity: 0.4;"></div> @endif
                                @endif
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
                <div class="fw-bold mb-4 fs-5 text-dark">{{ __('All Habits') }}</div>

                @foreach($habits as $habit)
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <!-- Left: Side Streak Box -->
                            <div class="streak-side-box shadow-sm">
                                <i class="fa-solid fa-fire"></i>
                                <span class="streak-num" id="habit-streak-{{ $habit->id }}">{{ $habit->streak }}</span>
                            </div>

                            <!-- Right: Content Information -->
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $habit->title }}</div>
                                <div class="small text-muted mb-1">
                                    @php
                                        $typeText = $habit->repeat_type == 1 ? __('Daily') : ($habit->repeat_type == 2 ? __('Weekly') : __('Monthly'));
                                        $intervalText = $habit->repeat_interval > 1 ? __('Every :interval :type', ['interval' => $habit->repeat_interval, 'type' => $typeText]) : $typeText;
                                        if ($habit->repeat_type == 3 && $habit->day_of_month) {
                                            $intervalText .= " (" . __('Day :day', ['day' => $habit->day_of_month]) . ")";
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
                                    {{ $habit->end_date ? \Carbon\Carbon::parse($habit->end_date)->format('M d, Y') : __('Ongoing') }}
                                </div>
                            </div>
                        </div>

                        <!-- Action Icons -->
                        <div class="dropdown">
                            <a href="#" class="text-muted text-decoration-none px-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-0 shadow-sm" style="min-width: 120px;">
                                <li>
                                    <a class="dropdown-item btn btn-light text-secondary py-1" href="#" data-bs-toggle="modal" data-bs-target="#editHabitModal{{ $habit->id }}">
                                        <i class="fa-solid fa-pen-to-square me-2"></i>{{ __('Edit') }}
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item btn btn-light text-danger py-1" data-bs-toggle="modal" data-bs-target="#deleteHabitModal{{ $habit->id }}">
                                        <i class="fa-solid fa-trash-can me-2"></i>{{ __('Delete') }}
                                    </button>
                                </li>
                            </ul>
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
    const dayEl = e.target.closest('.ajax-date-nav');
    if(dayEl){
        e.preventDefault();
        const date = dayEl.dataset.date;

        // Visual Selection Update
        document.querySelectorAll('.date-card.active').forEach(el => {
            el.classList.remove('active');
        });
        
        dayEl.classList.add('active');

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
        showCustomWarning(
            "{{ __('Cannot Complete') }}",
            "{{ __('You cannot complete habits for future dates.') }}",
            false,
            function() {}
        );
        e.target.checked = !isChecked; // revert visually
        return;
    }

    // Validation for past dates
    if (date < TODAY_DATE && !isChecked) {
        e.target.checked = !isChecked; // Revert visually immediately so backdrop dismiss works safely
        showCustomWarning(
            "{{ __('Unmark Past Habit') }}",
            "{{ __('Do you want to mark this habit as incomplete for a past date?') }}",
            true,
            function(confirmed) {
                if (confirmed) {
                    e.target.checked = isChecked; // Apple actual action
                    processHabitToggle(e.target, habitId, date, isChecked, titleDiv);
                }
            }
        );
        return;
    }

    // Normal processing for today or past dates (when checking)
    processHabitToggle(e.target, habitId, date, isChecked, titleDiv);
});

function showCustomWarning(title, message, isConfirm, callback) {
    const titleEl = document.getElementById('customWarningTitle');
    const messageEl = document.getElementById('customWarningMessage');
    const actionsEl = document.getElementById('customWarningActions');
    
    titleEl.textContent = title;
    messageEl.innerHTML = message; // Allow HTML if needed
    
    actionsEl.innerHTML = ''; // Clear previous buttons
    
    if (isConfirm) {
        // Cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn-light rounded-pill px-4 fw-semibold text-muted';
        cancelBtn.textContent = "{{ __('Cancel') }}";
        cancelBtn.setAttribute('data-bs-dismiss', 'modal');
        cancelBtn.onclick = function() {
            if (callback) callback(false);
        };
        
        // Confirm (Yes) button
        const confirmBtn = document.createElement('button');
        confirmBtn.type = 'button';
        confirmBtn.className = 'btn btn-warning text-white rounded-pill px-4 fw-bold shadow-sm';
        confirmBtn.textContent = "{{ __('Yes') }}";
        confirmBtn.setAttribute('data-bs-dismiss', 'modal');
        confirmBtn.onclick = function() {
            if (callback) callback(true);
        };
        
        actionsEl.appendChild(cancelBtn);
        actionsEl.appendChild(confirmBtn);
    } else {
        // Ok button for simple alert
        const okBtn = document.createElement('button');
        okBtn.type = 'button';
        okBtn.className = 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm';
        okBtn.textContent = "OK";
        okBtn.setAttribute('data-bs-dismiss', 'modal');
        okBtn.onclick = function() {
            if (callback) callback(true);
        };
        actionsEl.appendChild(okBtn);
    }
    
    // Use a native hidden trigger to avoid requiring 'bootstrap' JS object in global namespace
    let triggerBtn = document.getElementById('customWarningTriggerBtn');
    if (!triggerBtn) {
        triggerBtn = document.createElement('button');
        triggerBtn.id = 'customWarningTriggerBtn';
        triggerBtn.className = 'd-none';
        triggerBtn.setAttribute('data-bs-toggle', 'modal');
        triggerBtn.setAttribute('data-bs-target', '#customWarningModal');
        document.body.appendChild(triggerBtn);
    }
    triggerBtn.click();
}

function processHabitToggle(checkbox, habitId, date, isChecked, titleDiv) {
    if(isChecked){
        titleDiv.classList.add('text-decoration-line-through','text-muted');
    } else {
        titleDiv.classList.remove('text-decoration-line-through','text-muted');
    }

    checkbox.disabled = true;

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
        // reload the list to update streaks and states in the today view
        loadHabitsByDate(date);
        
        // Update the streak in the All Habits column in real-time
        const streakSpan = document.getElementById(`habit-streak-${habitId}`);
        if(streakSpan && data.streak !== undefined) {
            streakSpan.textContent = data.streak;
        }
    })
    .catch(err => {
        console.error(err);
        // Revert UI if fetch fails
        checkbox.checked = !isChecked;
        checkbox.disabled = false;
        if(!isChecked){
            titleDiv.classList.add('text-decoration-line-through','text-muted');
        } else {
            titleDiv.classList.remove('text-decoration-line-through','text-muted');
        }
        showCustomWarning("{{ __('Error') }}", "{{ __('Failed to save habit state. Please make sure you are logged in and try again.') }}", false);
    });
}

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

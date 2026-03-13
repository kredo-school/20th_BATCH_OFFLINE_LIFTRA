<!-- Calendar Header Navigation -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="view-switcher">
            <a href="{{ route('calendar.index', ['view' => 'week', 'date' => $selectedDate->format('Y-m-d')]) }}" 
               class="view-btn ajax-nav {{ $view == 'week' ? 'active' : '' }}">Week</a>
            <a href="{{ route('calendar.index', ['view' => 'month', 'date' => $selectedDate->format('Y-m-d')]) }}" 
               class="view-btn ajax-nav {{ $view == 'month' ? 'active' : '' }}">Month</a>
        </div>
        <a href="{{ route('calendar.index', ['view' => $view, 'date' => now()->format('Y-m-d')]) }}" class="today-btn ajax-nav">
            Today
        </a>
    </div>

    <div class="d-flex align-items-center gap-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('calendar.index', ['view' => $view, 'date' => ($view == 'week' ? $selectedDate->copy()->subWeek() : $selectedDate->copy()->subMonth())->format('Y-m-d')]) }}" class="nav-arrow ajax-nav">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <div class="text-center" style="min-width: 120px;">
                <h5 class="fw-bold mb-0">{{ $selectedDate->format('F Y') }}</h5>
            </div>
            <a href="{{ route('calendar.index', ['view' => $view, 'date' => ($view == 'week' ? $selectedDate->copy()->addWeek() : $selectedDate->copy()->addMonth())->format('Y-m-d')]) }}" class="nav-arrow ajax-nav">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>
    
    <div style="width: 200px;"></div>
</div>

@if($view == 'week')
<div class="calendar-nav-card">
    <div class="d-flex gap-2">
        @foreach($weekDates as $date)
            @php
                $dateStr = $date->format('Y-m-d');
                $counts = $activityCounts[$dateStr] ?? ['tasks' => 0, 'habits' => 0, 'actions' => 0, 'total' => 0];
            @endphp
            <a href="{{ route('calendar.index', ['view' => 'week', 'date' => $dateStr]) }}" 
               class="date-card ajax-nav {{ $date->isToday() ? 'is-today' : '' }} {{ $date->isSameDay($selectedDate) ? 'active' : '' }}">
                <div class="day-name">{{ $date->format('D') }}</div>
                <div class="day-number">{{ $date->format('j') }}</div>
                <div class="indicator-dots">
                    @if($counts['actions'] > 0) <div class="dot-sm dot-blue"></div> @endif
                    @if($counts['tasks'] > 0) <div class="dot-sm dot-green"></div> @endif
                    @if($counts['habits'] > 0) <div class="dot-sm dot-orange"></div> @endif
                </div>
            </a>
        @endforeach
    </div>
</div>
@else
<div class="calendar-nav-card text-dark">
    <div class="month-grid">
        @php $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; @endphp
        @foreach($days as $day)
            <div class="month-day-header">{{ $day }}</div>
        @endforeach

        @foreach($monthDates as $date)
            @php
                $dateStr = $date->format('Y-m-d');
                $counts = $activityCounts[$dateStr] ?? ['tasks' => 0, 'habits' => 0, 'actions' => 0, 'total' => 0];
                $isOtherMonth = $date->format('m') != $selectedDate->format('m');
                $isActive = $date->isSameDay($selectedDate);
            @endphp
            <a href="{{ route('calendar.index', ['view' => 'month', 'date' => $dateStr]) }}" 
               class="month-day-cell ajax-nav {{ $isOtherMonth ? 'other-month' : '' }} {{ $date->isToday() ? 'is-today' : '' }} {{ $isActive ? 'active' : '' }}">
                <div class="month-day-number">{{ $date->format('j') }}</div>
                <div class="month-indicators">
                    @if($counts['actions'] > 0)
                        <div class="month-indicator-item bg-action-light">
                            <span class="dot-sm dot-blue"></span> {{ $counts['actions'] }} Actions
                        </div>
                    @endif
                    @if($counts['tasks'] > 0)
                        <div class="month-indicator-item bg-task-light">
                            <span class="dot-sm dot-green"></span> {{ $counts['tasks'] }} Tasks
                        </div>
                    @endif
                    @if($counts['habits'] > 0)
                        <div class="month-indicator-item bg-habit-light">
                            <span class="dot-sm dot-orange"></span> {{ $counts['habits'] }} Habits
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

@include('calendar.partials.daily-dashboard')

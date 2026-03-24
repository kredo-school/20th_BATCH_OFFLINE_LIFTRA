<!-- Calendar Header Navigation -->
<div class="d-flex flex-column flex-md-row align-items-center mb-4 gap-3">
    
    {{-- Left block (Week/Month + Today) - Desktop Left, Mobile Bottom --}}
    <div class="d-flex align-items-center justify-content-between justify-content-md-start gap-3 order-2 order-md-1" style="flex: 1; width: 100%;">
        <div class="view-switcher shadow-sm">
            <a href="{{ route('calendar.index', ['view' => 'week', 'date' => $selectedDate->format('Y-m-d')]) }}" 
               class="view-btn ajax-nav {{ $view == 'week' ? 'active' : '' }}">Week</a>
            <a href="{{ route('calendar.index', ['view' => 'month', 'date' => $selectedDate->format('Y-m-d')]) }}" 
               class="view-btn ajax-nav {{ $view == 'month' ? 'active' : '' }}">Month</a>
        </div>
        <a href="{{ route('calendar.index', ['view' => $view, 'date' => now()->format('Y-m-d')]) }}" class="today-btn ajax-nav shadow-sm">
            Today
        </a>
    </div>

    {{-- Middle block (Month/Year Navigation) - Desktop Center, Mobile Top --}}
    <div class="d-flex align-items-center justify-content-between justify-content-md-center order-1 order-md-2" style="flex: 1; width: 100%;">
        <a href="{{ route('calendar.index', ['view' => $view, 'date' => ($view == 'week' ? $selectedDate->copy()->subWeek() : $selectedDate->copy()->subMonth())->format('Y-m-d')]) }}" class="nav-arrow ajax-nav bg-white shadow-sm border" style="width: 36px; height: 36px;">
            <i class="fa-solid fa-chevron-left text-primary"></i>
        </a>
        <div class="text-center px-2">
            <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $selectedDate->format('F Y') }}</h5>
        </div>
        <a href="{{ route('calendar.index', ['view' => $view, 'date' => ($view == 'week' ? $selectedDate->copy()->addWeek() : $selectedDate->copy()->addMonth())->format('Y-m-d')]) }}" class="nav-arrow ajax-nav bg-white shadow-sm border" style="width: 36px; height: 36px;">
            <i class="fa-solid fa-chevron-right text-primary"></i>
        </a>
    </div>
    
    {{-- Right blank block for perfect centering on desktop --}}
    <div class="d-none d-md-block order-3" style="flex: 1;"></div>
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
                    @if(isset($counts['google']) && $counts['google'] > 0) <div class="dot-sm" style="background: #10b981;"></div> @endif
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
                <div class="month-indicators w-100">
                    {{-- PC View: Text format --}}
                    <div class="d-none d-md-flex flex-column gap-1 w-100">
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
                        @if(isset($counts['google']) && $counts['google'] > 0)
                            <div class="month-indicator-item" style="background: #ecfdf5; color: #047857; border: 1px solid #d1fae5;">
                                <span class="dot-sm" style="background: #10b981;"></span> {{ $counts['google'] }} Events
                            </div>
                        @endif
                    </div>
                    
                    {{-- SP View: Dots format --}}
                    <div class="d-flex d-md-none justify-content-center align-items-center gap-1 mt-1 mb-1">
                        @if($counts['actions'] > 0) <div class="dot-sm dot-blue"></div> @endif
                        @if($counts['tasks'] > 0) <div class="dot-sm dot-green"></div> @endif
                        @if($counts['habits'] > 0) <div class="dot-sm dot-orange"></div> @endif
                        @if(isset($counts['google']) && $counts['google'] > 0) <div class="dot-sm" style="background: #10b981;"></div> @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

@include('calendar.partials.daily-dashboard')

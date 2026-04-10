{{-- Common Calendar Card --}}
<div class="calendar-nav-card shadow-sm {{ $view == 'month' ? 'text-dark' : '' }}">
    
    {{-- Desktop Header (MD+) --}}
    <div class="d-none d-md-flex align-items-center mb-4 gap-3">
        {{-- Left block (Week/Month + Today) --}}
        <div class="d-flex align-items-center justify-content-start gap-3" style="flex: 1;">
            <div class="view-switcher shadow-sm">
                <a href="{{ route('calendar.index', ['view' => 'week', 'date' => $selectedDate->format('Y-m-d')]) }}" 
                   class="view-btn ajax-nav {{ $view == 'week' ? 'active' : '' }}">{{ __('Week') }}</a>
                <a href="{{ route('calendar.index', ['view' => 'month', 'date' => $selectedDate->format('Y-m-d')]) }}" 
                   class="view-btn ajax-nav {{ $view == 'month' ? 'active' : '' }}">{{ __('Month') }}</a>
            </div>
            <a href="{{ route('calendar.index', ['view' => $view, 'date' => now()->format('Y-m-d')]) }}" class="today-btn ajax-nav shadow-sm">
                {{ __('Today') }}
            </a>
        </div>

        {{-- Middle block (Month/Year Navigation) --}}
        <div class="d-flex align-items-center justify-content-center" style="flex: 1;">
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
        
        <div style="flex: 1;"></div>
    </div>

    {{-- Mobile Header (SP) --}}
    <div class="d-md-none mb-4">
        {{-- Single Row: Month Nav + Switcher + Today Pill --}}
        <div class="d-flex align-items-center justify-content-between gap-1">
            {{-- Month Navigation (Left) --}}
            <div class="d-flex align-items-center gap-1">
                <a href="{{ route('calendar.index', ['view' => $view, 'date' => ($view == 'week' ? $selectedDate->copy()->subWeek() : $selectedDate->copy()->subMonth())->format('Y-m-d')]) }}" 
                   class="ajax-nav d-flex align-items-center justify-content-center" 
                   style="width: 24px; height: 24px; border: 0.5px solid #ddd; background: white; border-radius: 50%; text-decoration: none;">
                    <i class="fa-solid fa-chevron-left text-primary" style="font-size: 10px;"></i>
                </a>
                <h6 class="fw-bold mb-0 text-dark text-center" style="font-size: 13px; min-width: 85px;">{{ $selectedDate->format('M Y') }}</h6>
                <a href="{{ route('calendar.index', ['view' => $view, 'date' => ($view == 'week' ? $selectedDate->copy()->addWeek() : $selectedDate->copy()->addMonth())->format('Y-m-d')]) }}" 
                   class="ajax-nav d-flex align-items-center justify-content-center" 
                   style="width: 24px; height: 24px; border: 0.5px solid #ddd; background: white; border-radius: 50%; text-decoration: none;">
                    <i class="fa-solid fa-chevron-right text-primary" style="font-size: 10px;"></i>
                </a>
            </div>

            {{-- Week/Month Switcher (Center) --}}
            <div class="d-flex p-1 gap-1" style="background: #EBEBF5; border-radius: 8px;">
                <a href="{{ route('calendar.index', ['view' => 'week', 'date' => $selectedDate->format('Y-m-d')]) }}" 
                   class="ajax-nav d-flex align-items-center justify-content-center {{ $view == 'week' ? 'bg-white border text-dark shadow-sm' : 'text-muted' }}" 
                   style="font-size: 9px; padding: 3px 6px; border-radius: 6px; text-decoration: none; {{ $view == 'week' ? 'border: 0.5px solid #ddd !important;' : '' }}">
                   {{ __('Week') }}
                </a>
                <a href="{{ route('calendar.index', ['view' => 'month', 'date' => $selectedDate->format('Y-m-d')]) }}" 
                   class="ajax-nav d-flex align-items-center justify-content-center {{ $view == 'month' ? 'bg-white border text-dark shadow-sm' : 'text-muted' }}" 
                   style="font-size: 9px; padding: 3px 6px; border-radius: 6px; text-decoration: none; {{ $view == 'month' ? 'border: 0.5px solid #ddd !important;' : '' }}">
                   {{ __('Month') }}
                </a>
            </div>

            {{-- Today Pill (Right) --}}
            <a href="{{ route('calendar.index', ['view' => $view, 'date' => now()->format('Y-m-d')]) }}" 
               class="ajax-nav d-flex align-items-center gap-1" 
               style="background: rgba(107,92,231,0.1); border-radius: 20px; padding: 2px 6px 2px 4px; text-decoration: none;">
                <div style="width: 5px; height: 5px; background-color: #6B5CE7; border-radius: 50%;"></div>
                <span style="font-size: 9px; color: #6B5CE7; font-weight: 500;">{{ __('Today') }}</span>
            </a>
        </div>
    </div>

    @if($view == 'week')
        <div class="d-flex gap-2">
            @foreach($weekDates as $date)
                @php
                    $dateStr = $date->format('Y-m-d');
                    $counts = $activityCounts[$dateStr] ?? ['tasks' => 0, 'habits' => 0, 'actions' => 0, 'google' => 0, 'total' => 0];
                @endphp
                <a href="{{ route('calendar.index', ['view' => 'week', 'date' => $dateStr]) }}" 
                   class="date-card ajax-nav {{ $date->isToday() ? 'is-today' : '' }} {{ $date->isSameDay($selectedDate) ? 'active' : '' }}">
                    <div class="day-name">{{ $date->format('D') }}</div>
                    <div class="day-number">{{ $date->format('j') }}</div>
                    <div class="indicator-dots">
                        @if($counts['actions'] > 0) <div class="dot-sm dot-blue"></div> @endif
                        @if($counts['tasks'] > 0) <div class="dot-sm dot-green"></div> @endif
                        @if($counts['habits'] > 0) <div class="dot-sm dot-orange"></div> @endif
                        @if(isset($counts['google']) && $counts['google'] > 0) <div class="dot-sm dot-purple"></div> @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="month-grid">
            @php $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; @endphp
            @foreach($days as $day)
                <div class="month-day-header">{{ $day }}</div>
            @endforeach

            @foreach($monthDates as $date)
                @php
                    $dateStr = $date->format('Y-m-d');
                    $counts = $activityCounts[$dateStr] ?? ['tasks' => 0, 'habits' => 0, 'actions' => 0, 'google' => 0, 'total' => 0];
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
                                    <span class="dot-sm dot-blue"></span> {{ $counts['actions'] }}{{ __(' Actions') }}
                                </div>
                            @endif
                            @if($counts['tasks'] > 0)
                                <div class="month-indicator-item bg-task-light">
                                    <span class="dot-sm dot-green"></span> {{ $counts['tasks'] }}{{ __(' Tasks') }}
                                </div>
                            @endif
                            @if($counts['habits'] > 0)
                                <div class="month-indicator-item bg-habit-light">
                                    <span class="dot-sm dot-orange"></span> {{ $counts['habits'] }}{{ __(' Habits') }}
                                </div>
                            @endif
                            @if(isset($counts['google']) && $counts['google'] > 0)
                                <div class="month-indicator-item" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe;">
                                    <span class="dot-sm dot-purple"></span> {{ $counts['google'] }}{{ __(' Events') }}
                                </div>
                            @endif
                        </div>
                        
                        {{-- SP View: Dots format --}}
                        <div class="d-flex d-md-none justify-content-center align-items-center gap-1 mt-1 mb-1">
                            @if($counts['actions'] > 0) <div class="dot-sm dot-blue"></div> @endif
                            @if($counts['tasks'] > 0) <div class="dot-sm dot-green"></div> @endif
                            @if($counts['habits'] > 0) <div class="dot-sm dot-orange"></div> @endif
                            @if(isset($counts['google']) && $counts['google'] > 0) <div class="dot-sm dot-purple"></div> @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

<div class="{{ $view == 'month' ? 'd-md-none' : '' }}">
    @include('calendar.partials.daily-dashboard')
</div>

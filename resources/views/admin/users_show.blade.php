@extends('layouts.app')

@section('content')
<div class="container-fluid p-2 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <a href="{{ route('admin.users') }}" class="text-dark text-decoration-none me-2">
                <i class="fa-solid fa-arrow-left fs-5"></i>
            </a>
            User Profile: {{ $user->name }}
        </h3>
        <div>
            @if($user->is_suspended)
                <span class="badge bg-secondary px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-ban me-1"></i>Suspended</span>
            @elseif($user->role_id === 1)
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-shield-halved me-1"></i>Admin</span>
            @else
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-user me-1"></i>Active User</span>
            @endif
        </div>
    </div>

    <!-- User Information Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex align-items-center">
            @if($user->profile_image)
                <img src="{{ $user->profile_image }}" class="rounded-circle me-4 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
            @else
                <div class="rounded-circle me-4 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366f1, #8b5cf6); font-size: 2rem;">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2"><i class="fa-regular fa-envelope me-2"></i>{{ $user->email }}</p>
                <div class="small text-muted">
                    <i class="fa-regular fa-calendar-plus me-1"></i> Registered: {{ $user->created_at->format('Y-m-d H:i') }}
                    <span class="mx-2">|</span>
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Account Age: {{ $user->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3 mt-5">Activity Statistics</h5>
    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-light">
                <div class="text-muted small fw-bold mb-1"><i class="fa-solid fa-repeat text-success me-2"></i>Total Habits</div>
                <h3 class="fw-bold mb-0">{{ number_format($stats['habits_count']) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-light">
                <div class="text-muted small fw-bold mb-1"><i class="fa-regular fa-square-check text-info me-2"></i>Total Tasks</div>
                <h3 class="fw-bold mb-0">{{ number_format($stats['tasks_count']) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-light">
                <div class="text-muted small fw-bold mb-1"><i class="fa-solid fa-bullseye text-primary me-2"></i>Task Completion</div>
                <h3 class="fw-bold mb-0">{{ $stats['completion_rate'] }}%</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-light">
                <div class="text-muted small fw-bold mb-1"><i class="fa-solid fa-book-open text-warning me-2"></i>Total Journals</div>
                <h3 class="fw-bold mb-0">{{ number_format($stats['journals_count']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Recent Journals -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mt-5">
        <h5 class="fw-bold mb-4">Recent Journals (Up to 5)</h5>
        @if($stats['recent_journals']->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fa-solid fa-book-open fs-2 mb-2 opacity-50"></i>
                <p>User hasn't written any journals recently.</p>
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($stats['recent_journals'] as $journal)
                    <li class="list-group-item px-0 py-3 border-bottom {{ $loop->last ? 'border-0' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $journal->title ?? 'Untitled Entry' }}</h6>
                                <p class="text-muted small mb-0 text-truncate" style="max-width: 500px;">{{ strip_tags($journal->content) }}</p>
                            </div>
                            <span class="badge bg-light text-dark border">{{ \Carbon\Carbon::parse($journal->entry_date)->format('M d, Y') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
@endsection

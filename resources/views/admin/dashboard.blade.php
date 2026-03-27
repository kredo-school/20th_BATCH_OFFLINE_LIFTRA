@extends('layouts.app')

@section('content')
<div class="container-fluid p-2 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Admin Dashboard</h3>
        <a href="{{ route('admin.users') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-users me-2"></i>Manage Users
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <!-- Users -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-primary bg-opacity-10 me-3" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-user text-primary"></i>
                    </div>
                    <div class="text-muted small fw-bold">Users</div>
                </div>
                <h3 class="fw-bold mb-0 ms-1">{{ number_format($stats['total_users']) }}</h3>
            </div>
        </div>

        <!-- Habits -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-success bg-opacity-10 me-3" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-repeat text-success"></i>
                    </div>
                    <div class="text-muted small fw-bold">Habits</div>
                </div>
                <h3 class="fw-bold mb-0 ms-1">{{ number_format($stats['total_habits']) }}</h3>
            </div>
        </div>

        <!-- Tasks -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-info bg-opacity-10 me-3" style="width: 40px; height: 40px;">
                        <i class="fa-regular fa-square-check text-info"></i>
                    </div>
                    <div class="text-muted small fw-bold">Tasks</div>
                </div>
                <h3 class="fw-bold mb-0 ms-1">{{ number_format($stats['total_tasks']) }}</h3>
            </div>
        </div>

        <!-- Journals -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-warning bg-opacity-10 me-3" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-book-open text-warning"></i>
                    </div>
                    <div class="text-muted small fw-bold">Journals</div>
                </div>
                <h3 class="fw-bold mb-0 ms-1">{{ number_format($stats['total_journals']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-4">Recently Registered Users</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($stats['recent_users'] as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->profile_image)
                                        <img src="{{ $user->profile_image }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 32px; height: 32px; background: linear-gradient(135deg, #6366f1, #8b5cf6); font-size: 0.8rem;">
                                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="fw-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($user->role_id === 1)
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Admin</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-end mt-3">
            <a href="{{ route('admin.users') }}" class="text-decoration-none small">View All Users <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
    </div>
</div>
@endsection

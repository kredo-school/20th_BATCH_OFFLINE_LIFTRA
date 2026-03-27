@extends('layouts.app')

@section('content')
<div class="container-fluid p-2 p-md-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h3 class="fw-bold mb-0">
            <a href="{{ route('admin.dashboard') }}" class="text-dark text-decoration-none me-2">
                <i class="fa-solid fa-arrow-left fs-5"></i>
            </a>
            User Management
        </h3>

        <form action="{{ route('admin.users') }}" method="GET" class="d-flex align-items-center bg-white rounded-pill shadow-sm border px-3 py-1" style="max-width: 300px;">
            <i class="fa-solid fa-magnifying-glass text-muted"></i>
            <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent" placeholder="Search users..." value="{{ request('search') }}">
            @if(request('search'))
                <a href="{{ route('admin.users') }}" class="text-muted ms-2"><i class="fa-solid fa-xmark"></i></a>
            @endif
        </form>
    </div>
    {{--　users information table　--}}
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($users as $user)
                        <tr>
                            <td class="text-muted small">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->profile_image){{-- icon --}}
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
                            <td>
                                @if($user->is_suspended)
                                    <span class="badge bg-secondary rounded-pill px-3"><i class="fa-solid fa-ban me-1"></i>Suspended</span>
                                @elseif($user->role_id === 1)
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Admin</span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">User</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M d, Y - H:i') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-light border text-secondary rounded-circle" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('admin.users.password', $user->id) }}" method="POST" onsubmit="return confirm('Send a password reset link to this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border text-warning rounded-circle" title="Send Password Reset">
                                                <i class="fa-solid fa-key"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.role', $user->id) }}" method="POST" onsubmit="return confirm('Toggle admin rights for this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border text-primary rounded-circle" title="Toggle Role">
                                                <i class="fa-solid fa-shield-halved"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to {{ $user->is_suspended ? 'restore' : 'suspend' }} this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle" title="{{ $user->is_suspended ? 'Restore User' : 'Suspend User' }}">
                                                <i class="fa-solid {{ $user->is_suspended ? 'fa-user-check' : 'fa-ban' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fa-solid fa-users-slash fs-1 mb-3 text-opacity-25"></i>
                                <p class="mb-0">No users found matching your search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

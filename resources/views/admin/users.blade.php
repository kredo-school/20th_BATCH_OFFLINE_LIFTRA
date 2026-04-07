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
                                    @if(Auth::id() !== $user->id)

                                        <button type="button" class="btn btn-sm btn-light border text-primary rounded-circle" title="Toggle Role" data-bs-toggle="modal" data-bs-target="#roleModal{{ $user->id }}">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-light border text-danger rounded-circle" title="{{ $user->is_suspended ? 'Restore User' : 'Suspend User' }}" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $user->id }}">
                                            <i class="fa-solid {{ $user->is_suspended ? 'fa-user-check' : 'fa-ban' }}"></i>
                                        </button>


                                        <!-- Role Toggle Modal -->
                                        <div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
                                                <div class="modal-content p-3 mx-3 border-0 shadow-lg rounded-4 text-start">
                                                    <div class="modal-body text-center pt-4">
                                                        <div class="mb-3 text-primary">
                                                            <i class="fa-solid fa-shield-halved fa-3x"></i>
                                                        </div>
                                                        <h5 class="fw-bold text-dark mb-3">Change User Role</h5>
                                                        <p class="text-muted mb-4">Are you sure you want to change <span class="fw-bold">{{ $user->name }}</span>'s role to <span class="fw-bold text-primary">{{ $user->role_id === 1 ? 'General User' : 'Administrator' }}</span>?</p>
                                                    </div>
                                                    <div class="text-center px-3 pb-3">
                                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Confirm Change</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Suspend/Restore Modal -->
                                        <div class="modal fade" id="suspendModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
                                                <div class="modal-content p-3 mx-3 border-0 shadow-lg rounded-4 text-start">
                                                    <div class="modal-body text-center pt-4">
                                                        <div class="mb-3 {{ $user->is_suspended ? 'text-success' : 'text-danger' }}">
                                                            <i class="fa-solid {{ $user->is_suspended ? 'fa-user-check' : 'fa-ban' }} fa-3x"></i>
                                                        </div>
                                                        <h5 class="fw-bold text-dark mb-3">{{ $user->is_suspended ? 'Restore User' : 'Suspend User' }}</h5>
                                                        <p class="text-muted mb-4">Are you sure you want to <span class="fw-bold {{ $user->is_suspended ? 'text-success' : 'text-danger' }}">{{ $user->is_suspended ? 'Restore' : 'Suspend' }}</span> account access for <span class="fw-bold">{{ $user->name }}</span>?</p>
                                                    </div>
                                                    <div class="text-center px-3 pb-3">
                                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn {{ $user->is_suspended ? 'btn-success' : 'btn-danger' }} rounded-pill px-4 fw-bold shadow-sm">{{ $user->is_suspended ? 'Restore Access' : 'Suspend Access' }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-light border text-secondary rounded-circle" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
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

@extends('layouts.app')

@push('styles')
<style>
    .notification-item {
        transition: background-color 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }
    .notification-item:hover {
        background-color: #f8fafc;
    }
    .notification-item.unread {
        background-color: #f0f7ff;
        border-left: 4px solid #3b82f6;
    }
    .notification-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
</style>
@endpush

@section('content')
<x-page-header title="Notifications" subtitle="Stay updated on your progress and schedule">
    <form action="{{ route('notifications.markAsRead') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-light border text-secondary rounded-3 px-3">
            <i class="fa-solid fa-check-double me-1 small"></i> Mark all as read
        </button>
    </form>
    <form action="{{ route('notifications.destroyAll') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all notifications?')" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-light border text-danger rounded-3 px-3">
            <i class="fa-solid fa-trash-can me-1 small"></i> Delete all
        </button>
    </form>
</x-page-header>

<div class="container-fluid px-3 px-md-5 pb-5">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isTask = $data['type'] === 'task_start';
                @endphp
                <div class="notification-item p-3 d-flex align-items-start gap-3 {{ $notification->unread() ? 'unread' : '' }}">
                    <div class="notification-icon {{ $isTask ? 'bg-primary bg-opacity-10 text-primary' : 'bg-info bg-opacity-10 text-info' }}">
                        <i class="fa-solid {{ $isTask ? 'fa-thumbtack' : 'fa-circle-info' }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0 text-dark">{{ $data['title'] ?? 'Notification' }}</h6>
                            <span class="text-muted small me-2">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <p class="text-muted mb-2 small">{{ $data['message'] }}</p>
                            @if(isset($data['task_id']))
                                <a href="{{ route('tasks.show', $data['task_id']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                    View Task
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="fa-regular fa-bell-slash fa-3x opacity-25"></i>
                    </div>
                    <h5 class="text-muted fw-bold">No notifications yet</h5>
                    <p class="text-muted small">We'll notify you here when tasks start or reports are ready.</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

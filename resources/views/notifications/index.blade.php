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
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .notification-item.cursor-pointer {
        cursor: pointer;
    }
    .header-action-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .header-action-btn:hover {
        background: #f8fafc;
        border-color: #94a3b8 !important;
        transform: translateY(-1px);
    }
    @media (max-width: 576px) {
        .btn-view-task {
            font-size: 0.55rem !important;
            padding: 2px 6px !important;
            white-space: nowrap !important;
        }
        .header-btn {
            font-size: 0.7rem !important;
            padding: 2px 6px !important;
        }
    }
</style>
@endpush

@section('content')
<x-page-header title="{{ __('Notifications') }}" subtitle="{{ __('Stay updated on your progress and schedule') }}">
    
</x-page-header>

@include('notifications.modals.delete-all')
@include('notifications.modals.delete-single')

<div class="container-fluid px-3 px-md-5 pb-1">
    <div class="row justify-content-center mt-3">
        <div class="col-12">
            <div class="text-end">
        <form action="{{ route('notifications.markAsRead') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn header-action-btn text-secondary rounded-3 px-3 header-btn">
            <i class="fa-solid fa-check-double me-1 small"></i> {{ __('Mark all as read') }}
        </button>
        </form>
        <button type="button" class="btn header-action-btn text-danger rounded-3 px-3 header-btn" data-bs-toggle="modal" data-bs-target="#deleteAllNotificationsModal">
            <i class="fa-solid fa-trash-can me-1 small"></i> {{ __('Delete all') }}
        </button>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-3">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isTask = $data['type'] === 'task_start';
                @endphp
                <div class="notification-item p-3 d-flex align-items-start gap-3 {{ $notification->unread() ? 'unread' : '' }} {{ isset($data['task_id']) ? 'cursor-pointer' : '' }}"
                     @if(isset($data['task_id'])) onclick="window.location.href='{{ route('tasks.show', $data['task_id']) }}'" @endif>
                    <div class="notification-icon {{ $isTask ? 'bg-primary bg-opacity-10 text-primary' : 'bg-info bg-opacity-10 text-info' }}">
                        <i class="fa-solid {{ $isTask ? 'fa-thumbtack' : 'fa-circle-info' }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0 text-dark text-truncate pe-2">{{ $data['title'] ?? __('Notification') }}</h6>
                            <span class="text-muted small ms-auto text-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <p class="text-muted mb-0 small pe-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $data['message'] }}</p>
                            <div class="d-flex gap-2">
                                @if($notification->unread())
                                    <form action="{{ route('notifications.markAsReadSingle', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 fw-bold btn-view-task" 
                                            style="font-size: 0.75rem;"
                                            onclick="event.stopPropagation()">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 fw-bold btn-view-task" 
                                        style="font-size: 0.75rem;" disabled>
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-bold btn-view-task" 
                                    style="font-size: 0.75rem;" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteNotificationModal" 
                                    data-url="{{ route('notifications.destroy', $notification->id) }}"
                                    onclick="event.stopPropagation()">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="fa-regular fa-bell-slash fa-3x opacity-25"></i>
                    </div>
                    <h5 class="text-muted fw-bold">{{ __('No notifications yet') }}</h5>
                    <p class="text-muted small">{{ __('We\'ll notify you here when tasks start or reports are ready.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <div class="mt-4 mx-1">
        {{ $notifications->links() }}
    </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteNotificationModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const url = button.getAttribute('data-url');
                const form = document.getElementById('deleteNotificationForm');
                form.setAttribute('action', url);
            });
        }
    });
</script>
@endpush
@endsection

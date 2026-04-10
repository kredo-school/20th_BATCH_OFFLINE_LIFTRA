@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
@endpush

@section('content')

<x-page-header title="{{ __('Tasks') }}" subtitle="{{ __('Organize and prioritize your tasks') }}">
    <button class="btn btn-light rounded-3 my-auto px-lg-4 px-md-3 px-2 text-primary-6366F1 btn-responsive"
            data-bs-toggle="modal" data-bs-target="#add-task">
        <i class="fa-solid fa-plus"></i><span class="btn-text">{{ __('Add Tasks') }}</span>
    </button>
</x-page-header>

    {{-- modal here --}}
    @include('tasks.modals.add-task')

<div class=" container-fluid px-3 px-md-5">
    <div class="row justify-content-center mt-3">
        <div class="view-buttons col-11 mb-3">
            <a href="{{ route('tasks.index', ['view' => 'matrix']) }}"
                class="btn {{ $view === 'matrix' ? 'btn-secondary' : 'btn-outline-secondary' }} col-3 col-md-3 col-lg-2">
                <i class="fa-solid fa-table-cells-large"></i> {{ __('Matrix View') }}
            </a> 

            <a href="{{ route('tasks.index', ['view' => 'list']) }}"
                class="btn {{ $view === 'list' ? 'btn-secondary' : 'btn-outline-secondary' }} col-3 col-md-3 col-lg-2">
                <i class="fa-solid fa-list"></i> {{ __('List View') }}
            </a>
            
            <a href="{{ route('tasks.index', ['view' => 'completed']) }}"
                class="btn {{ $view === 'completed' ? 'btn-success' : 'btn-outline-success' }} col-4 col-md-4 col-lg-3">
                <i class="fa-solid fa-check-circle"></i> {{ __('Completed') }}
            </a>
        </div>

        @if($view === 'list')
            @include('tasks.list')
        @elseif($view === 'completed')
            @include('tasks.completed')
        @else
            @include('tasks.matrix')
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[action*="complete"]').forEach(function(form) {
        const checkbox = form.querySelector('input[type="checkbox"][name="task"]');
        if (checkbox) {
            // Remove inline event to prevent form.submit()
            checkbox.removeAttribute('onchange');
            
            checkbox.addEventListener('change', function(e) {
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST', 
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const container = form.closest('.card-body') || form.closest('td');
                        if (container) {
                            let titleElement = container.querySelector('.task-title') || container.querySelector('.fw-bold') || container.querySelector('*[id^="task_label_"]');
                            if (titleElement) {
                                if (data.completed) {
                                    titleElement.classList.remove('text-dark', 'text-decoration-none');
                                    titleElement.classList.add('text-decoration-line-through', 'text-muted');
                                } else {
                                    // If we are natively inside completed view, do not remove it entirely, 
                                    // but we can remove it for matrix/list views when they toggle rapidly
                                    if(!form.closest('.border-success')) { 
                                        titleElement.classList.remove('text-decoration-line-through', 'text-muted');
                                        titleElement.classList.add('text-dark', 'text-decoration-none');
                                    }
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error toggling task completion:', error);
                    checkbox.checked = !checkbox.checked; // Revert
                });
            });
        }
    });
});
</script>
@endpush

@endsection
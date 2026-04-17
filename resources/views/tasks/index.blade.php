@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
<style>
    .header-action-btn {
        transition: all 0.2s ease;
    }
    .header-action-btn:hover, .header-action-btn:hover i, .header-action-btn:hover span {
        color: #6366F1 !important;
    }
</style>
@endpush

@section('content') 

<x-page-header title="{{ __('Tasks') }}" subtitle="{{ __('Organize and prioritize your tasks') }}">
    <button class="btn btn-light rounded-3 my-auto px-lg-4 px-md-3 px-2 text-primary-6366F1 btn-responsive header-action-btn"
            data-bs-toggle="modal" data-bs-target="#add-task">
        <i class="fa-solid fa-plus"></i><span class="btn-text">{{ __('Add Tasks') }}</span>
    </button>
</x-page-header>

    {{-- modal here --}}
    @include('tasks.modals.add-task')

<div class=" container-fluid px-3 px-md-5">
    <div class="row justify-content-center mt-3">
        <div class="view-buttons col-12 mb-3 d-flex justify-content-center gap-2 gap-md-3">
            <a href="{{ route('tasks.index', ['view' => 'matrix']) }}"
                class="btn {{ $view === 'matrix' ? 'btn-secondary' : 'btn-outline-secondary' }} col-3 col-md-3 col-lg-2 d-inline-flex align-items-center justify-content-center gap-1">
                <i class="fa-solid fa-table-cells-large view-icon"></i> <span class="d-none d-md-inline">{{ __('Matrix View') }}</span>
            </a> 

            <a href="{{ route('tasks.index', ['view' => 'list']) }}"
                class="btn {{ $view === 'list' ? 'btn-secondary' : 'btn-outline-secondary' }} col-3 col-md-3 col-lg-2 d-inline-flex align-items-center justify-content-center gap-1">
                <i class="fa-solid fa-list view-icon"></i> <span class="d-none d-md-inline">{{ __('List View') }}</span>
            </a>
            
            <a href="{{ route('tasks.index', ['view' => 'completed']) }}"
                class="btn {{ $view === 'completed' ? 'btn-success' : 'btn-outline-success' }} col-3 col-md-3 col-lg-2 d-inline-flex align-items-center justify-content-center gap-1">
                <i class="fa-solid fa-check-circle view-icon"></i> <span class="d-none d-md-inline">{{ __('Completed') }}</span>
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
    // Close all other open dropdowns when clicking a new dropdown toggle
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('[data-bs-toggle="dropdown"]');
        if (!toggleBtn) return;

        // Find all currently open dropdown menus and close them
        document.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
            // Find the toggle button inside the same dropdown wrapper
            const dropdownWrapper = menu.closest('.dropdown');
            const otherToggle = dropdownWrapper ? dropdownWrapper.querySelector('[data-bs-toggle="dropdown"]') : null;
            if (otherToggle && otherToggle !== toggleBtn) {
                const instance = bootstrap.Dropdown.getInstance(otherToggle);
                if (instance) {
                    instance.hide();
                } else {
                    menu.classList.remove('show');
                    otherToggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }, true); // Use capture phase to run before Bootstrap's own handlers

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
                        // Find the task card element
                        const taskCard = form.closest('.task-card');
                        if (taskCard) {
                            // Animate out
                            taskCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease, max-height 0.3s ease';
                            taskCard.style.opacity = '0';
                            taskCard.style.transform = 'scale(0.95)';
                            taskCard.style.overflow = 'hidden';

                            setTimeout(() => {
                                // Update the matrix quadrant counter if in matrix view
                                const quadrant = taskCard.closest('.matrix');
                                if (quadrant) {
                                    const countEl = quadrant.querySelector('.count');
                                    if (countEl) {
                                        const current = parseInt(countEl.textContent) || 0;
                                        countEl.textContent = Math.max(0, current - 1);
                                    }
                                }

                                // Remove the wrapper (col-12 in list/completed) or the card itself (matrix)
                                const colWrapper = taskCard.closest('.col-12');
                                if (colWrapper) {
                                    colWrapper.remove();
                                } else {
                                    taskCard.remove();
                                }
                            }, 300);
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
@extends('layouts.app')

@push('styles')
<style>
    
    .view-switcher {
        background: #f1f5f9;
        padding: 3px;
        border-radius: 10px;
        display: inline-flex;
    }
    
    .view-btn {
        padding: 6px 16px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
        text-decoration: none;
        color: #64748b;
    }
    
    .view-btn.active {
        background: white;
        color: #1e293b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .today-btn {
        padding: 6px 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .today-btn:hover {
        background: #f8fafc;
        color: #1e293b;
    }
    
    .calendar-nav-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }
    
    .nav-arrow {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #94a3b8;
        font-size: 1rem;
        transition: all 0.2s;
        text-decoration: none;
    }
    
    .nav-arrow:hover {
        background: #f1f5f9;
        color: #6366f1;
    }
    
    .date-card {
        flex: 1;
        text-align: center;
        padding: 10px 5px;
        border-radius: 12px;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        min-width: 0;
    }
    
    .date-card:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    
    .date-card.active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    
    .date-card.is-today:not(.active),
    .month-day-cell.is-today:not(.active) {
        background-color: rgba(99, 102, 241, 0.08); /* Very soft blue */
        border-color: rgba(99, 102, 241, 0.2);
    }
    
    /* Slightly stronger text color for today indicator */
    .date-card.is-today:not(.active) .day-number,
    .month-day-cell.is-today:not(.active) .month-day-number {
        color: #4f46e5;
    }
    
    .day-name {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
        opacity: 0.7;
    }
    
    .day-number {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .indicator-dots {
        display: flex;
        justify-content: center;
        gap: 4px;
        margin-top: 4px;
        height: 6px;
    }

    .dot-sm {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    
    /* Month View Styles */
    .month-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
    }

    .month-day-header {
        padding: 10px 5px;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .month-day-cell {
        background: white;
        min-height: 90px;
        padding: 8px 10px;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .month-day-cell:hover {
        border-color: #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }

    .month-day-cell.other-month {
        background: #fafafa;
        border-color: transparent;
    }
    
    .month-day-cell.other-month .month-day-number {
        color: #d4d4d8;
    }

    .month-day-cell.active {
        box-shadow: 0 0 0 2px #6366f1;
        border-color: transparent;
    }

    .month-day-number {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 6px;
        color: #334155;
    }

    .active .month-day-number {
        color: #6366f1;
        font-weight: 700;
    }

    .month-indicators {
        display: flex;
        flex-direction: column;
        gap: 3px;
        margin-top: auto;
    }

    .month-indicator-item {
        font-size: 0.65rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 3px 6px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
        letter-spacing: 0.02em;
    }

    .bg-action-light { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .bg-task-light { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .bg-habit-light { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .loading-overlay {
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.2s;
    }
    
    .dashboard-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .dashboard-section-title {
        font-weight: 700;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    
    .dot-blue { background: #3b82f6; }
    .dot-green { background: #10b981; }
    .dot-orange { background: #f59e0b; }
    
    .content-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #f1f5f9;
        padding: 20px;
        height: 100%;
        min-height: 300px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .item-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f8fafc;
    }
    
    .item-row:last-child {
        border-bottom: none;
    }
    
    .item-checkbox {
        width: 18px;
        height: 18px;
        margin-top: 3px;
        border-radius: 4px;
        border: 2px solid #cbd5e1;
    }
    
    .item-title {
        font-weight: 500;
        font-size: 0.95rem;
        color: #334155;
    }
    
    .item-meta {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .priority-badge {
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<x-page-header title="Calendar" subtitle="Plan your actions, tasks, and habits">
    <button class="btn btn-light rounded-3 px-4 btn-responsive"
            data-bs-toggle="modal" data-bs-target="#addActionModal">
        <i class="fa-solid fa-plus"></i><span class="btn-text">Add Action</span>
    </button>
</x-page-header>

    <div class="container mt-3">
    <div class="container mt-2">
        <div id="calendar-app-container">
            @include('calendar.partials.app-container')
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const appContainer = document.getElementById('calendar-app-container');
    
    function navigate(url, dateStr, isDashboardOnly = false, skipHistory = false) {
        const targetContainer = isDashboardOnly ? document.getElementById('daily-dashboard-fragment') : appContainer;
        if (targetContainer) targetContainer.classList.add('loading-overlay');
        
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (isDashboardOnly) {
            headers['X-Requested-Part'] = 'dashboard';
        }
        
        fetch(url, { headers })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            if (isDashboardOnly && targetContainer) {
                targetContainer.innerHTML = html;
                targetContainer.classList.remove('loading-overlay');
                
                // Update active states visually
                document.querySelectorAll('.date-card, .month-day-cell').forEach(el => {
                    el.classList.toggle('active', el.getAttribute('href').includes('date=' + dateStr));
                });
            } else {
                appContainer.innerHTML = html;
                appContainer.classList.remove('loading-overlay');
                
                // Only update URL for full container navigation (like Next/Prev arrows)
                if (!skipHistory) {
                    window.history.pushState({ date: dateStr, url: url, isDashboardOnly: false }, '', url);
                }
            }
        })
        .catch(error => {
            console.error('Error navigating calendar:', error);
            if (targetContainer) targetContainer.classList.remove('loading-overlay');
        });
    }

    document.addEventListener('click', function(e) {
        const navLink = e.target.closest('.ajax-nav');
        if (navLink) {
            e.preventDefault();
            const url = navLink.getAttribute('href');
            const urlParams = new URLSearchParams(url.split('?')[1]);
            const dateStr = urlParams.get('date');
            
            const isDashboardOnly = navLink.classList.contains('date-card') || navLink.classList.contains('month-day-cell');
            
            navigate(url, dateStr, isDashboardOnly);
        }
    });

    // Handle back/forward navigation
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.url) {
            navigate(event.state.url, event.state.date, event.state.isDashboardOnly, true);
        } else {
            window.location.reload();
        }
    });
});

const TODAY_DATE = "{{ \Carbon\Carbon::today()->format('Y-m-d') }}";

// Habit Checkbox UI
document.body.addEventListener('change', function(e){
    // Match either the generic task checkbox or specifically our habit checkbox
    if(!e.target.classList.contains('habit-checkbox')) return;
    
    console.log("Habit checkbox triggered on Calendar!", e.target);

    // Find the title element to apply strikethrough. 
    // In daily-dashboard, e.target is the <input>, the title is in the next sibling <div>
    const parentRow = e.target.closest('.item-row');
    const titleContainer = parentRow ? parentRow.querySelector('div:not(.item-row)') : null;
    const titleDiv = titleContainer ? titleContainer.querySelector('.item-title') : null;
    
    if(!titleDiv) {
        console.error("Could not find .item-title next to the clicked checkbox.");
        return;
    }

    const habitId = e.target.dataset.habitId;
    const date = e.target.dataset.date;
    const isChecked = e.target.checked;

    console.log("Habit ID:", habitId, "Date:", date, "Checked:", isChecked);

    // Validation for future dates
    if (date > TODAY_DATE) {
        alert("You cannot complete habits for future dates.");
        e.target.checked = !isChecked; // revert
        return;
    }

    // Validation for past dates
    if (date < TODAY_DATE) {
        const confirmPast = confirm("Do you want to complete/incomplete a habit for a past date?");
        if (!confirmPast) {
            e.target.checked = !isChecked; // revert
            return;
        }
    }

    // Optimistic UI update
    if(isChecked){
        titleDiv.classList.add('text-decoration-line-through','text-muted');
    } else {
        titleDiv.classList.remove('text-decoration-line-through','text-muted');
    }

    e.target.disabled = true;

    fetch(`/habits/${habitId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ date: date })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if(!data.success) throw new Error('Update reported failure from server.');
        e.target.disabled = false;
        console.log("Habit toggle successful.");
    })
    .catch(err => {
        console.error('Habit toggle error:', err);
        // Revert UI if fetch fails
        e.target.checked = !isChecked;
        e.target.disabled = false;
        if(!isChecked){
            titleDiv.classList.add('text-decoration-line-through','text-muted');
        } else {
            titleDiv.classList.remove('text-decoration-line-through','text-muted');
        }
        alert('Failed to save habit state. Please make sure you are logged in and try again. Error: ' + err.message);
    });
});

// Task Checkbox UI
document.body.addEventListener('change', function(e){
    if(!e.target.classList.contains('task-checkbox')) return;
    
    console.log("Task checkbox triggered on Calendar!", e.target);

    // Find the title element to apply strikethrough. 
    const parentRow = e.target.closest('.item-row');
    const titleContainer = parentRow ? parentRow.querySelector('div:not(.item-row)') : null;
    const titleDiv = titleContainer ? titleContainer.querySelector('.item-title') : null;
    
    if(!titleDiv) {
        console.error("Could not find .item-title next to the clicked task checkbox.");
        return;
    }

    const taskId = e.target.dataset.taskId;
    const isChecked = e.target.checked;

    console.log("Task ID:", taskId, "Checked:", isChecked);

    // Optimistic UI update
    if(isChecked){
        titleDiv.classList.add('text-decoration-line-through','text-muted');
    } else {
        titleDiv.classList.remove('text-decoration-line-through','text-muted');
    }

    e.target.disabled = true;

    fetch(`/tasks/${taskId}/complete`, {
        method: 'POST', // Use POST with _method override for Laravel PATCH
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            _method: 'PATCH' 
        })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if(!data.success) throw new Error('Update reported failure from server.');
        e.target.disabled = false;
        console.log("Task toggle successful.");
    })
    .catch(err => {
        console.error('Task toggle error:', err);
        // Revert UI if fetch fails
        e.target.checked = !isChecked;
        e.target.disabled = false;
        if(!isChecked){
            titleDiv.classList.add('text-decoration-line-through','text-muted');
        } else {
            titleDiv.classList.remove('text-decoration-line-through','text-muted');
        }
        alert('Failed to save task state. Please make sure you are logged in and try again. Error: ' + err.message);
    });
});
</script>
@endpush
@include('tasks.modals.add-task')
@include('habits.modals.habit-add')

<!-- Placeholder Action Modal -->
<div class="modal fade" id="addActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-4 border-0 shadow rounded-4 text-center">
            <div class="modal-body">
                <i class="fa-solid fa-person-running text-primary fs-1 mb-3"></i>
                <h5 class="fw-bold">Actions Feature</h5>
                <p class="text-muted">The Milestone Actions feature is currently under development. Stay tuned!</p>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

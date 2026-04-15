<!-- Task Delete Modal -->
<div class="modal fade" id="deleteTaskModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
      @csrf
      @method('DELETE')

      <div class="modal-content p-3 border-0 shadow-lg rounded-4">
        <div class="modal-body text-center pt-4">
          <div class="mb-3 text-danger">
            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
          </div>
          <h5 class="fw-bold text-dark mb-3">{{ __('Delete Task') }}</h5>
          <p class="text-muted mb-4">{{ __('Are you sure you want to delete') }} 
            <span class="fw-bold">{{ $task->title }}</span>{{ __('?This action cannot be undone.') }}</p>
          <div>
            <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border shadow-sm text-dark me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">{{ __('Delete') }}</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Education Delete Modal -->
<div class="modal fade" id="deleteEducationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="deleteEducationForm" method="POST" action="{{ route('profile.education.destroy', $edu->id) }}">
      @csrf
      @method('DELETE')
      <div class="modal-content p-3 border-0 shadow-lg rounded-4">
        <div class="modal-body text-center p-4">
          <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Delete Education') }}</h5>
          <p class="text-muted mb-4">{{ __('Are you sure you want to delete this education record?') }}</p>
          <div>
            <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">{{ __('Delete') }}</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
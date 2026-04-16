<!-- Experience Delete Modal -->
<div class="modal fade" id="deleteExperienceModal-{{ $exp->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
    <div class="modal-content p-3 border-0 shadow-lg rounded-4">
      <form method="POST" action="{{ route('profile.experience.destroy', $exp->id) }}">
        @csrf
        @method('DELETE')
        <div class="modal-body text-center p-4">
          <div class="mb-3 text-danger">
            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
          </div>
          <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Delete Work Experience') }}</h5>
          <p class="text-muted mb-4">{{ __('Are you sure you want to delete this work experience?') }}</p>
          <div class="d-flex justify-content-center align-items-center gap-2">
            <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">{{ __('Delete') }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Delete Milestone Modal -->
<div class="modal fade" id="deleteMilestoneModal{{ $milestone->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
    <div class="modal-content p-3 border-0 shadow-lg" style="border-radius: 20px;">
      <form action="{{ route('lifeplan.milestone.destroy', $milestone->id) }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="modal-body text-center pt-4">
          <div style="font-size: 2.2rem; margin-bottom: 12px;">⚠️</div>
          <h5 class="fw-bold text-dark mb-3">{{ __('Delete Milestone') }}</h5>
          <p class="text-muted mb-4 small">{{ __('Are you sure you want to delete this milestone?') }}<br>{{ __('This action cannot be undone.') }}</p>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-light flex-grow-1 rounded-pill fw-semibold text-muted" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-danger flex-grow-1 rounded-pill fw-bold shadow-sm">{{ __('Delete') }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Journal Delete Modal -->
<div class="modal fade" id="deleteJournalModal{{ $journal->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('journals.destroy', $journal->id) }}" method="POST">
      @csrf
      @method('DELETE')

      <div class="modal-content p-3 border-0 shadow-lg rounded-4">
        <div class="modal-body text-center pt-4">
          <div class="mb-3 text-danger">
            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
          </div>
          <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Delete Journal Entry') }}</h5>
          <p class="text-muted mb-4">{{ __('Are you sure you want to delete') }}
            <span class="fw-bold">{{ $journal->title }}</span>{{ __('?This action cannot be undone.') }}</p>
        </div>

        <div class="d-flex justify-content-center px-3 pb-3 gap-2">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">{{ __('Delete') }}</button>
        </div>
      </div>
    </form>
  </div>
</div>

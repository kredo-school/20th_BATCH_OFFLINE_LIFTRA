<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
    <div class="modal-content p-3 border-0 shadow-lg" style="border-radius: 20px;">
      <form action="{{ route('lifeplan.category.destroy', $category->id) }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="modal-body text-center pt-4">
          <div class="mb-3 text-danger">
            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
          </div>
          <h5 class="fw-bold text-dark mb-3">{{ __('Delete Category') }}</h5>
          <p class="text-muted mb-4 small">{{ __('Are you sure you want to delete this category?') }}<br>{{ __('This action cannot be undone.') }}</p>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-light flex-grow-1 rounded-pill fw-semibold text-muted shadow-sm border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-danger flex-grow-1 rounded-pill fw-bold shadow-sm">{{ __('Delete') }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

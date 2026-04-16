<!-- Education Add Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
      <div class="modal-content p-3 border-0 shadow-lg rounded-4">
    <form action="{{ route('profile.education.store') }}" method="POST">
      @csrf
        <div class="modal-body">
          <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Add Education') }}</h5>
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('School Name') }}</label>
            <input type="text" name="school_name" class="form-control border bg-white rounded-3 px-3 py-2" required>
          </div>
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Degree') }}</label>
            <input type="text" name="degree" class="form-control border bg-white rounded-3 px-3 py-2" required>
          </div>
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Field') }}</label>
            <input type="text" name="field" class="form-control border bg-white rounded-3 px-3 py-2" required>
          </div>
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Country') }}</label>
            <input type="text" name="country" class="form-control border bg-white rounded-3 px-3 py-2" required>
          </div>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Start Date') }}</label>
                <input type="date" name="start_date" class="form-control border bg-white rounded-3 px-3 py-2" required>
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('End Date') }}</label>
                <input type="date" name="end_date" class="form-control border bg-white rounded-3 px-3 py-2">
              </div>
            </div>
          </div>
        </div>
        <div class="text-end px-3 pb-3">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2 border shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">{{ __('Add') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
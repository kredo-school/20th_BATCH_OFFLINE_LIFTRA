<!-- Education Add Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('profile.education.store') }}" method="POST">
      @csrf
      <div class="modal-content p-3">
        <div class="modal-body">
          <h5 class="modal-title mb-3 fw-bold">Add Education</h5>
          <div class="mb-2">
            <label class="fw-bold">School Name</label>
            <input type="text" name="school_name" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="fw-bold">Degree</label>
            <input type="text" name="degree" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="fw-bold">Field</label>
            <input type="text" name="field" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="fw-bold">Country</label>
            <input type="text" name="country" class="form-control" required>
          </div>
          <div class="row">
            <div class="col">
              <div class="mb-2">
                <label class="fw-bold">Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
              </div>
            </div>
            <div class="col">
              <div class="mb-2">
                <label class="fw-bold">End Date</label>
                <input type="date" name="end_date" class="form-control">
              </div>
            </div>
          </div>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Education Edit Modal (動的に値を入れる想定) -->
<div class="modal fade" id="editEducationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editEducationForm" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Education</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>School Name</label>
            <input type="text" name="school_name" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Degree</label>
            <input type="text" name="degree" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Field</label>
            <input type="text" name="field" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Country</label>
            <input type="text" name="country" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>
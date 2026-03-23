<!-- Education Edit Modal -->
<div class="modal fade" id="editEducationModal-{{ $edu->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editEducationForm-{{ $edu->id }}" method="POST" action="{{ route('profile.education.update', $edu->id) }}">
      @csrf
      @method('PUT')

      <div class="modal-content p-3">
        <div class="modal-body">
          <h5 class="modal-title mb-3 ">Edit Education</h5>

          <div class="mb-2">
            <label>School Name</label>
            <input type="text"
                   name="school_name"
                   class="form-control"
                   value="{{ old('school_name', $edu->school_name) }}"
                   required>
          </div>

          <div class="mb-2">
            <label>Degree</label>
            <input type="text"
                   name="degree"
                   class="form-control"
                   value="{{ old('degree', $edu->degree) }}"
                   required>
          </div>

          <div class="mb-2">
            <label>Field</label>
            <input type="text"
                   name="field"
                   class="form-control"
                   value="{{ old('field', $edu->field) }}"
                   required>
          </div>

          <div class="mb-2">
            <label>Country</label>
            <input type="text"
                   name="country"
                   class="form-control"
                   value="{{ old('country', $edu->country) }}"
                   required>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-2">
                <label>Start Date</label>
                <input type="date"
                      name="start_date"
                      class="form-control"
                      value="{{ old('start_date', $edu->start_date) }}"
                      required>
              </div>
            </div>
            <div class="col">
              <div class="mb-2">
                <label>End Date</label>
                <input type="date"
                       name="end_date"
                       class="form-control"
                       value="{{ old('end_date', $edu->end_date) }}">
              </div>
            </div>
          </div>





        </div>

        <div class="text-end">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>

          <button type="submit" class="btn btn-primary">
            Update
          </button>
        </div>

      </div>
    </form>
  </div>
</div>
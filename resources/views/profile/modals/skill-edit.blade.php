<div class="modal fade" id="editSkillModal-{{ $skill->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
    <div class="modal-content p-3 border-0 shadow-lg rounded-4">
      <form method="POST" action="{{ route('profile.skill.update', $skill->id) }}">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <h5 class="modal-title mb-4 fw-bold text-dark">Edit Skill</h5>
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Skill Name</label>
            <input type="text" name="skill_name" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ old('skill_name', $skill->skill_name) }}" required>
          </div>
        </div>
        <div class="text-end px-3 pb-3">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="editSkillModal-{{ $skill->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('profile.skill.update', $skill->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-content p-3">
        <div class="modal-body">
          <h5 class="modal-title mb-3 fw-bold">Edit Skill</h5>
          <div class="mb-2">
            <label class="fw-bold">Skill Name</label>
            <input type="text" name="skill_name" class="form-control" value="{{ old('skill_name', $skill->skill_name) }}" required>
          </div>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('profile.skill.store') }}" method="POST">
      @csrf
      <div class="modal-content p-3">
        <div class="modal-body">
          <h5 class="modal-title mb-3 fw-bold">Add Skill</h5>
          <div class="mb-2">
            <label class="fw-bold">Skill Name</label>
            <input type="text" name="skill_name" class="form-control" required>
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
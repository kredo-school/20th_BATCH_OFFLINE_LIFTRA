<div class="modal fade" id="deleteSkillModal-{{ $skill->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('profile.skill.destroy', $skill->id) }}">
      @csrf
      @method('DELETE')
      <div class="modal-content p-3">
        <div class="modal-body text-muted">
          Are you sure you want to delete this skill?
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>
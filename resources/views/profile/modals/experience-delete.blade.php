<!-- Experience Delete Modal -->
<div class="modal fade" id="deleteExperienceModal-{{ $exp->id }}" tabindex="-1">
  <div class="modal-dialog">

    <form method="POST" action="{{ route('profile.experience.destroy', $exp->id) }}">
      @csrf
      @method('DELETE')

      <div class="modal-content p-3">

        <h5 class="modal-title">Delete Work Experience</h5>

        <div class="modal-body text-muted">
          Are you sure you want to delete this work experience?
        </div>

        <div class="text-end">

          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>

          <button type="submit" class="btn btn-danger">
            Delete
          </button>

        </div>

      </div>
    </form>

  </div>
</div>
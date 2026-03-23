<!-- Education Delete Modal -->
<div class="modal fade" id="deleteEducationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="deleteEducationForm" method="POST" action="{{ route('profile.education.destroy', $edu->id) }}">
      @csrf
      @method('DELETE')
      <div class="modal-content p-3">
        <h5 class="modal-title">Delete Education</h5>
        <div class="modal-body text-muted">
          Are you sure you want to delete this education record?
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Certification Delete Modal -->
<div class="modal fade" id="deleteCertificationModal-{{ $cert->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('profile.certification.destroy', $cert->id) }}">
      @csrf
      @method('DELETE')

      <div class="modal-content p-3">
        <h5 class="modal-title">Delete Certification</h5>
        <div class="modal-body text-muted">
          Are you sure you want to delete the certification: <strong>{{ $cert->title }}</strong>?
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </div>

    </form>
  </div>
</div>
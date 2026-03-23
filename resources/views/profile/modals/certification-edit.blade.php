<!-- Certification Edit Modal -->
<div class="modal fade" id="editCertificationModal-{{ $cert->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('profile.certification.update', $cert->id) }}">
      @csrf
      @method('PUT')

      <div class="modal-content p-3">

        <div class="modal-body">

          <h5 class="fw-bold mb-3">Edit Certification</h5>

          <div class="mb-2">
            <label class="fw-bold">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $cert->title) }}" required>
          </div>

          <div class="mb-2">
            <label class="fw-bold">Issuer</label>
            <input type="text" name="issuer" class="form-control" value="{{ old('issuer', $cert->issuer) }}" required>
          </div>

          <div class="mb-2">
            <label class="fw-bold">Obtained Date</label>
            <input type="date" name="obtained_date" class="form-control" value="{{ old('obtained_date', $cert->obtained_date ? \Carbon\Carbon::parse($cert->obtained_date)->format('Y-m-d') : '') }}">
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
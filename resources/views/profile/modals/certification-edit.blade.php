<!-- Certification Edit Modal -->
<div class="modal fade" id="editCertificationModal-{{ $cert->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('profile.certification.update', $cert->id) }}">
      @csrf
      @method('PUT')

      <div class="modal-content p-3 border-0 shadow-lg rounded-4">

        <div class="modal-body">

          <h5 class="modal-title mb-4 fw-bold text-dark">Edit Certification</h5>

          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Title</label>
            <input type="text" name="title" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ old('title', $cert->title) }}" required>
          </div>

          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Issuer</label>
            <input type="text" name="issuer" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ old('issuer', $cert->issuer) }}" required>
          </div>

          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Obtained Date</label>
            <input type="date" name="obtained_date" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ old('obtained_date', $cert->obtained_date ? \Carbon\Carbon::parse($cert->obtained_date)->format('Y-m-d') : '') }}">
          </div>

        </div>

        <div class="text-end px-3 pb-3">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Update</button>
        </div>

      </div>
    </form>
  </div>
</div>
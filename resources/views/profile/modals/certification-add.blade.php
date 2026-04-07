<div class="modal fade" id="addCertificationModal">
<div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">

<form method="POST" action="{{ route('profile.certification.store') }}">
@csrf

<div class="modal-content p-3 border-0 shadow-lg rounded-4">

<div class="modal-body">

<h5 class="modal-title mb-4 fw-bold text-dark">Add Certification</h5>

<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">Title</label>
<input type="text" name="title" class="form-control border bg-white rounded-3 px-3 py-2" required>
</div>

<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">Issuer</label>
<input type="text" name="issuer" class="form-control border bg-white rounded-3 px-3 py-2" required>
</div>

<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">Obtained Date</label>
<input type="date" name="obtained_date" class="form-control border bg-white rounded-3 px-3 py-2">
</div>

</div>

<div class="text-end px-3 pb-3">

<button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>

<button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Add</button>

</div>

</div>

</form>
</div>
</div>
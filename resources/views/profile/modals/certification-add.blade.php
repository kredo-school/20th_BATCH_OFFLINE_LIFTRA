<div class="modal fade" id="addCertificationModal">
<div class="modal-dialog">

<form method="POST" action="{{ route('profile.certification.store') }}">
@csrf

<div class="modal-content p-3">

<div class="modal-body">

<h5 class="fw-bold mb-3">Add Certification</h5>

<div class="mb-2">
<label class="fw-bold">Title</label>
<input type="text" name="title" class="form-control" required>
</div>

<div class="mb-2">
<label class="fw-bold">Issuer</label>
<input type="text" name="issuer" class="form-control" required>
</div>

<div class="mb-2">
<label class="fw-bold">Obtained Date</label>
<input type="date" name="obtained_date" class="form-control">
</div>

</div>

<div class="text-end">

<button type="button"
        class="btn btn-light"
        data-bs-dismiss="modal">
Cancel
</button>

<button type="submit"
        class="btn btn-primary">
Add
</button>

</div>

</div>

</form>
</div>
</div>
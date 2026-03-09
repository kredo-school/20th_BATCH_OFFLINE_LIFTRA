<!-- Experience Add Modal -->
<div class="modal fade" id="addExperienceModal" tabindex="-1">
<div class="modal-dialog">

<form method="POST" action="{{ route('profile.experience.store') }}">
@csrf

<div class="modal-content p-3">

<div class="modal-body">

<h5 class="fw-bold mb-3">Add Work Experience</h5>

<div class="mb-2">
<label class="fw-bold">Job Title</label>
<input type="text" name="job_title" class="form-control" required>
</div>

<div class="mb-2">
<label class="fw-bold">Company Name</label>
<input type="text" name="company_name" class="form-control" required>
</div>

<div class="mb-2">
<label class="fw-bold">Employment Type</label>

<select name="employment_type" class="form-control">
<option value="Full-time">Full-time</option>
<option value="Part-time">Part-time</option>
<option value="Contract">Contract</option>
<option value="Internship">Internship</option>
<option value="Freelance">Freelance</option>
</select>

</div>

<div class="row">

<div class="col">
<label class="fw-bold">Start Date</label>
<input type="date" name="start_date" class="form-control" required>
</div>

<div class="col">
<label class="fw-bold">End Date</label>
<input type="date" name="end_date" class="form-control">
</div>

</div>

<div class="form-check mt-2">
<input class="form-check-input"
       type="checkbox"
       name="currently_working"
       value="1">

<label class="form-check-label">
Currently Working Here
</label>
</div>

<div class="mt-2">
<label class="fw-bold">Description</label>
<textarea name="description" class="form-control" rows="3"></textarea>
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
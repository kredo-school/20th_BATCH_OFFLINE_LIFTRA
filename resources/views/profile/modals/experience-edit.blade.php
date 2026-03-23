<!-- Experience Edit Modal -->
<div class="modal fade" id="editExperienceModal-{{ $exp->id }}">
<div class="modal-dialog">

<form method="POST"
      action="{{ route('profile.experience.update',$exp->id) }}">
@csrf
@method('PUT')

<div class="modal-content p-3">

<div class="modal-body">

<h5 class="fw-bold mb-3">Edit Work Experience</h5>

<div class="mb-2">
<label class="fw-bold">Job Title</label>

<input type="text"
       name="job_title"
       class="form-control"
       value="{{ $exp->job_title }}"
       required>
</div>

<div class="mb-2">
<label class="fw-bold">Company Name</label>

<input type="text"
       name="company_name"
       class="form-control"
       value="{{ $exp->company_name }}"
       required>
</div>

<div class="mb-2">
<label class="fw-bold">Employment Type</label>

<select name="employment_type" class="form-control">

<option value="Full-time"
{{ $exp->employment_type=='Full-time'?'selected':'' }}>
Full-time
</option>

<option value="Part-time"
{{ $exp->employment_type=='Part-time'?'selected':'' }}>
Part-time
</option>

<option value="Contract"
{{ $exp->employment_type=='Contract'?'selected':'' }}>
Contract
</option>

<option value="Internship"
{{ $exp->employment_type=='Internship'?'selected':'' }}>
Internship
</option>

<option value="Freelance"
{{ $exp->employment_type=='Freelance'?'selected':'' }}>
Freelance
</option>

</select>

</div>

<div class="row">

<div class="col">
<label class="fw-bold">Start Date</label>

<input type="date"
       name="start_date"
       class="form-control"
       value="{{ $exp->start_date }}">
</div>

<div class="col">
<label class="fw-bold">End Date</label>

<input type="date"
       name="end_date"
       class="form-control"
       value="{{ $exp->end_date }}">
</div>

</div>

<div class="form-check mt-2">

<input class="form-check-input"
       type="checkbox"
       name="currently_working"
       value="1"
       {{ $exp->currently_working ? 'checked' : '' }}>

<label class="form-check-label">
Currently Working Here
</label>

</div>

<div class="mt-2">
<label class="fw-bold">Description</label>

<textarea name="description"
          class="form-control"
          rows="3">{{ $exp->description }}</textarea>

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
Update
</button>

</div>

</div>
</form>

</div>
</div>
<!-- Experience Edit Modal -->
<div class="modal fade" id="editExperienceModal-{{ $exp->id }}">
<div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">

<div class="modal-content p-3 border-0 shadow-lg rounded-4">
<form method="POST"
      action="{{ route('profile.experience.update',$exp->id) }}">
@csrf
@method('PUT')

<div class="modal-body">

<h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Edit Work Experience') }}</h5>

<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Job Title') }}</label>

<input type="text"
       name="job_title"
       class="form-control border bg-white rounded-3 px-3 py-2"
       value="{{ $exp->job_title }}"
       required>
</div>

<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Company Name') }}</label>

<input type="text"
       name="company_name"
       class="form-control border bg-white rounded-3 px-3 py-2"
       value="{{ $exp->company_name }}"
       required>
</div>

<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Employment Type') }}</label>

<select name="employment_type" class="form-control border bg-white rounded-3 px-3 py-2">

<option value="Full-time"
{{ $exp->employment_type=='Full-time'?'selected':'' }}>
{{ __('Full-time') }}
</option>

<option value="Part-time"
{{ $exp->employment_type=='Part-time'?'selected':'' }}>
{{ __('Part-time') }}
</option>

<option value="Contract"
{{ $exp->employment_type=='Contract'?'selected':'' }}>
{{ __('Contract') }}
</option>

<option value="Internship"
{{ $exp->employment_type=='Internship'?'selected':'' }}>
{{ __('Internship') }}
</option>

<option value="Freelance"
{{ $exp->employment_type=='Freelance'?'selected':'' }}>
{{ __('Freelance') }}
</option>

</select>

</div>

<div class="row">

<div class="col">
<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Start Date') }}</label>

<input type="date"
       name="start_date"
       class="form-control border bg-white rounded-3 px-3 py-2"
       value="{{ $exp->start_date }}">
</div>
</div>

<div class="col">
<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('End Date') }}</label>

<input type="date"
       name="end_date"
       class="form-control border bg-white rounded-3 px-3 py-2"
       value="{{ $exp->end_date }}">
</div>
</div>

</div>

<div class="form-check mt-1 mb-3">

<input class="form-check-input"
       type="checkbox"
       name="currently_working"
       value="1"
       {{ $exp->currently_working ? 'checked' : '' }}>

<label class="form-check-label text-muted small">
{{ __('Currently Working Here') }}
</label>

</div>

<div class="mb-3">
<label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Description') }}</label>

<textarea name="description"
          class="form-control border bg-white rounded-3 px-3 py-2"
          rows="3">{{ $exp->description }}</textarea>

</div>

</div>

<div class="text-end px-3 pb-3">

<button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>

<button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">{{ __('Update') }}</button>

</div>

</div>
</form>

</div>
</div>
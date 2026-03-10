<!-- Habit Add Modal -->
<div class="modal fade" id="addHabitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('habits.store') }}" method="POST">
      @csrf

      <div class="modal-content p-3 border-0 shadow-lg rounded-4">
        <div class="modal-body">
          <h5 class="modal-title mb-4 fw-bold text-dark">Add Habit</h5>

          <!-- Habit title -->
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Habit Name</label>
            <input type="text" name="title" class="form-control border-0 bg-light rounded-3 px-3 py-2" placeholder="e.g. Morning Yoga" required>
          </div>

          <!-- Repeat type -->
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Repeat Type</label>
            <select name="repeat_type" class="form-select border-0 bg-light rounded-3 px-3 py-2 repeatType" required>
              <option value="1">Daily</option>
              <option value="2">Weekly</option>
              <option value="3">Monthly</option>
            </select>
          </div>

          <!-- Interval -->
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Interval</label>
            <div class="d-flex align-items-center">
              <span class="me-2 text-muted">Every</span>
              <input type="number" name="repeat_interval" class="form-control border-0 bg-light rounded-3 text-center" value="1" min="1" style="width:80px;">
              <span class="ms-2 intervalUnit text-muted">day(s)</span>
            </div>
          </div>

          <!-- Weekly -->
          <div class="mb-3 weeklyOptions" style="display:none;">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Select Days</label>
            <div class="d-flex flex-wrap gap-2">
              @foreach(['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'] as $value => $label)
                <label class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                  <input type="checkbox" name="days_of_week[]" value="{{ $value }}" hidden> {{ $label }}
                </label>
              @endforeach
            </div>
          </div>

          <!-- Monthly -->
          <div class="mb-3 monthlyOptions" style="display:none;">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Day of Month</label>
            <select name="day_of_month" class="form-select border-0 bg-light rounded-3 px-3 py-2">
              @for($i=1;$i<=31;$i++)
                <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </div>

          <!-- Start / End -->
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Start Date</label>
                <input type="date" name="start_date" class="form-control border-0 bg-light rounded-3 px-3 py-2" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">End Date</label>
                <input type="date" name="end_date" class="form-control border-0 bg-light rounded-3 px-3 py-2 habitEndDate">
                <div class="form-check mt-2">
                  <input type="checkbox" class="form-check-input noEndDate">
                  <label class="form-check-label small text-muted">No End Date</label>
                </div>
              </div>
            </div>
          </div>

          <!-- Time -->
          <div class="mb-3">
            <div class="form-check form-switch mb-2">
              <input class="form-check-input allDay" type="checkbox">
              <label class="form-check-label fw-bold text-dark">All Day</label>
            </div>
            <input type="time" name="habit_time" class="form-control border-0 bg-light rounded-3 px-3 py-2 habitTime" value="09:00">
          </div>

        </div>

        <div class="text-end px-3 pb-3">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Add Habit</button>
        </div>

      </div>
    </form>
  </div>
</div>

<script>

// Repeat type change
document.querySelectorAll('.repeatType').forEach(select => {
  select.addEventListener('change', function(){
    const modal = this.closest('.modal')
    const weekly = modal.querySelector('.weeklyOptions')
    const monthly = modal.querySelector('.monthlyOptions')
    const unit = modal.querySelector('.intervalUnit')

    weekly.style.display='none'
    monthly.style.display='none'

    if(this.value == "1"){
      unit.innerText='day(s)'
    }
    if(this.value == "2"){
      unit.innerText='week(s)'
      weekly.style.display='block'
    }
    if(this.value == "3"){
      unit.innerText='month(s)'
      monthly.style.display='block'
    }
  })
})

// All day toggle
document.querySelectorAll('.allDay').forEach(cb=>{
  cb.addEventListener('change',function(){
    const modal=this.closest('.modal')
    const time=modal.querySelector('.habitTime')
    if (time) {
      time.disabled=this.checked
      time.style.backgroundColor = this.checked ? '#e9ecef' : ''
    }
  })
})

// No End Date
document.querySelectorAll('.noEndDate').forEach(cb => {
  cb.addEventListener('change', function() {
    const modal = this.closest('.modal');
    const endDateInput = modal.querySelector('.habitEndDate');
    if (endDateInput) {
      endDateInput.disabled = this.checked;
      endDateInput.style.backgroundColor = this.checked ? '#e9ecef' : '';
    }
  });
});

// Weekly day toggle
document.querySelectorAll('.weeklyOptions input[type="checkbox"]').forEach(cb => {
  cb.addEventListener('change', function() {
    const label = this.closest('label');
    if (this.checked) {
      label.classList.remove('btn-outline-primary');
      label.classList.add('btn-primary', 'text-white', 'shadow-sm');
    } else {
      label.classList.remove('btn-primary', 'text-white', 'shadow-sm');
      label.classList.add('btn-outline-primary');
    }
  });
});

</script>
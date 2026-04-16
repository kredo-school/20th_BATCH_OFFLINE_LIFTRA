<!-- Habit Edit Modal -->
<div class="modal fade" id="editHabitModal{{ $habit->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
    <div class="modal-content p-3 border-0 shadow-lg rounded-4">
    <form action="{{ route('habits.update',$habit->id) }}" method="POST">
      @csrf
      @method('PUT')
        <div class="modal-body">
          <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Edit Habit') }}</h5>

          <!-- Habit title -->
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Habit Name') }}</label>
            <input type="text" name="title" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $habit->title }}" required>
          </div>

          <!-- Repeat type -->
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Repeat Type') }}</label>
            <select name="repeat_type" class="form-select border bg-white rounded-3 px-3 py-2 repeatType" required>
              <option value="1" {{ $habit->repeat_type == 1 ? 'selected' : '' }}>{{ __('Daily') }}</option>
              <option value="2" {{ $habit->repeat_type == 2 ? 'selected' : '' }}>{{ __('Weekly') }}</option>
              <option value="3" {{ $habit->repeat_type == 3 ? 'selected' : '' }}>{{ __('Monthly') }}</option>
            </select>
          </div>

          <!-- Interval -->
          <div class="mb-3">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Interval') }}</label>
            <div class="d-flex align-items-center">
              <span class="me-2 text-muted">{{ __('Every') }}</span>
              <input type="number" name="repeat_interval" class="form-control border bg-white rounded-3 text-center repeatInterval" value="{{ $habit->repeat_interval }}" min="1" style="width:80px;" data-original="{{ $habit->repeat_interval }}">
              <span class="ms-2 intervalUnit text-muted">
                @if($habit->repeat_type == 1) {{ __('day(s)') }} @elseif($habit->repeat_type == 2) {{ __('week(s)') }} @else {{ __('month(s)') }} @endif
              </span>
            </div>
          </div>

          <!-- Weekly -->
          <div class="mb-3 weeklyOptions" style="{{ $habit->repeat_type == 2 ? '' : 'display:none;' }}">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Select Days') }}</label>
            <div class="d-flex flex-wrap gap-2">
              @php
                $selectedDays = is_array($habit->days_of_week) ? $habit->days_of_week : (json_decode($habit->days_of_week, true) ?? []);
              @endphp
              @foreach(['mon' => __('Mon'), 'tue' => __('Tue'), 'wed' => __('Wed'), 'thu' => __('Thu'), 'fri' => __('Fri'), 'sat' => __('Sat'), 'sun' => __('Sun')] as $value => $label)
                <label class="btn {{ in_array($value, $selectedDays) ? 'btn-primary text-white shadow-sm' : 'btn-outline-primary' }} btn-sm rounded-pill px-3 py-1">
                  <input type="checkbox" name="days_of_week[]" value="{{ $value }}" {{ in_array($value, $selectedDays) ? 'checked' : '' }} hidden> {{ $label }}
                </label>
              @endforeach
            </div>
          </div>

          <!-- Monthly -->
          <div class="mb-3 monthlyOptions" style="{{ $habit->repeat_type == 3 ? '' : 'display:none;' }}">
            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Day of Month') }}</label>
            <select name="day_of_month" class="form-select border bg-white rounded-3 px-3 py-2">
              @for($i=1;$i<=31;$i++)
                <option value="{{ $i }}" {{ $habit->day_of_month == $i ? 'selected' : '' }}>{{ $i }}</option>
              @endfor
            </select>
          </div>

          <!-- Start / End -->
          <div class="row mb-3">
            <div class="col-6">
              <label class="fw-bold text-muted small mb-1 d-block text-uppercase">{{ __('Start Date') }}</label>
              <input type="date" name="start_date" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $habit->start_date }}" required>
            </div>
            <div class="col-6">
              <label class="fw-bold text-muted small mb-1 d-block text-uppercase">{{ __('End Date') }}</label>
              <input type="date" name="end_date" class="form-control border bg-white rounded-3 px-3 py-2 habitEndDate" value="{{ $habit->end_date }}" {{ !$habit->end_date ? 'disabled style=background-color:#e9ecef;' : '' }}>
              <div class="form-check mt-2">
                <input type="checkbox" class="form-check-input noEndDate" id="noEndDateEdit{{ $habit->id }}" {{ !$habit->end_date ? 'checked' : '' }}>
                <label class="form-check-label small text-muted" for="noEndDateEdit{{ $habit->id }}">{{ __('No End Date') }}</label>
              </div>
            </div>
          </div>

          <!-- Time -->
          <div class="mb-3">
            <label class="fw-bold text-muted small mb-1 d-block text-uppercase">{{ __('Time') }}</label>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input allDay" type="checkbox" {{ !$habit->habit_time ? 'checked' : '' }}>
              <label class="form-check-label fw-bold text-dark">{{ __('All Day') }}</label>
            </div>
            <input type="time" name="habit_time" class="form-control border bg-white rounded-3 px-3 py-2 habitTime" value="{{ $habit->habit_time ? \Carbon\Carbon::parse($habit->habit_time)->format('H:i') : '09:00' }}" {{ !$habit->habit_time ? 'disabled style=background-color:#e9ecef;' : '' }}>
          </div>

        </div>

        <div class="text-end px-3 pb-3">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border shadow-sm text-dark me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">{{ __('Update') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function() {
  const modal = document.querySelector('#editHabitModal{{ $habit->id }}');
  if (!modal) return;

  const repeatType = modal.querySelector('.repeatType');
  const repeatInterval = modal.querySelector('.repeatInterval');
  const allDay = modal.querySelector('.allDay');
  const noEndDate = modal.querySelector('.noEndDate');

  const originalRepeatType = repeatType.value;
  const originalRepeatInterval = repeatInterval.value;

  // Streak warning
  [repeatType, repeatInterval].forEach(el => {
    el.addEventListener('change', function() {
      if (repeatType.value != originalRepeatType || repeatInterval.value != originalRepeatInterval) {
        if (!confirm("Changing the Repeat Type or Interval will apply from today onwards and your streak will be reset. Do you want to continue?")) {
          repeatType.value = originalRepeatType;
          repeatInterval.value = originalRepeatInterval;
          // Trigger repeatType change logic to reset UI
          repeatType.dispatchEvent(new Event('change'));
        }
      }
    });
  });

  // Repeat type change UI logic
  repeatType.addEventListener('change', function() {
    const weekly = modal.querySelector('.weeklyOptions');
    const monthly = modal.querySelector('.monthlyOptions');
    const unit = modal.querySelector('.intervalUnit');

    weekly.style.display = 'none';
    monthly.style.display = 'none';

    if (this.value == "1") unit.innerText = "{{ __('day(s)') }}";
    if (this.value == "2") {
      unit.innerText = "{{ __('week(s)') }}";
      weekly.style.display = 'block';
    }
    if (this.value == "3") {
      unit.innerText = "{{ __('month(s)') }}";
      monthly.style.display = 'block';
    }
  });

  // Shared helper for disabled styling
  const applyDisabledStyle = (input, checked) => {
    if (input) {
      input.disabled = checked;
      input.style.backgroundColor = checked ? '#e9ecef' : '';
    }
  };

  // All day toggle logic
  applyDisabledStyle(modal.querySelector('.habitTime'), allDay.checked);
  allDay.addEventListener('change', function() {
    applyDisabledStyle(modal.querySelector('.habitTime'), this.checked);
  });

  // No End Date toggle logic
  applyDisabledStyle(modal.querySelector('.habitEndDate'), noEndDate.checked);
  noEndDate.addEventListener('change', function() {
    applyDisabledStyle(modal.querySelector('.habitEndDate'), this.checked);
  });

  // Weekly day toggle
  modal.querySelectorAll('.weeklyOptions input[type="checkbox"]').forEach(cb => {
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
})();
</script>
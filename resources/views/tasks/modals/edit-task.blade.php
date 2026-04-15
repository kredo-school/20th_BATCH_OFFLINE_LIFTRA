<div class="modal fade" id="editTaskModal{{ $task->id }}">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
    <div class="modal-content p-3 border-0 shadow-lg rounded-4">
        <form action="{{ route('tasks.update', $task->id) }}" method="post">
            @csrf
            @method('PATCH')
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Edit Task') }}</h5>

                    <!-- Task name -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Task Name') }}</label>
                        <input type="text" class="form-control border bg-white rounded-3 px-3 py-2" name="title" value="{{ $task->title }}" placeholder="{{ __('Task name...') }}" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Description') }}</label>
                        <textarea class="form-control border bg-white rounded-3 px-3 py-2" name="description" placeholder="{{ __('Add description...') }}">{{ $task->description }}</textarea>
                    </div>

                    <!-- Priority -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Priority (Matrix)') }}</label>
                        <select class="form-select border bg-white rounded-3 px-3 py-2" name="priority_type" required>
                            <option value="1" {{ $task->priority_type == 1 ? 'selected' : '' }}>{{ __('Urgent & Important') }}</option>
                            <option value="2" {{ $task->priority_type == 2 ? 'selected' : '' }}>{{ __('Important & Not Urgent') }}</option>
                            <option value="3" {{ $task->priority_type == 3 ? 'selected' : '' }}>{{ __('Not Important & Urgent') }}</option>
                            <option value="4" {{ $task->priority_type == 4 ? 'selected' : '' }}>{{ __('Not Important & Not Urgent') }}</option>
                        </select>
                    </div>

                    @php
                        $isRepeat = $task->repeat_type !== null;
                    @endphp

                    <!-- Repeat checkbox -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="taskRepeatCheck{{ $task->id }}" name="is_repeat" value="1" {{ $isRepeat ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark">{{ __('Repeat') }}</label>
                    </div>

                    <!-- NO REPEAT AREA -->
                    <div id="noRepeatArea{{ $task->id }}" style="display: {{ $isRepeat ? 'none' : 'block' }};">
                        <div class="row align-items-center mb-3">
                            <div class="col-6">
                                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Start Date (Optional)') }}</label>
                                <input type="date" name="start_date_no_repeat" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->start_date ? date('Y-m-d', strtotime($task->start_date)) : '' }}">
                            </div>
                            <div class="col-6">
                                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Due Date') }}</label>
                                <input type="date" name="due_date" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->due_date ? date('Y-m-d', strtotime($task->due_date)) : date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Time') }}</label>
                            <div class="form-check form-switch mb-2 d-flex align-items-center">
                                <input class="form-check-input allDayCheck{{ $task->id }} mt-0" type="checkbox" name="all_day_no_repeat" value="1" {{ !$task->task_time ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark ms-2">{{ __('All day') }}</label>
                            </div>
                            <input type="time" name="task_time_no_repeat" class="form-control border bg-white rounded-3 px-3 py-2 taskTimeInput{{ $task->id }}" value="{{ $task->task_time ? date('H:i', strtotime($task->task_time)) : '09:00' }}">
                        </div>
                    </div>

                    <!-- REPEAT AREA -->
                    <div id="repeatArea{{ $task->id }}" style="display: {{ $isRepeat ? 'block' : 'none' }};">
                        <div class="border rounded-3 p-3 mb-3">
                            <!-- Repeat type -->
                            <div class="mb-3">
                                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Repeat type') }}</label>
                                <select name="repeat_type" class="form-select border bg-white rounded-3 px-3 py-2 taskRepeatType{{ $task->id }}">
                                    <option value="1" {{ $task->repeat_type == 1 ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                    <option value="2" {{ $task->repeat_type == 2 ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                    <option value="3" {{ $task->repeat_type == 3 ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                </select>
                            </div>

                            <!-- Interval -->
                            <div class="mb-3 d-flex align-items-center">
                                <span class="me-2 text-muted small font-monospace text-uppercase">{{ __('Every') }}</span>
                                <input type="number" name="repeat_interval" class="form-control border bg-white rounded-3 text-center" value="{{ $task->repeat_interval ?? 1 }}" min="1" style="width:70px;">
                                <span class="ms-2 taskIntervalUnit{{ $task->id }} text-muted small font-monospace text-uppercase">
                                    @if($task->repeat_type == 2) {{ __('week(s)') }} @elseif($task->repeat_type == 3) {{ __('month(s)') }} @else {{ __('day(s)') }} @endif
                                </span>
                            </div>

                            <!-- Weekly -->
                            <div class="mb-3 taskWeeklyOptions{{ $task->id }}" style="display: {{ $task->repeat_type == 2 ? 'block' : 'none' }};">
                                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Select days') }}</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                        $selectedDays = is_string($task->days_of_week) ? json_decode($task->days_of_week, true) ?? [] : (array)$task->days_of_week;
                                    @endphp
                                    @foreach(['mon' => __('Mon'), 'tue' => __('Tue'), 'wed' => __('Wed'), 'thu' => __('Thu'), 'fri' => __('Fri'), 'sat' => __('Sat'), 'sun' => __('Sun')] as $value => $label)
                                        <div class="form-check form-check-inline me-1">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="{{ $value }}" {{ in_array($value, $selectedDays) ? 'checked' : '' }}>
                                            <label class="form-check-label small">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Monthly -->
                            <div class="mb-3 taskMonthlyOptions{{ $task->id }}" style="display: {{ $task->repeat_type == 3 ? 'block' : 'none' }};">
                                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Select day of month') }}</label>
                                <select name="day_of_month" class="form-select border bg-white rounded-3 px-3 py-2">
                                    @for($i=1;$i<=31;$i++)
                                        <option value="{{ $i }}" {{ $task->day_of_month == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Start / End -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Start date') }}</label>
                                    <input type="date" name="start_date_repeat" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->start_date ? date('Y-m-d', strtotime($task->start_date)) : date('Y-m-d') }}">
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('End date') }}</label>
                                    <input type="date" name="end_date" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->end_date ? date('Y-m-d', strtotime($task->end_date)) : '' }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Time') }}</label>
                                <div class="form-check form-switch mb-2 d-flex align-items-center">
                                    <input class="form-check-input allDayCheck2{{ $task->id }} mt-0" type="checkbox" name="all_day_repeat" value="1" {{ !$task->task_time ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark ms-2">{{ __('All day') }}</label>
                                </div>
                                <input type="time" name="task_time_repeat" class="form-control border bg-white rounded-3 px-3 py-2 taskTimeInput2{{ $task->id }}" value="{{ $task->task_time ? date('H:i', strtotime($task->task_time)) : '09:00' }}">
                            </div>
                        </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const id = '{{ $task->id }}';
        const repeatCheck = document.getElementById('taskRepeatCheck' + id);
        const noRepeatArea = document.getElementById('noRepeatArea' + id);
        const repeatArea = document.getElementById('repeatArea' + id);
        const repeatType = document.querySelector('.taskRepeatType' + id);
        const weeklyOptions = document.querySelector('.taskWeeklyOptions' + id);
        const monthlyOptions = document.querySelector('.taskMonthlyOptions' + id);
        const intervalUnit = document.querySelector('.taskIntervalUnit' + id);
        const allDayCheck = document.querySelector('.allDayCheck' + id);
        const taskTimeInput = document.querySelector('.taskTimeInput' + id);
        const allDayCheck2 = document.querySelector('.allDayCheck2' + id);
        const taskTimeInput2 = document.querySelector('.taskTimeInput2' + id);

        if(!repeatCheck || !repeatType) return;

        // Toggle Repeat/No Repeat
        repeatCheck.addEventListener('change', function() {
            if (this.checked) {
                noRepeatArea.style.display = 'none';
                repeatArea.style.display = 'block';
            } else {
                noRepeatArea.style.display = 'block';
                repeatArea.style.display = 'none';
            }
        });

        // Toggle Repeat Type (Daily/Weekly/Monthly)
        repeatType.addEventListener('change', function() {
            weeklyOptions.style.display = 'none';
            monthlyOptions.style.display = 'none';

            if(this.value == "1"){
                intervalUnit.innerText = "{{ __('day(s)') }}";
            } else if(this.value == "2"){
                intervalUnit.innerText = "{{ __('week(s)') }}";
                weeklyOptions.style.display = 'block';
            } else if(this.value == "3"){
                intervalUnit.innerText = "{{ __('month(s)') }}";
                monthlyOptions.style.display = 'block';
            }
        });

        // Toggle All Day
        const toggleTime = (checkbox, timeInput) => {
            if(!checkbox || !timeInput) return;

            const applyStyle = (input, checked) => {
                if (input) {
                    input.disabled = checked;
                    input.style.backgroundColor = checked ? '#e9ecef' : '';
                }
            };

            // Initial state
            applyStyle(timeInput, checkbox.checked);

            checkbox.addEventListener('change', function() {
                applyStyle(timeInput, this.checked);
            });
        };
        toggleTime(allDayCheck, taskTimeInput);
        toggleTime(allDayCheck2, taskTimeInput2);
    });
</script>

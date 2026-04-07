<div class="modal fade mx-4 mt-3" id="editTaskModal{{ $task->id }}">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
        <form action="{{ route('tasks.update', $task->id) }}" method="post">
            @csrf
            @method('PATCH')
            
            <div class="modal-content p-3 border-0 shadow-lg rounded-4">
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">Edit Task</h5>

                    <!-- Task name -->
                    <div class="mb-3">
                        <input type="text" class="form-control border bg-white rounded-3 px-3 py-2" name="title" value="{{ $task->title }}" placeholder="Task name..." required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <textarea class="form-control border bg-white rounded-3 px-3 py-2" name="description" placeholder="Add description...">{{ $task->description }}</textarea>
                    </div>

                    <!-- Priority -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small mb-1 d-block">Priority (Matrix)</label>
                        <select class="form-select border bg-white rounded-3 px-3 py-2" name="priority_type" required>
                            <option value="1" {{ $task->priority_type == 1 ? 'selected' : '' }}>Urgent & Important</option>
                            <option value="2" {{ $task->priority_type == 2 ? 'selected' : '' }}>Important & Not Urgent</option>
                            <option value="3" {{ $task->priority_type == 3 ? 'selected' : '' }}>Not Important & Urgent</option>
                            <option value="4" {{ $task->priority_type == 4 ? 'selected' : '' }}>Not Important & Not Urgent</option>
                        </select>
                    </div>

                    @php
                        $isRepeat = $task->repeat_type !== null;
                    @endphp

                    <!-- Repeat checkbox -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="taskRepeatCheck{{ $task->id }}" name="is_repeat" value="1" {{ $isRepeat ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark">Repeat</label>
                    </div>

                    <!-- NO REPEAT AREA -->
                    <div id="noRepeatArea{{ $task->id }}" style="display: {{ $isRepeat ? 'none' : 'block' }};">
                        <div class="row align-items-center mb-3">
                            <div class="col-6">
                                <label class="fw-bold text-muted small mb-1 d-block">Start Date (Optional)</label>
                                <input type="date" name="start_date_no_repeat" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->start_date ? date('Y-m-d', strtotime($task->start_date)) : '' }}">
                            </div>
                            <div class="col-6">
                                <label class="fw-bold text-muted small mb-1 d-block">Due Date</label>
                                <input type="date" name="due_date" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->due_date ? date('Y-m-d', strtotime($task->due_date)) : date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-4">
                                <label class="fw-bold text-muted small mb-1 d-block">Time</label>
                                <input type="time" name="task_time_no_repeat" class="form-control border bg-white rounded-3 px-3 py-2 taskTimeInput{{ $task->id }}" value="{{ $task->task_time ? date('H:i', strtotime($task->task_time)) : '09:00' }}" {{ !$task->task_time ? 'disabled style=background-color:#e9ecef;' : '' }}>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input allDayCheck{{ $task->id }}" type="checkbox" name="all_day_no_repeat" value="1" {{ !$task->task_time ? 'checked' : '' }}>
                                    <label class="form-check-label small">All day</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- REPEAT AREA -->
                    <div id="repeatArea{{ $task->id }}" style="display: {{ $isRepeat ? 'block' : 'none' }};">
                        <div class="border rounded-3 p-3 mb-3">
                            <!-- Repeat type -->
                            <div class="mb-3">
                                <label class="fw-bold text-muted small mb-1 d-block">Repeat type</label>
                                <select name="repeat_type" class="form-select border bg-white rounded-3 px-3 py-2 taskRepeatType{{ $task->id }}">
                                    <option value="1" {{ $task->repeat_type == 1 ? 'selected' : '' }}>Daily</option>
                                    <option value="2" {{ $task->repeat_type == 2 ? 'selected' : '' }}>Weekly</option>
                                    <option value="3" {{ $task->repeat_type == 3 ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>

                            <!-- Interval -->
                            <div class="mb-3 d-flex align-items-center">
                                <span class="me-2 text-muted small">Every</span>
                                <input type="number" name="repeat_interval" class="form-control border bg-white rounded-3 text-center" value="{{ $task->repeat_interval ?? 1 }}" min="1" style="width:70px;">
                                <span class="ms-2 taskIntervalUnit{{ $task->id }} text-muted small">
                                    {{ $task->repeat_type == 2 ? 'week(s)' : ($task->repeat_type == 3 ? 'month(s)' : 'day(s)') }}
                                </span>
                            </div>

                            <!-- Weekly -->
                            <div class="mb-3 taskWeeklyOptions{{ $task->id }}" style="display: {{ $task->repeat_type == 2 ? 'block' : 'none' }};">
                                <label class="fw-bold text-muted small mb-1 d-block">Select days</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                        $selectedDays = is_string($task->days_of_week) ? json_decode($task->days_of_week, true) ?? [] : (array)$task->days_of_week;
                                    @endphp
                                    @foreach(['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'] as $value => $label)
                                        <div class="form-check form-check-inline me-1">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="{{ $value }}" {{ in_array($value, $selectedDays) ? 'checked' : '' }}>
                                            <label class="form-check-label small">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Monthly -->
                            <div class="mb-3 taskMonthlyOptions{{ $task->id }}" style="display: {{ $task->repeat_type == 3 ? 'block' : 'none' }};">
                                <label class="fw-bold text-muted small mb-1 d-block">Select day of month</label>
                                <select name="day_of_month" class="form-select border bg-white rounded-3 px-3 py-2">
                                    @for($i=1;$i<=31;$i++)
                                        <option value="{{ $i }}" {{ $task->day_of_month == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Start / End -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="fw-bold text-muted small mb-1 d-block">Start date</label>
                                    <input type="date" name="start_date_repeat" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->start_date ? date('Y-m-d', strtotime($task->start_date)) : date('Y-m-d') }}">
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold text-muted small mb-1 d-block">End date</label>
                                    <input type="date" name="end_date" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $task->end_date ? date('Y-m-d', strtotime($task->end_date)) : '' }}">
                                </div>
                            </div>

                            <!-- Time -->
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input allDayCheck2{{ $task->id }}" type="checkbox" name="all_day_repeat" value="1" {{ !$task->task_time ? 'checked' : '' }}>
                                        <label class="form-check-label small">All day</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="time" name="task_time_repeat" class="form-control border bg-white rounded-3 px-3 py-2 taskTimeInput2{{ $task->id }}" value="{{ $task->task_time ? date('H:i', strtotime($task->task_time)) : '09:00' }}" {{ !$task->task_time ? 'disabled style=background-color:#e9ecef;' : '' }}>
                                </div>
                            </div>
                        </div>
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
                intervalUnit.innerText = 'day(s)';
            } else if(this.value == "2"){
                intervalUnit.innerText = 'week(s)';
                weeklyOptions.style.display = 'block';
            } else if(this.value == "3"){
                intervalUnit.innerText = 'month(s)';
                monthlyOptions.style.display = 'block';
            }
        });

        // Toggle All Day
        const toggleTime = (checkbox, timeInput) => {
            if(!checkbox || !timeInput) return;
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    timeInput.disabled = true;
                    timeInput.style.backgroundColor = '#e9ecef';
                } else {
                    timeInput.disabled = false;
                    timeInput.style.backgroundColor = '';
                }
            });
        };
        toggleTime(allDayCheck, taskTimeInput);
        toggleTime(allDayCheck2, taskTimeInput2);
    });
</script>

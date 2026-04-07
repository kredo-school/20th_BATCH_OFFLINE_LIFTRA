<div class="modal fade mx-4 mt-3" id="add-task">
    <div class="modal-dialog">
        <form action="{{ route('tasks.store') }}" method="post">
            @csrf
            
            <div class="modal-content p-3 border-0 shadow-lg rounded-4">
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">Add Task</h5>

                    <!-- Task name -->
                    <div class="mb-3">
                        <input type="text" class="form-control border bg-white rounded-3 px-3 py-2" name="title" placeholder="Task name..." required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <textarea class="form-control border bg-white rounded-3 px-3 py-2" name="description" placeholder="Add description..."></textarea>
                    </div>

                    <!-- Priority -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small mb-1 d-block">Priority (Matrix)</label>
                        <select class="form-select border bg-white rounded-3 px-3 py-2" name="priority_type" required>
                            <option value="1">Urgent & Important</option>
                            <option value="2">Important & Not Urgent</option>
                            <option value="3">Not Important & Urgent</option>
                            <option value="4">Not Important & Not Urgent</option>
                        </select>
                    </div>

                    <!-- Repeat checkbox -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="taskRepeatCheck" name="is_repeat" value="1">
                        <label class="form-check-label fw-bold text-dark">Repeat</label>
                    </div>

                    <!-- NO REPEAT AREA -->
                    <div id="noRepeatArea">
                        <div class="row align-items-center mb-3">
                            <div class="col-6">
                                <label class="fw-bold text-muted small mb-1 d-block">Start Date (Optional)</label>
                                <input type="date" name="start_date_no_repeat" class="form-control border bg-white rounded-3 px-3 py-2" placeholder="Start Date">
                            </div>
                            <div class="col-6">
                                <label class="fw-bold text-muted small mb-1 d-block">Due Date</label>
                                <input type="date" name="due_date" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-4">
                                <label class="fw-bold text-muted small mb-1 d-block">Time</label>
                                <input type="time" name="task_time_no_repeat" class="form-control border bg-white rounded-3 px-3 py-2 taskTimeInput" value="09:00">
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input allDayCheck" type="checkbox" name="all_day_no_repeat" value="1">
                                    <label class="form-check-label small">All day</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- REPEAT AREA -->
                    <div id="repeatArea" style="display:none;">
                        <div class="border rounded-3 p-3 mb-3">
                            <!-- Repeat type -->
                            <div class="mb-3">
                                <label class="fw-bold text-muted small mb-1 d-block">Repeat type</label>
                                <select name="repeat_type" class="form-select border bg-white rounded-3 px-3 py-2 taskRepeatType">
                                    <option value="1">Daily</option>
                                    <option value="2">Weekly</option>
                                    <option value="3">Monthly</option>
                                </select>
                            </div>

                            <!-- Interval -->
                            <div class="mb-3 d-flex align-items-center">
                                <span class="me-2 text-muted small">Every</span>
                                <input type="number" name="repeat_interval" class="form-control border bg-white rounded-3 text-center" value="1" min="1" style="width:70px;">
                                <span class="ms-2 taskIntervalUnit text-muted small">day(s)</span>
                            </div>

                            <!-- Weekly -->
                            <div class="mb-3 taskWeeklyOptions" style="display:none;">
                                <label class="fw-bold text-muted small mb-1 d-block">Select days</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'] as $value => $label)
                                        <div class="form-check form-check-inline me-1">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" value="{{ $value }}">
                                            <label class="form-check-label small">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Monthly -->
                            <div class="mb-3 taskMonthlyOptions" style="display:none;">
                                <label class="fw-bold text-muted small mb-1 d-block">Select day of month</label>
                                <select name="day_of_month" class="form-select border bg-white rounded-3 px-3 py-2">
                                    @for($i=1;$i<=31;$i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Start / End -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="fw-bold text-muted small mb-1 d-block">Start date</label>
                                    <input type="date" name="start_date_repeat" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold text-muted small mb-1 d-block">End date</label>
                                    <input type="date" name="end_date" class="form-control border bg-white rounded-3 px-3 py-2">
                                </div>
                            </div>

                            <!-- Time -->
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input allDayCheck2" type="checkbox" name="all_day_repeat" value="1">
                                        <label class="form-check-label small">All day</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="time" name="task_time_repeat" class="form-control border bg-white rounded-3 px-3 py-2 taskTimeInput2" value="09:00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end px-3 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const repeatCheck = document.getElementById('taskRepeatCheck');
        const noRepeatArea = document.getElementById('noRepeatArea');
        const repeatArea = document.getElementById('repeatArea');
        const repeatType = document.querySelector('.taskRepeatType');
        const weeklyOptions = document.querySelector('.taskWeeklyOptions');
        const monthlyOptions = document.querySelector('.taskMonthlyOptions');
        const intervalUnit = document.querySelector('.taskIntervalUnit');
        const allDayCheck = document.querySelector('.allDayCheck');
        const taskTimeInput = document.querySelector('.taskTimeInput');
        const allDayCheck2 = document.querySelector('.allDayCheck2');
        const taskTimeInput2 = document.querySelector('.taskTimeInput2');

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

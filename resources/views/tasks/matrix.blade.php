@foreach ($taskGroups as $key => $tasks) {{-- マトリックスの枠 --}}
    <div class="col-6 col-lg-5 px-1 px-md-3 mb-3 d-flex flex-column">
        <div class="matrix border rounded fw-bold flex-grow-1 p-2 d-flex flex-column
                @if($key === 'importantUrgent') border-danger bg-danger bg-opacity-10
                @elseif($key === 'importantNotUrgent') border-warning bg-warning bg-opacity-10
                @elseif($key === 'notImportantUrgent') border-info bg-info bg-opacity-10
                @elseif($key === 'notImportantNotUrgent') border-success bg-success bg-opacity-10
                @endif">

            <div class="mx-auto w-100 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">{{-- 各象限のタイトルとタスク数カウント --}}
                        <span class="matrix-title 
                            @if($key === 'importantUrgent') text-danger
                            @elseif($key === 'importantNotUrgent') text-warning
                            @elseif($key === 'notImportantUrgent') text-info
                            @elseif($key === 'notImportantNotUrgent') text-success
                            @endif">
                                {{[
                                    'importantUrgent' => 'Important & Urgent',
                                    'importantNotUrgent' => 'Important & Not Urgent',
                                    'notImportantUrgent' => 'Not Important & Urgent',
                                    'notImportantNotUrgent' => 'Not Important & Not Urgent'
                                ][$key] }}
                        </span>
                        <span class="count ms-auto px-1 px-md-2 py-1 rounded small">{{ $tasks->count() }}</span>
                    </div>
                    
                <div class="task-scroll flex-grow-1 px-2">
                    @foreach ($tasks as $task) {{-- カード中のタスク --}}
                        <div class="card mt-1 shadow-sm border-0">
                            <div class="card-body p-2">

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted due-date">Due date:{{ $task->due_date }}</div>

                                    <div class="dropdown">
                                        <button class="btn btn-sm p-0 text-muted" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical px-1 py-1 mx-0"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end p-0 shadow-sm" style="min-width: 120px;">
                                            <li>
                                                <a class="dropdown-item text-primary py-1" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}"><i class="fa-solid fa-pen-to-square me-2"></i>Edit</a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger py-1" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}"><i class="fa-solid fa-trash-can me-2"></i>Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="d-flex w-100">
                                    <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-flex w-100 mb-0">
                                        @csrf
                                        @method('PATCH')

                                        <input class="form-check-input flex-shrink-0 mt-1" id="task" name="task"
                                            type="checkbox"
                                            onchange="this.form.submit()"
                                            {{ $task->completed ? 'checked' : '' }}
                                        >
                                        <div class="ms-2 my-auto form-label text-truncate task-title mb-0" style="min-width: 0;" title="{{ $task->title }}"> {{-- タスク名 --}}
                                            {{ $task->title }}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            @foreach($tasks as $task)
                @include('tasks.modals.edit-task', ['task' => $task])
                @include('tasks.modals.delete-task', ['task' => $task])
            @endforeach
            
        </div>
    </div>
@endforeach
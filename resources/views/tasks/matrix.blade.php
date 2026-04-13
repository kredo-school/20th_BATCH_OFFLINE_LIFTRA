{{-- <div class="container-fluid px-1 px-md-5">
    <div class="row justify-content-center mt-1"> --}}

        @foreach ($taskGroups as $key => $tasks) {{-- マトリックスの枠 --}}
            <div class="col-6 col-lg-6 ps-1 ps-md-2 mb-3 mx-0 d-flex flex-column">
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
                                            'importantUrgent' => __('Important & Urgent'),
                                            'importantNotUrgent' => __('Important & Not Urgent'),
                                            'notImportantUrgent' => __('Not Important & Urgent'),
                                            'notImportantNotUrgent' => __('Not Important & Not Urgent')
                                        ][$key] }}
                                </span>
                                <span class="count ms-auto px-1 px-md-2 py-1 rounded small">{{ $tasks->count() }}</span>
                            </div>
                            
                        <div class="task-scroll flex-grow-1 px-0">
                            @foreach ($tasks as $task) {{-- カード中のタスク --}}
                                <div class="card mb-1 shadow-sm border-0 px-1 task-card" style="cursor: pointer; transition: box-shadow 0.2s;" onclick="window.location.href='{{ route('tasks.show', $task->id) }}'" onmouseover="this.classList.replace('shadow-sm', 'shadow')" onmouseout="this.classList.replace('shadow', 'shadow-sm')">
                                    <div class="card-body p-1 py-lg-2 d-flex align-items-center">

                                        <div class="flex-shrink-0">
                                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="mb-0 d-flex align-items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input class="form-check-input mt-0" id="task{{ $task->id }}" name="task"
                                                    type="checkbox"
                                                    onclick="event.stopPropagation()"
                                                    {{ $task->completed ? 'checked' : '' }}
                                                >
                                            </form>
                                        </div>

                                        <div class="flex-grow-1 ms-2" style="min-width: 0;">
                                            <div class="task-title text-truncate {{ $task->completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}" style="line-height: 1.2;" title="{{ $task->title }}">
                                                {{ $task->title }}
                                            </div>
                                            <div class="text-muted due-date" style="margin-top: 0.15rem; line-height: 1.2;">
                                                {{ __('Due date: ') }}{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}
                                            </div>
                                        </div>

                                        <div class="dropdown flex-shrink-0 ms-1" onclick="event.stopPropagation()">
                                            <button class="btn btn-sm p-0 border-0 text-muted d-flex align-items-center" id="dropdownButton{{ $task->id }}" data-bs-toggle="dropdown">
                                                <i class="fa-solid fa-ellipsis-vertical px-1 py-0 mx-0"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end p-0 shadow-sm" style="min-width: 120px;">
                                                <li>
                                                    <a class="dropdown-item btn btn-light text-secondary py-1" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}"><i class="fa-solid fa-pen-to-square me-2"></i>{{ __('Edit') }}</a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item btn btn-light text-danger py-1" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}"><i class="fa-solid fa-trash-can me-2"></i>{{ __('Delete') }}</button>
                                                </li>
                                            </ul>
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
    </div>
</div>
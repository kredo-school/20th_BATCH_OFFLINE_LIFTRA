        @forelse($tasks as $task)
            <div class="col-12">
                <div class="card mb-1 border-success border-opacity-50 bg-light shadow-sm task-card" style="cursor: pointer; transition: all 0.2s;" onclick="window.location.href='{{ route('tasks.show', $task->id) }}'" onmouseover="this.classList.replace('bg-light', 'bg-white'); this.classList.replace('shadow-sm', 'shadow');" onmouseout="this.classList.replace('bg-white', 'bg-light'); this.classList.replace('shadow', 'shadow-sm');">
                    <div class="card-body py-1 py-lg-2 d-flex align-items-center">
                        
                        <!-- Left: Checkbox & SP Dot -->
                        <div class="d-flex align-items-center flex-shrink-0">
                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="mb-0 d-flex align-items-center">
                                @csrf
                                @method('PATCH')
                                <input class="form-check-input mt-0 border-success text-success" id="task{{ $task->id }}" name="task"
                                    type="checkbox"
                                    onclick="event.stopPropagation()"
                                    {{ $task->completed ? 'checked' : '' }}
                                >
                            </form>
                            
                            @php
                                preg_match('/text-(danger|warning|info|success)/', $task->priority_class, $matches);
                                $iconColorClass = $matches[0] ?? 'text-secondary';
                            @endphp
                            <i class="fa-solid fa-circle {{ $iconColorClass }} d-lg-none ms-2" style="font-size: 0.6rem;"></i>
                        </div>

                        <!-- Middle: Title, Date, Description -->
                        <div class="flex-grow-1 ms-2 list" style="min-width: 0;">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold task-title text-truncate text-decoration-line-through text-muted"
                                    style="min-width:0;" id="task_label_{{ $task->id }}" title="{{ $task->title }}">
                                    {{ $task->title }}
                                </span>
                                <span class="text-muted ms-1 small text-truncate" style="min-width:0;">
                                    @if ($task->repeat_type)
                                        <span class="d-none d-lg-inline">Ended: </span>{{ \Carbon\Carbon::parse($task->end_date)->format('Y-m-d') }}
                                    @else
                                        <span class="d-none d-lg-inline">Due date: </span>{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}
                                    @endif
                                </span>
                            </div>
                            @if($task->description)
                                <div class="list-description text-muted small mt-1 d-none d-lg-inline-block w-100" style="line-height: 1.3;">{{ $task->description }}</div>
                            @endif
                        </div>

                        <!-- Right: Priority & Ellipsis -->
                        <div class="d-flex flex-shrink-0 ms-2 align-items-center">
                            <span class="border rounded small px-2 {{ $task->priority_class }} d-none d-lg-inline opacity-75 text-truncate" style="min-width:0;">
                                {{ $task->priority_label }}
                            </span>

                            <div class="dropdown ms-3 d-flex align-items-center" onclick="event.stopPropagation()">
                                <button class="btn btn-sm btn-lg-md p-0 border-0 text-muted" id="dropdownButton{{ $task->id }}" data-bs-toggle="dropdown">
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
                </div>
            </div>

            @include('tasks.modals.edit-task', ['task' => $task])
            @include('tasks.modals.delete-task', ['task' => $task])

        @empty
            <div class="col-10 text-center py-5">
                <div class="text-muted mb-3"><i class="fa-solid fa-ghost fa-3x"></i></div>
                <h5 class="text-muted">No completed tasks yet.</h5>
            </div>
        @endforelse

        <div class="col-10 mt-3 pagination-wrapper">
            {{ $tasks->appends(['view' => 'completed'])->links() }}
        </div>

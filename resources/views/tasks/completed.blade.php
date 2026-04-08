<div class="container-fluid px-1 px-md-5">
    <div class="row justify-content-center mt-1">

        @forelse($tasks as $task)
            <div class="col-12">
                <div class="card mb-1 border-success border-opacity-50 bg-light shadow-sm task-card" style="cursor: pointer; transition: all 0.2s;" onclick="window.location.href='{{ route('tasks.show', $task->id) }}'" onmouseover="this.classList.replace('bg-light', 'bg-white'); this.classList.replace('shadow-sm', 'shadow');" onmouseout="this.classList.replace('bg-white', 'bg-light'); this.classList.replace('shadow', 'shadow-sm');">
                    <div class="card-body py-1 py-lg-2">
                        <div class="list d-flex justify-content-between align-items-center">

                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST"
                                class="d-flex align-items-center mb-0" style="min-width:0;">
                                @csrf
                                @method('PATCH')

                                <input class="form-check-input mt-0 me-2 border-success text-success flex-shrink-0"
                                    id="task{{ $task->id }}" name="task" type="checkbox"
                                    onclick="event.stopPropagation()"
                                    onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>

                                <span class="ms-2 fw-bold my-auto text-decoration-line-through text-muted text-truncate"
                                    style="min-width:0;" id="task_label_{{ $task->id }}">
                                    {{ $task->title }}
                                </span>

                                <span class="text-muted ms-1 small text-truncate" style="min-width:0;">
                                    @if ($task->repeat_type)
                                        <span class="d-none d-md-inline">Ended: </span>{{ \Carbon\Carbon::parse($task->end_date)->format('Y-m-d') }}
                                    @else
                                        <span class="d-none d-md-inline">Due date: </span>{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}
                                    @endif
                                </span>
                            </form>

                            @php
                                $priority = $task->priority_type;
                            @endphp

                            <div class="d-flex flex-shrink-0 ms-2">
                                {{-- Priority: PCのみ表示 --}}
                                <span class="border rounded small px-2 my-auto {{ $task->priority_class }} opacity-75 d-none d-lg-inline">
                                    {{ $task->priority_label }}
                                </span>

                                <div class="d-flex ms-3 py-0" onclick="event.stopPropagation()">
                                    <a class="dropdown-item btn btn-light text-secondary hover" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}"><i class="fa-solid fa-pen-to-square"></i></a>

                                    <button class="dropdown-item btn btn-light text-danger hover ms-1" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                            </div>
                        </div>
                        {{-- description: SPでは2行まで省略 --}}
                        <p class="list-description text-muted small mt-1 d-none d-lg-inline">{{ $task->description }}</p>
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
    </div>
</div>

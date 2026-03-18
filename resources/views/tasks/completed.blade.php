<div class="container-fluid px-1 px-md-5">
    <div class="row justify-content-center mt-1">

        @forelse($tasks as $task)
            <div class="col-12">
                <div class="card mb-1 border-success border-opacity-50 bg-light">
                    <div class="card-body py-1 py-lg-2">
                        <div class="list d-flex justify-content-between align-items-center">

                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST"
                                class="d-flex align-items-center mb-0" style="min-width:0;">
                                @csrf
                                @method('PATCH')

                                <input class="form-check-input mt-0 me-2 border-success text-success flex-shrink-0"
                                    id="task{{ $task->id }}" name="task" type="checkbox"
                                    onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>

                                <div class="ms-2 fw-bold my-auto form-label text-decoration-line-through text-muted text-truncate"
                                    style="min-width:0;" id="task_label_{{ $task->id }}">
                                    {{ $task->title }}
                                </div>

                                <span class="text-muted ms-1 small text-truncate" style="min-width:0;">
                                    @if ($task->repeat_type)
                                        <span class="d-none d-md-inline">Ended: </span>{{ $task->end_date }}
                                    @else
                                        <span class="d-none d-md-inline">Due date: </span>{{ $task->due_date }}
                                    @endif
                                </span>
                            </form>

                            @php
                                $priority = $task->priority_type;
                            @endphp

                            <div class="d-flex flex-shrink-0 ms-2">
                                {{-- Priority: PCのみ表示 --}}
                                <span class="border rounded fw-bold px-2 my-auto {{ $task->priority_class }} opacity-75 d-none d-lg-inline">
                                    {{ $task->priority_label }}
                                </span>

                                <div class="d-flex ms-2 py-0">
                                    <a class="dropdown-item text-primary hover" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}"><i class="fa-solid fa-pen-to-square"></i></a>

                                    <button class="dropdown-item text-danger hover ms-1" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}"><i class="fa-solid fa-trash-can"></i></button>
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

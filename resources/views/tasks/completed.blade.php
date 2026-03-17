<div class="container me-5">
    <div class="row justify-content-center mt-3">


        @forelse($tasks as $task)
            <div class="col-10">
                <div class="card mb-2 border-success border-opacity-25 bg-light">
                    <div class="card-body pb-0">
                        <div class="d-flex justify-content-between align-items-center">

                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST"
                                class="d-flex align-items-center mb-0">
                                @csrf
                                @method('PATCH')

                                <input class="form-check-input mt-0 me-2 border-success text-success"
                                    id="task{{ $task->id }}" name="task" type="checkbox"
                                    onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>
                                <span class="text-muted ms-3 small">
                                    @if ($task->repeat_type)
                                        Ended: {{ $task->end_date }}
                                    @else
                                        Due date: {{ $task->due_date }}
                                    @endif
                                </span>

                                <div class="ms-3 fw-bold my-auto form-label text-decoration-line-through text-muted"
                                    id="task_label_{{ $task->id }}">
                                    {{ $task->title }}
                                </div>
                            </form>

                            @php
                                $priority = $task->priority_type;
                            @endphp

                            <div class="d-flex">
                                <span
                                    class="border mx-auto rounded fw-bold px-2 my-auto {{ $task->priority_class }} opacity-75">
                                    {{ $task->priority_label }}
                                </span>

                                <div class="dropdown">
                                    <button class="btn btn-sm" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu p-0">
                                        <li>
                                            <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal"
                                                data-bs-target="#editTaskModal{{ $task->id }}"><i
                                                    class="fa-solid fa-pen-to-square"></i>Edit</a>
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteTaskModal{{ $task->id }}"><i
                                                    class="fa-solid fa-trash-can"></i>Delete</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mt-2">{{ $task->description }}</p>
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

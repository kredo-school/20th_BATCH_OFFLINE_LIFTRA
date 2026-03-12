@foreach($tasks as $task)

<div class="col-10">
    <div class="card mb-2">
        <div class="card-body pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-flex">
                    @csrf
                    @method('PATCH')

                    <input class="d-flex" id="task" name="task"
                        type="checkbox"
                        onchange="this.form.submit()"
                        {{ $task->completed ? 'checked' : '' }}
                    >
                <span class="text-muted ms-2">Due date:{{ $task->due_date }}</span>

                    <div class="ms-3 fw-bold my-auto form-label" id="task"> {{-- タスク名 --}}
                        {{ $task->title }}
                    </div>
                </form>

                @php
                    $priority = $task->priority_type;
                @endphp

                <div class="d-flex">
                    <span class="border mx-auto rounded fw-bold px-2 my-auto {{ $task->priority_class }}">
                        {{ $task->priority_label }}
                    </span>

                    <div class="dropdown">
                        <button class="btn btn-sm" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>

                        <ul class="dropdown-menu p-0">
                            <li>
                                <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}"><i class="fa-solid fa-pen-to-square"></i>Edit</a>
                            </li>
                            <li>
                                <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}"><i class="fa-solid fa-trash-can"></i>Delete</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <p>{{ $task->description }}</p>
        </div>
    </div>
</div>

    @include('tasks.modals.edit-task', ['task' => $task])
    @include('tasks.modals.delete-task', ['task' => $task])

@endforeach

<div class="col-10 mt-3 me-5 d-flex justify-content-center border">
    {{ $tasks->appends(['view' => 'list'])->links() }}
</div>
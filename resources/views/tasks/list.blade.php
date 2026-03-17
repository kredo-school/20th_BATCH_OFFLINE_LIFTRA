@foreach($tasks as $task)

<div class="col-10">
    <div class="card mb-2">
        <div class="card-body pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-flex align-items-center" style="min-width:0;">
                    @csrf
                    @method('PATCH')

                    <input class="flex-shrink-0" id="task" name="task"
                        type="checkbox"
                        onchange="this.form.submit()"
                        {{ $task->completed ? 'checked' : '' }}
                    >
                    {{-- Due date: PC表示 / 日付のみ: SP表示 --}}
                    <span class="text-muted ms-2 small flex-shrink-0">
                        <span class="d-none d-md-inline">Due date:</span>{{ $task->due_date }}
                    </span>

                    <div class="ms-2 fw-bold my-auto form-label text-truncate" style="min-width:0;"> {{-- タスク名 --}}
                        {{ $task->title }}
                    </div>
                </form>

                @php
                    $priority = $task->priority_type;
                @endphp

                <div class="d-flex flex-shrink-0 ms-2">
                    {{-- Priority: PCのみ表示 --}}
                    <span class="border rounded fw-bold px-2 my-auto {{ $task->priority_class }} d-none d-md-inline">
                        {{ $task->priority_label }}
                    </span>

                    <div class="d-flex ms-2">
                        <a class="dropdown-item text-primary hover" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}"><i class="fa-solid fa-pen-to-square"></i></a>

                        <button class="dropdown-item text-danger hover ms-1" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
            </div>
            {{-- description: SPでは2行まで省略 --}}
            <p class="list-description text-muted small mt-1">{{ $task->description }}</p>
        </div>
    </div>
</div>

    @include('tasks.modals.edit-task', ['task' => $task])
    @include('tasks.modals.delete-task', ['task' => $task])

@endforeach

<div class="col-10 my-3 pagination-wrapper">
    {{ $tasks->appends(['view' => 'list'])->links() }}
</div>
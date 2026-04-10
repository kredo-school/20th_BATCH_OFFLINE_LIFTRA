<div class="container-fluid px-1 px-md-5">
    <div class="row justify-content-center mt-1">

        @forelse($tasks as $task)

        <div class="col-12 col-lg-12">
            <div class="card mb-1 task-card" style="cursor: pointer; transition: all 0.2s;" onclick="window.location.href='{{ route('tasks.show', $task->id) }}'" onmouseover="this.classList.add('shadow-sm')" onmouseout="this.classList.remove('shadow-sm')">
                <div class="card-body py-1 py-lg-2">
                    <div class="list d-flex justify-content-between align-items-center">
                        <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-flex align-items-center" style="min-width:0;">
                            @csrf
                            @method('PATCH')

                            <input class="flex-shrink-0 " id="task" name="task"
                                type="checkbox"
                                onclick="event.stopPropagation()"
                                {{ $task->completed ? 'checked' : '' }}
                            >

                            @php
                                preg_match('/text-(danger|warning|info|success)/', $task->priority_class, $matches);
                                $iconColorClass = $matches[0] ?? 'text-secondary';
                            @endphp
                            <i class="fa-solid fa-circle {{ $iconColorClass }} d-lg-none my-auto ms-2" style="font-size: 0.6rem;"></i>

                            <span class="ms-2 fw-bold my-auto text-truncate {{ $task->completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}"
                                style="min-width:0;" title="{{ $task->title }}"> {{-- タスク名 --}}
                                {{ $task->title }}
                            </span>

                            {{-- Due date: PC表示 / 日付のみ: SP表示 --}}
                            <span class="text-muted ms-1 small text-truncate" style="min-width:0;">
                                <span class="d-none d-lg-inline">{{ __('Due date: ') }}</span>{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}
                            </span>
                        </form>

                        @php
                            $priority = $task->priority_type;
                        @endphp

                        <div class="d-flex flex-shrink-1 ms-2 py-0 align-items-center">
                            {{-- Priority: PCのみ表示 --}}
                            <span class="border rounded fw-bold small px-2 my-auto {{ $task->priority_class }} d-none d-lg-inline opacity-75 text-truncate" style="min-width:0;">
                                {{ $task->priority_label }}
                            </span>

                            <div class="dropdown ms-3 d-flex align-items-center" onclick="event.stopPropagation()">
                                <button class="btn btn-sm btn-lg-md p-0 border-0 text-muted" id="dropdownButton" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical px-1 py-0 mx-0"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-0 shadow-sm" style="min-width: 120px;">
                                    <li>
                                        <a class="dropdown-item btn btn-light text-secondary py-1" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}"><i class="fa-solid fa-pen-to-square me-2"></i>Edit</a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item btn btn-light text-danger py-1" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}"><i class="fa-solid fa-trash-can me-2"></i>Delete</button>
                                    </li>
                                </ul>
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
                <h5 class="text-muted">{{ __('No tasks yet.') }}</h5>
            </div>
        @endforelse

        <div class="col-10 my-3 pagination-wrapper">
            {{ $tasks->appends(['view' => 'list'])->links() }}
        </div>
    </div>
</div>
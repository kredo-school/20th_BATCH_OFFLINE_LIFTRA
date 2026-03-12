@foreach ($taskGroups as $key => $tasks) {{-- マトリックスの枠 --}}
        <div class="matrix col-5 border mb-3 ms-3 rounded fw-bold
                @if($key === 'importantUrgent') border-danger bg-danger bg-opacity-10
                @elseif($key === 'importantNotUrgent') border-warning bg-warning bg-opacity-10
                @elseif($key === 'notImportantUrgent') border-info bg-info bg-opacity-10
                @elseif($key === 'notImportantNotUrgent') border-success bg-success bg-opacity-10
                @endif">

            <div class="mx-auto my-2">
                    <div class="d-flex justify-content-between align-items-center">{{-- 各象限のタイトルとタスク数カウント --}}
                        <span class=" 
                            @if($key === 'importantUrgent') text-danger h4
                            @elseif($key === 'importantNotUrgent') text-warning h4
                            @elseif($key === 'notImportantUrgent') text-info h4
                            @elseif($key === 'notImportantNotUrgent') text-success h4
                            @endif">
                                {{[
                                    'importantUrgent' => 'Important & Urgent',
                                    'importantNotUrgent' => 'Important & Not Urgent',
                                    'notImportantUrgent' => 'Not Important & Urgent',
                                    'notImportantNotUrgent' => 'Not Important & Not Urgent'
                                ][$key] }}
                        </span>
                        <span class="count me-3 px-2 py-1 rounded">{{ $tasks->count() }}</span>
                    </div>
                    
                <div class="task-scroll pe-3">
                    @foreach ($tasks as $task) {{-- カード中のタスク --}}
                        <div class="card mt-1">
                            <div class="card-body py-1 pe-0">

                                <span class="text-muted">Due date:{{ $task->due_date }}</span>

                                <div class="d-flex justify-content-between align-items-center">
                                    <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PATCH')

                                        <input class="d-flex" id="task" name="task"
                                            type="checkbox"
                                            onchange="this.form.submit()"
                                            {{ $task->completed ? 'checked' : '' }}
                                        >
                                        <div class="ms-2 my-auto form-label" id="task"> {{-- タスク名 --}}
                                            {{ $task->title }}
                                        </div>
                                    </form>

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
                        </div>
                    @endforeach
                </div>
            </div>
            
            @foreach($tasks as $task)
                @include('tasks.modals.edit-task', ['task' => $task])
                @include('tasks.modals.delete-task', ['task' => $task])
            @endforeach
            
        </div>
@endforeach
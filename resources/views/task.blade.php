@extends('layouts.app')

@section('content')

<x-page-header 
    title="Enter Email to Change Password"
    subtitle="Reset Password URL will be sent to your email"
>
    <a href="#" class="btn btn-light rounded-3 px-4">
        <i class="fa-solid fa-plus"></i>
        Add Categories
    </a>
</x-page-header>

<div class="container">
    <div class="row justify-content-center mt-3">
        <div class="col-6 border border-danger mt-1">
            <div class="mx-auto my-1">
                <span class="text-danger">{{ __('tasks.important_urgent') }}</span>
                <span class="bg-secondary text-end">{{$importantUrgent->count()}}</span>

                @foreach ($importantUrgent as $task)
                    <div class="card mb-2">
                        <div class="card-body">
                            <p class="ms-2 text-">{{ $task->due_date }}</p>
                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input 
                                    type="checkbox"
                                    name="is_completed"
                                    id="task-{{ $task->id }}"
                                    onchange="this.form.submit()"
                                    {{ $task->is_completed ? 'checked' : '' }}
                                >
                            </form>
                            <div class="py-2">
                                {{ $task->title }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-6 border border-warning mt-1">
            <div class="mx-auto my-1">
                <span class="text-warning">{{ __('tasks.important_not_urgent') }}</span>
                <span class="bg-secondary text-end">{{ $importantNotUrgent->count()}}</span>

                @foreach ($importantNotUrgent as $task)
                    <div class="card mb-2">
                        <div class="card-body">
                            <p class="ms-2 text-">{{ $task->due_date }}</p>
                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input 
                                    type="checkbox"
                                    name="is_completed"
                                    id="task-{{ $task->id }}"
                                    onchange="this.form.submit()"
                                    {{ $task->is_completed ? 'checked' : '' }}
                                >
                            </form>
                            <div class="py-2">
                                {{ $task->title }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-6 border border-success mt-1">
            <div class="mx-auto my-1">
                <span class="text-success">{{ __('tasks.not_important_urgent') }}</span>
                <span class="bg-secondary text-end">{{$notImportantUrgent->count()}}</span>

                @foreach ($notImportantUrgent as $task)
                    <div class="card mb-2">
                        <div class="card-body">
                            <p class="ms-2 text-">{{ $task->due_date }}</p>
                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input 
                                    type="checkbox"
                                    name="is_completed"
                                    id="task-{{ $task->id }}"
                                    onchange="this.form.submit()"
                                    {{ $task->is_completed ? 'checked' : '' }}
                                >
                            </form>
                            <div class="py-2">
                                {{ $task->title }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-6 border border-primary mt-1">
            <div class="mx-auto my-1">
                <span class="text-primary">{{ __('tasks.not_important_not_urgent') }}</span>
                <span class="bg-secondary text-end">{{$notImportantNotUrgent->count()}}</span>

                @foreach ($notImportantNotUrgent as $task)
                    <div class="card mb-2">
                        <div class="card-body">
                            <p class="ms-2 text-">{{ $task->due_date }}</p>
                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input 
                                    type="checkbox"
                                    name="is_completed"
                                    id="task-{{ $task->id }}"
                                    onchange="this.form.submit()"
                                    {{ $task->is_completed ? 'checked' : '' }}
                                >
                            </form>
                            <div class="py-2">
                                {{ $task->title }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
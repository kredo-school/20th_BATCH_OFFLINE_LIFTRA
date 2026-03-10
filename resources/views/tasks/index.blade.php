@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
@endpush

@section('content')

<x-page-header title="Tasks" subtitle="Organize and prioritize your tasks">
    <button class="btn btn-light rounded-3 px-4"
            data-bs-toggle="modal" data-bs-target="">
        <i class="fa-solid fa-plus"></i>Add Tasks
    </button>
</x-page-header>

    {{-- modal here --}}
    @include('tasks.modals.add-task')

<div class="container me-5">
    <div class="row justify-content-center mt-3">
        <div class="col-10 mb-3">
            <a href="{{ route('tasks.index', ['view' => 'matrix']) }}"
                class="btn {{ $view === 'matrix' ? 'btn-secondary' : 'btn-outline-secondary' }} col-2">
                Matrix View
            </a>

            <a href="{{ route('tasks.index', ['view' => 'list']) }}"
                class="btn {{ $view === 'list' ? 'btn-secondary' : 'btn-outline-secondary' }} col-2">
                List View
            </a>
        </div>

        @if($view === 'list')
            @include('tasks.list')
        @else
            @include('tasks.matrix')
        @endif
    </div>
</div>

@endsection
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
@endpush

@section('content')

<x-page-header title="Tasks" subtitle="Organize and prioritize your tasks">
    <button class="btn btn-light rounded-3 my-auto px-lg-4 px-md-3 px-2 text-primary-6366F1 btn-responsive"
            data-bs-toggle="modal" data-bs-target="#add-task">
        <i class="fa-solid fa-plus"></i><span class="btn-text">Add Tasks</span>
    </button>
</x-page-header>

    {{-- modal here --}}
    @include('tasks.modals.add-task')

<div class=" container-fluid px-3 px-md-5">
    <div class="row justify-content-center mt-3">
        <div class="view-buttons col-11 mb-3">
            <a href="{{ route('tasks.index', ['view' => 'matrix']) }}"
                class="btn {{ $view === 'matrix' ? 'btn-secondary' : 'btn-outline-secondary' }} col-3 col-md-3 col-lg-2">
                <i class="fa-solid fa-table-cells-large"></i> Matrix View
            </a> 

            <a href="{{ route('tasks.index', ['view' => 'list']) }}"
                class="btn {{ $view === 'list' ? 'btn-secondary' : 'btn-outline-secondary' }} col-3 col-md-3 col-lg-2">
                <i class="fa-solid fa-list"></i> List View
            </a>
            
            <a href="{{ route('tasks.index', ['view' => 'completed']) }}"
                class="btn {{ $view === 'completed' ? 'btn-success' : 'btn-outline-success' }} col-4 col-md-4 col-lg-3">
                <i class="fa-solid fa-check-circle"></i> Completed
            </a>
        </div>

        @if($view === 'list')
            @include('tasks.list')
        @elseif($view === 'completed')
            @include('tasks.completed')
        @else
            @include('tasks.matrix')
        @endif
    </div>
</div>

@endsection
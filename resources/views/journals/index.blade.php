@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/journal.css') }}">
@endpush

@section('content')

<x-page-header title="Journal" subtitle="Reflect on your journey">
    @if(isset($view) && in_array($view, ['create', 'edit']))
        <a href="{{ route('journals.index') }}" class="btn btn-light rounded-3 px-4 btn-responsive">
            <i class="fa-solid fa-arrow-left"></i> <span class="btn-text">Back</span>
        </a>
    @else
        <a href="{{ route('journals.index', ['view' => 'create']) }}" class="btn btn-light rounded-3 px-4 btn-responsive">
            <i class="fa-solid fa-plus"></i> <span class="btn-text">Add Journal</span>
        </a>
    @endif
</x-page-header> 

@if(isset($view) && $view === 'create')
    @include('journals.create')
@elseif(isset($view) && $view === 'edit')
    @include('journals.edit')
@else
    @include('journals.list')
@endif
@endsection



@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/journal.css') }}">
<style>
    .header-action-btn {
        transition: all 0.2s ease;
    }
    .header-action-btn:hover, .header-action-btn:hover i, .header-action-btn:hover span {
        color: #6366F1 !important;
    }
</style>
@endpush

@section('content')

<x-page-header title="{{ __('Journals') }}" subtitle="{{ __('Reflect on your journey') }}">
    @if(isset($view) && in_array($view, ['create', 'edit']))
        <a href="{{ route('journals.index') }}" class="btn btn-light rounded-3 px-4 btn-responsive text-primary-6366F1 header-action-btn">
            <i class="fa-solid fa-arrow-left"></i> <span class="btn-text">{{ __('Back') }}</span>
        </a>
    @else
        <a href="{{ route('journals.index', ['view' => 'create']) }}" class="btn btn-light rounded-3 px-4 text-primary-6366F1 btn-responsive header-action-btn">
            <i class="fa-solid fa-plus"></i> <span class="btn-text">{{ __('Add Journal') }}</span>
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



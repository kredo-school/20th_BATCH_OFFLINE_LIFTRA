@extends('layouts.app')

@section('content')

<x-page-header 
    title="LifePlan"
    subtitle="Your roadmap to fulfilling life"
>
    <a href="#" class="btn btn-light rounded-3 px-4 text-primary-6366F1" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fa-solid fa-plus text-primary-6366F1"></i>
        Add Categories
    </a>
</x-page-header>

@include('lifeplan.modals.add-category')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-sm-6">

            <div class="card shadow-sm rounded-3 p-4 mx-5" style="position: relative; top: -30px;">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <i class="fa-solid fa-star fs-4 text-primary"></i>

                    <div>
                        <div class="text-muted small">My Primary Life Goal</div>
                        <div class="fw-bold text-dark">
                            My primary life goal is to be able to live without worrying about money
                        </div>
                    </div>
                </div>

                <!-- Overall Progress -->
                <div class="mb-2 text-muted small">
                    Overall Progress
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="progress flex-grow-1" style="height: 8px; border-radius: 10px;">
                        <div 
                            class="progress-bar" 
                            role="progressbar" 
                            style="width: 60%; background: linear-gradient(90deg, #6366F1, #8B5CF6);" 
                            aria-valuenow="0" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                        </div>
                    </div>

                    <div class="fw-semibold text-primary-6366F1">
                        60%
                    </div>
                </div>
            </div>

            <!-- Life Categories Section -->
            <div class="mt-5 mx-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0">Life Categories</h5>
                </div>

                @if(isset($categories) && $categories->count())
                    <div class="row g-4">
                        @foreach($categories as $category)
                            <div class="col-md-6 col-lg-4">
                                <div class="card shadow-sm rounded-4 p-4 h-100">

                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center gap-3">

                                            <!-- Icon -->
                                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; background-color: {{ $category->color->code ?? '#EEF2FF' }}{{ !isset($category->color->code) ? '' : '20' }};">
                                                
                                                <i class="fa-solid {{ $category->icon->class ?? 'fa-folder' }}"
                                                   style="color: {{ $category->color->code ?? '#6366F1' }};"></i>
                                            </div>

                                            <!-- Category Name -->
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $category->name }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $category->goals->count() }} goals
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Percentage -->
                                        <div class="fw-semibold"
                                             style="color: {{ $category->color->code ?? '#6366F1' }};">
                                            {{ $category->progress }}%
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="progress" style="height: 8px; border-radius: 10px;">
                                        <div class="progress-bar"
                                             role="progressbar"
                                             style="width: {{ $category->progress }}%; background-color: {{ $category->color->code ?? '#6366F1' }};"
                                             aria-valuenow="{{ $category->progress }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">
                        No categories found. Please add a category.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
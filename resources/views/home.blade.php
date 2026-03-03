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
    <div class="row justify-content-center">
        <div class="col-lg-12 col-sm-6">

            <div class="card shadow-sm rounded-3 p-4 mx-5" style="position: relative; top: -30px;">
                <div class="d-flex align-items-start gap-3">
                    <i class="fa-solid fa-star fs-4 text-primary"></i>

                    <div>
                        <div class="text-muted small">My Primary Life Goal</div>
                        <div class="fw-bold text-dark">
                            My primary life goal is to be able to live without worrying about money
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
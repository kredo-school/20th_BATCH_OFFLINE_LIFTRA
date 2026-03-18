<<<<<<< Updated upstream
<div class="page-header">
    <div class="container-fluid h-100 px-3 px-md-5">
        <div class="row align-items-center h-100 flex-nowrap">
            
            <div class="col">
                <h1 class="mb-0 mt-3">{{ $title }}</h1>
                @if(isset($subtitle))
                    <p class="mb-0 mt-1">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="col-auto text-end">
                {{ $slot }}
=======
<div class="page-header shadow-sm">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-lg-9 col-8 ps-lg-5 ps-4">
                <h1 class="mb-1 fw-bold">{{ $title }}</h1>
                @if(isset($subtitle))
                    <p class="mb-0 opacity-75">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="col-lg-3 col-4 text-end pe-lg-5 pe-3">
                <div class="header-actions d-flex justify-content-end align-items-center">
                    {{ $slot }}
                </div>
>>>>>>> Stashed changes
            </div>

        </div>
    </div>
</div>
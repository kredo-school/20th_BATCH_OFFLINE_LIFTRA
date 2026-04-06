<div class="page-header shadow-sm">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-lg-7 col-md-6 col-12 ps-lg-5 ps-4">
                <h1 class="mb-1 fw-bold">{{ $title }}</h1>
                @if(isset($subtitle))
                    <p class="mb-0 opacity-75">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="col-lg-5 col-md-6 col-12 text-md-end text-start mt-md-0 mt-3 pe-lg-5 pe-3">
                <div class="header-actions d-flex justify-content-md-end justify-content-start align-items-center gap-2">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </div>
</div>
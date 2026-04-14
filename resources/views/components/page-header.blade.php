<div class="page-header shadow-sm">
    <div class="container-fluid px-3 px-md-5">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6 col-9 ps-lg-5 ps-4">
                <h1 class="mb-1 fw-bold">{{ $title }}</h1>
                @if(isset($subtitle))
                    <p class="mb-0 opacity-75">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="col-lg-5 col-md-6 col-3 text-end pe-lg-5 pe-3 mb-2">
                <div class="header-actions d-flex justify-content-end align-items-center gap-2">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
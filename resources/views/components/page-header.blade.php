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
            </div>

        </div>
    </div>
</div>
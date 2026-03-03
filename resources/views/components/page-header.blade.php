<div class="page-header" style="height:80px;">
    <div class="container h-100">
        <div class="row align-items-start h-100">
            
            <div class="col-lg-8 ps-5">
                <h1 class="mb-0">{{ $title }}</h1>
                @if(isset($subtitle))
                    <p class="mb-0">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="col-lg-4 text-end pe-5">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>
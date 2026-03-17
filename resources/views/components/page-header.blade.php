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
            </div>

        </div>
    </div>
</div>
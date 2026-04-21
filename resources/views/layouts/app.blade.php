<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    @stack('styles') <!-- 特定のファイルでだけcss読み込みたい時のためにつけた -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- css --}}
    
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/task.css') }}">
    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=1.1.7">
    <link rel="stylesheet" href="{{ asset('css/app-tour.css') }}">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


</head>
<body>
<div id="app">

    <div class="app-layout" id="layout-wrapper">
        {{-- Sidebar Overlay (Mobile) --}}
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        {{-- Sidebar --}}
        <aside class="sidebar d-flex flex-column justify-content-between">
            
            <div class="h-100 d-flex flex-column">
                {{-- SP Sidebar Header (Logo/Close) --}}
                <div class="sp-sidebar-header d-lg-none position-relative d-flex align-items-center justify-content-between px-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('favicon.png') }}" alt="Logo" style="width: 40px;">
                        <span class="fw-bold">Liftra</span>
                    </div>
                    <button id="sp-sidebar-close" class="sp-close-btn static">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="d-none d-lg-block">
                    <div class="d-flex align-items-center justify-content-between mb-1 px-2">
                        <a href="{{ Auth::check() && Auth::user()->role_id === 1 ? route('admin.dashboard') : route('home') }}" class="text-decoration-none text-dark d-flex align-items-center gap-3">
                            <div class="" style="width:40px; height:40px; overflow:hidden; border-radius:8px;">
                                <img src="{{ asset('favicon.png') }}" alt="App Logo" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <div class="logo-text fs-5 fw-bold">Liftra</div>
                        </a>
                        <i id="pc-sidebar-toggle" class="fa-solid fa-chevron-left pc-sidebar-toggle sidebar-toggle-open"></i>
                    </div>
                    <hr class="my-2">
                </div>

                {{-- Navigation --}}
                <nav class="nav flex-column gap-1 flex-grow-1">
                    {{-- Desktop Nav --}}
                    <div class="d-none d-lg-flex flex-column gap-1">
                        @if(Auth::check() && Auth::user()->role_id === 1)
                            {{-- Admin Menu --}}
                            <div class="small fw-bold text-muted px-2 mb-1">{{ __('Administration') }}</div>
                            <a href="{{ route('admin.dashboard') }}" class="nav-item-custom  {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-gauge-high"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('admin.users') }}" class="nav-item-custom {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> {{ __('User Management') }}
                            </a>
                        @else
                            {{-- General User Menu --}}
                            <a href="{{ route('home') }}" class="nav-item-custom {{ request()->routeIs('home') || request()->routeIs('lifeplan.*') ? 'active' : '' }}">
                                <i class="fa-regular fa-circle-dot"></i> {{ __('LifePlan') }}
                            </a>
                            
                            {{-- Lifeplan Sub-categories (PC) --}}
                            @if(request()->routeIs('home') || request()->routeIs('lifeplan.*'))
                                <div class="nav-sub-items mb-2">
                                    @foreach($sidebarCategories ?? [] as $sidebarCat)
                                        <a href="{{ route('lifeplan.category.show', $sidebarCat->id) }}" class="nav-sub-item">
                                            <i class="fa-solid {{ $sidebarCat->icon->class ?? 'fa-folder' }}" style="color: {{ $sidebarCat->color->code ?? '#6366f1' }}; width: 14px;"></i>
                                            {{ $sidebarCat->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <a href="{{ route('calendar.index') }}" class="nav-item-custom {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                                <i class="fa-regular fa-calendar"></i> {{ __('Calendar') }}
                            </a>

                            <a href="{{ route('tasks.index') }}" class="nav-item-custom {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                <i class="fa-regular fa-square-check"></i> {{ __('Task') }}
                            </a>

                            <a href="{{ route('habits.index') }}" class="nav-item-custom {{ request()->routeIs('habits.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-repeat"></i> {{ __('Habit') }}
                            </a>

                            <a href="{{ route('journals.index') }}" class="nav-item-custom {{ request()->routeIs('journals.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-open"></i> {{ __('Journal') }}
                            </a>
                        @endif
                    </div>

                    {{-- SP Specific Content --}}
                    @if(!Auth::check() || Auth::user()->role_id !== 1)
                        <div class="d-lg-none px-3">
                            <div class="section-title mb-2">{{ __('Life Categories') }}</div>
                            <div class="nav flex-column gap-1 mb-3">
                                @foreach($sidebarCategories ?? [] as $sidebarCat)
                                    <a href="{{ route('lifeplan.category.show', $sidebarCat->id) }}" class="nav-item-custom d-flex align-items-center gap-3 py-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: {{ $sidebarCat->color->code ?? '#6366f1' }}15;">
                                            <i class="fa-solid {{ $sidebarCat->icon->class ?? 'fa-folder' }} fs-5" style="color: {{ $sidebarCat->color->code ?? '#6366f1' }};"></i>
                                        </div>
                                        <span class="text-dark fw-medium">{{ $sidebarCat->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                            
                            <a href="#" class="btn btn-primary w-100 rounded-3 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2 mt-1" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="fa-solid fa-plus"></i> {{ __('Add Category') }}
                            </a>
                        </div>
                    @else
                        <div class="d-lg-none px-3 mt-2">
                            <div class="section-title mb-3">{{ __('Admin Quick Menu') }}</div>
                            <a href="{{ route('admin.dashboard') }}" class="nav-item-custom d-flex align-items-center gap-3 py-3 mb-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 bg-danger bg-opacity-10" style="width: 38px; height: 38px;">
                                    <i class="fa-solid fa-gauge-high text-danger"></i>
                                </div>
                                <span class="text-dark fw-bold">{{ __('Dashboard') }}</span>
                            </a>
                            <a href="{{ route('admin.users') }}" class="nav-item-custom d-flex align-items-center gap-3 py-3 {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 bg-primary bg-opacity-10" style="width: 38px; height: 38px;">
                                    <i class="fa-solid fa-users text-primary"></i>
                                </div>
                                <span class="text-dark fw-bold">{{ __('User Management') }}</span>
                            </a>
                        </div>
                    @endif
                </nav>

                {{-- Footer (PC & SP) --}}
                <div class="mt-auto pt-4 mb-1">
                    <hr class="m-0">
                    <a href="{{ route('notifications.index') }}" class="nav-item-custom my-1 {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i> {{ __('Notifications') }}
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="badge rounded-pill bg-danger ms-auto" style="font-size: 0.7rem;">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('settings.index') }}" class="nav-item-custom my-1 {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                       <i class="fa-solid fa-gear"></i> {{ __('Settings') }}
                    </a>
                    <hr class="m-0">
                    @auth
                    <a href="{{ route('profile.index') }}" 
                    class="d-flex align-items-center gap-3 p-2 rounded-4 text-decoration-none text-dark user-profile-card">
                        <div class="user-avatar shadow-sm flex-shrink-0">
                            @if(Auth::user()->profile_image)
                                <img src="{{ Auth::user()->profile_image }}" alt="User Avatar" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                            @else
                                <span class="avatar-initial w-100 h-100 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white fs-6" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                    {{ Auth::user() ? mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) : '' }}
                                </span>
                            @endif
                        </div>
                        <div class="user-info-text overflow-hidden w-100">
                            <div class="user-name fw-bold text-dark text-truncate" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                            <div class="user-email text-muted text-truncate" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                        </div>
                    </a>
                    @endauth
                </div>
            </div>

        </aside>


        {{-- Main Content --}}
        <main class="main-content flex-grow-1">
            {{-- SP Sidebar Toggle (Hamburger) --}}
            <button id="sidebar-toggle" class="sidebar-toggle d-lg-none">
                <i class="fa-solid fa-bars"></i>
            </button>

            {{-- PC Sidebar Toggle Closed (Chevron-right) --}}
            <div id="pc-sidebar-toggle-closed" class="sidebar-toggle-closed">
                <i class="fa-solid fa-chevron-right"></i>
            </div>

            @yield('content')
        </main>


    </div>

</div>

@include('partials.ai-chat')
@include('partials.app-tour')
@stack('modals')
@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const layoutWrapper = document.getElementById('layout-wrapper');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const pcSidebarToggle = document.getElementById('pc-sidebar-toggle');
        const pcSidebarToggleClosed = document.getElementById('pc-sidebar-toggle-closed');
        const spSidebarClose = document.getElementById('sp-sidebar-close');

        // Mobile Toggle
        sidebarToggle.addEventListener('click', function() {
            layoutWrapper.classList.toggle('sidebar-mobile-open');
        });

        spSidebarClose.addEventListener('click', function() {
            layoutWrapper.classList.remove('sidebar-mobile-open');
        });

        // PC Toggle
        pcSidebarToggle.addEventListener('click', function() {
            layoutWrapper.classList.add('sidebar-collapsed');
            document.body.classList.add('sidebar-collapsed');
        });

        pcSidebarToggleClosed.addEventListener('click', function() {
            layoutWrapper.classList.remove('sidebar-collapsed');
            document.body.classList.remove('sidebar-collapsed');
        });
        
        // Close sidebar when clicking overlay on mobile
        sidebarOverlay.addEventListener('click', function() {
            layoutWrapper.classList.remove('sidebar-mobile-open');
        });

        // Close mobile sidebar on window resize if it gets large
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                layoutWrapper.classList.remove('sidebar-mobile-open');
            }
        });

        // Close sidebar when clicking modal triggers inside it
        document.querySelectorAll('.sidebar [data-bs-toggle="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                layoutWrapper.classList.remove('sidebar-mobile-open');
            } );
        });
    });
</script>
    {{-- Bottom Nav (SP) --}}
    <div class="bottom-nav d-lg-none">
        @if(Auth::check() && Auth::user()->role_id === 1)
            {{-- Admin Bottom Nav --}}
            <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="flex: 1;">
                <i class="fa-solid fa-gauge-high"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('admin.users') }}" class="bottom-nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}" style="flex: 1;">
                <i class="fa-solid fa-users"></i>
                <span>{{ __('Users') }}</span>
            </a>
            <a href="{{ route('settings.index') }}" class="bottom-nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" style="flex: 1;">
                <i class="fa-solid fa-gear"></i>
                <span>{{ __('Settings') }}</span>
            </a>
        @else
            <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') || request()->routeIs('lifeplan.*') ? 'active' : '' }}">
                <i class="fa-regular fa-circle-dot"></i>
                <span>{{ __('LifePlan') }}</span>
            </a>
            <a href="{{ route('calendar.index') }}" class="bottom-nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ __('Calendar') }}</span>
            </a>
            <a href="{{ route('tasks.index') }}" class="bottom-nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <i class="fa-regular fa-square-check"></i>
                <span>{{ __('Task') }}</span>
            </a>
            <a href="{{ route('habits.index') }}" class="bottom-nav-item {{ request()->routeIs('habits.*') ? 'active' : '' }}">
                <i class="fa-solid fa-repeat"></i>
                <span>{{ __('Habit') }}</span>
            </a>
            <a href="{{ route('journals.index') }}" class="bottom-nav-item {{ request()->routeIs('journals.*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i>
                <span>{{ __('Journal') }}</span>
            </a>
        @endif
    </div>
</body>
</html>

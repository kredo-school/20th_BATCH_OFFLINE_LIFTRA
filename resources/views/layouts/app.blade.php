<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    @stack('styles') <!-- 特定のファイルでだけcss読み込みたい時のためにつけた -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- css --}}
    <link rel="stylesheet" href="public/css/style.css">
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/task.css') }}">
    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            /* background-color: #e5e7eb; アイコン背景 */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-avatar i {
            width: 100%;
            height: 100%;
            font-size: 40px; /* 丸枠いっぱいに表示 */
            line-height: 1;
            text-align: center;
            color: #9ca3af;  /* アイコン色 */
        }
    </style>
</head>
<body>
<div id="app">

    <div class="app-layout d-flex">

        {{-- Sidebar --}}
        <aside class="sidebar d-flex flex-column justify-content-between">

            <div>
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="text-decoration-none text-dark">
                    <div class="d-flex align-items-center gap-3 mb-4 px-2">
                        <div class="" style="width:48px; height:48px; overflow:hidden; border-radius:8px;">
                            <img src="{{ asset('favicon.png') }}" alt="App Logo" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        <div class="logo-text fs-5 fw-bold">Liftra</div>
                    </div>
                </a>
                <hr class="m-2">

                {{-- Navigation --}}
                <nav class="nav flex-column gap-1">

                    <a href="#" class="nav-item-custom {{ request()->routeIs('lifeplan.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-circle-dot"></i> LifePlan
                    </a>

                    <a href="{{ route('calendar.index') }}" class="nav-item-custom {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-calendar"></i> Calendar
                    </a>

                    <a href="{{ route('tasks.index') }}" class="nav-item-custom {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-square-check"></i> Task
                    </a>

                    <a href="{{ route('habits.index') }}" class="nav-item-custom {{ request()->routeIs('habits.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-repeat"></i> Habit
                    </a>

                    <a href="{{ route('journals.index') }}" class="nav-item-custom {{ request()->routeIs('journals.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-book-open"></i> Journal
                    </a>

                </nav>
            </div>

            {{-- Footer --}}
            <div>
                <hr class="mb-1">

                <a href="#" class="nav-item-custom">
                   <i class="fa-solid fa-gear"></i> Settings
                </a>
                <hr class="mt-1">

                @auth
                <a href="{{ route('profile.index') }}" 
                class="d-flex align-items-center gap-3 mt-3 px-2 rounded text-decoration-none text-dark user-block-link-hover">

                    {{-- ユーザーアイコン --}}
                    <div class="user-avatar flex-shrink-0">
                        @if(Auth::user()->profile_image)
                            <img src="{{ Auth::user()->profile_image }}" alt="User Avatar" class="rounded-circle">
                        @else
                            <span class="avatar-initial">
                                {{ Auth::user() ? mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) : '' }}
                            </span>
                        @endif
                    </div>

                    {{-- ユーザー情報 --}}
                    <div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-email">{{ Auth::user()->email }}</div>
                    </div>

                </a>
                @endauth
            </div>

        </aside>


        {{-- Main Content --}}
        <main class="main-content flex-grow-1">
            @yield('content')
        </main>

    </div>
</div>
@stack('scripts')
</body>
</html>

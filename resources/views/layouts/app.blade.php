<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
            background-color: #e5e7eb; /* アイコン背景 */
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
                <div class="d-flex align-items-center gap-3 mb-4 px-2">
                    <div class="" style="width:48px; height:48px; overflow:hidden; border-radius:8px;">
                        <img src="{{ asset('favicon.png') }}" alt="App Logo" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="logo-text fs-5 fw-bold">Liftra</div>
                </div>
                <hr class="m-2">

                {{-- Navigation --}}
                <nav class="nav flex-column gap-1">

                    <a href="#" class="nav-item-custom {{ request()->routeIs('lifeplan.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-circle-dot"></i> LifePlan
                    </a>

                    <a href="#" class="nav-item-custom {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-calendar"></i> Calendar
                    </a>

                    <a href="#" class="nav-item-custom {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-square-check"></i> Task
                    </a>

                    <a href="#" class="nav-item-custom {{ request()->routeIs('habits.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-repeat"></i> Habit
                    </a>

                    <a href="#" class="nav-item-custom {{ request()->routeIs('journal.*') ? 'active' : '' }}">
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
                <div class="user-block mt-3 px-2 d-flex align-items-center gap-3">

                    {{-- ユーザーアイコン --}}
                    <div class="user-avatar">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="User Avatar">
                        @else
                            <i class="fa-solid fa-circle-user"></i>
                        @endif
                    </div>

                    {{-- ユーザー情報 --}}
                    <div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-email">{{ Auth::user()->email }}</div>
                    </div>

                </div>
                @endauth
            </div>

        </aside>


        {{-- Main Content --}}
        <main class="main-content flex-grow-1 p-4">
            @yield('content')
        </main>

    </div>

</div>
</body>
</html>

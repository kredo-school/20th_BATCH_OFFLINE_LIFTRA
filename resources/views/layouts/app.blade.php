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

                {{-- Navigation --}}
                <nav class="nav flex-column gap-1">

                    <a href="#" class="nav-item-custom active">
                        LifePlan
                    </a>

                    <a href="#" class="nav-item-custom">
                        Calendar
                    </a>

                    <a href="#" class="nav-item-custom">
                        Task
                    </a>

                    <a href="#" class="nav-item-custom">
                        Habit
                    </a>

                    <a href="#" class="nav-item-custom">
                        Journal
                    </a>

                </nav>
            </div>

            {{-- Footer --}}
            <div>
                <hr>

                <a href="#" class="nav-item-custom">
                    Settings
                </a>

                @auth
                <div class="user-block mt-3 px-2">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-email">{{ Auth::user()->email }}</div>
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

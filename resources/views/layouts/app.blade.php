<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Inventory') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('layouts.navigation')

    <main class="main">
        <header class="topbar">
            <h1>@yield('title', 'Dashboard')</h1>
            <div class="user-box">
                <div>
                    <strong>{{ auth()->user()->full_name }}</strong>
                    <span class="role-tag">{{ auth()->user()->role }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm">Keluar</button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif
        @if(session('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif

        @yield('content')
    </main>
</body>
</html>

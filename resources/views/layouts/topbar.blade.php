<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HK Perhotelan')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/ra/dashboard_ra.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ra/absensi_ra.css') }}">
    @stack('styles')
</head>
<body>

{{-- TOPBAR --}}
<div class="topbar">
    <div class="topbar-left">
        <div class="topbar-logos">
            <img src="{{ asset('images/Logo SIG.png') }}" onerror="this.style.display='none'" alt="">
            <img src="{{ asset('images/LOGO PH.png') }}" onerror="this.style.display='none'" alt="">
        </div>
        <div>
            <div class="topbar-title">HK Perhotelan</div>
            <div class="topbar-subtitle">Room Attendant Panel</div>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-user">
            <div class="uname">{{ auth()->user()->name }}</div>
            <div class="urole">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
        
    </div>
</div>

{{-- CONTENT --}}
<div class="main-wrap">
    @if(session('error'))
        <div class="alert alert-error">⚠️ {{ session('error') }}</div>
    @endif
    @yield('content')
</div>

@stack('scripts')
</body>
</html>
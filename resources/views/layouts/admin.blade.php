<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HK Perhotelan')</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    @stack('styles')
</head>
<body>
<header class="topbar">
    <button class="hamburger sidebar-toggle">☰</button>
</header>

<div class="dashboard-wrapper" id="dashWrapper">
    <aside class="sidebar">
        <div class="sidebar-top">
            <button class="hamburger small sidebar-toggle">☰</button>
            <div class="side-logos">
                <img src="{{ asset('images/Logo SIG.png') }}" onerror="this.style.display='none'" alt="">
                <img src="{{ asset('images/LOGO PH.png') }}" onerror="this.style.display='none'" alt="">
            </div>
        </div>
        <div class="logo">
            <strong>HK Perhotelan</strong>
            <div style="font-size:11px;opacity:.7;margin-top:2px">{{ ucfirst(auth()->user()->role) }} Panel</div>
        </div>

        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-feather="grid"></i> Dashboard
            </a>
            <a href="{{ route('admin.rooms.index') }}" class="{{ request()->routeIs('admin.rooms*') ? 'active' : '' }}">
                <i data-feather="home"></i> Status Kamar
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i data-feather="users"></i> Kelola Akun
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance*') ? 'active' : '' }}">
                <i data-feather="calendar"></i> Absensi
            </a>
            <a href="{{ route('admin.history.index') }}" class="{{ request()->routeIs('admin.history*') ? 'active' : '' }}">
                <i data-feather="activity"></i> Log Aktivitas
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" style="margin-top:auto">
            @csrf
            <button type="submit" class="logout" style="width:100%;color:#7A0200;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer">
                <i data-feather="log-out"></i> Logout
            </button>
        </form>
    </aside>

    <div class="main-area">
        <div class="page-inner">
            <div class="page-header">
                <div>
                    <h1>@yield('page-title')</h1>
                    <p>@yield('page-subtitle', now()->translatedFormat('l, d F Y'))</p>
                </div>
                <span class="role-badge">{{ ucfirst(auth()->user()->role) }}</span>
            </div>

            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">⚠️ {{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script>feather.replace();</script>
<script src="{{ asset('js/sidebar.js') }}"></script>
@stack('scripts')
</body>
</html>
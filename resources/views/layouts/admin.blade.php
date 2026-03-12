<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HK Perhotelan')</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
        .page-header h1 { font-size:22px; font-weight:700; color:#1a1a1a; }
        .page-header p  { font-size:13px; color:#888; margin-top:2px; }
        .role-badge { padding:5px 14px; border-radius:20px; font-size:11px; font-weight:700;
            text-transform:uppercase; letter-spacing:.5px; background:#fde8e8; color:#7A0200; }
        .alert { padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:18px; }
        .alert-success { background:#e8f5e9; border:1px solid #a5d6a7; color:#2e7d32; }
        .alert-error   { background:#fde8e8; border:1px solid #ef9a9a; color:#c62828; }
        .card { background:#fff; border-radius:16px; padding:22px; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:20px; }
        .card-header { display:flex; justify-content:space-between; align-items:center;
            padding-bottom:14px; margin-bottom:16px; border-bottom:1px solid #f5f5f5; }
        .card-title { font-size:13px; font-weight:700; color:#7A0200; text-transform:uppercase; letter-spacing:.5px; }
        .tbl { width:100%; border-collapse:collapse; font-size:13px; }
        .tbl th { padding:10px 14px; background:#fdf5f5; color:#7A0200; font-size:11px; font-weight:700;
            text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid #f5e5e5; text-align:left; }
        .tbl td { padding:12px 14px; border-bottom:1px solid #fafafa; color:#444; }
        .tbl tr:last-child td { border-bottom:none; }
        .tbl tbody tr:hover td { background:#fffafa; }
        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-red    { background:#fde8e8; color:#c62828; }
        .badge-green  { background:#e8f5e9; color:#2e7d32; }
        .badge-blue   { background:#e3f2fd; color:#1565c0; }
        .badge-yellow { background:#fff8e1; color:#f57f17; }
        .badge-orange { background:#fff3e0; color:#e65100; }
        .badge-gray   { background:#f5f5f5; color:#666; }
        .btn-sm { display:inline-flex; align-items:center; gap:5px; padding:7px 14px;
            border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;
            border:none; font-family:inherit; transition:all .18s; text-decoration:none; }
        .btn-primary { background:#7A0200; color:#fff; }
        .btn-primary:hover { background:#9B0500; }
        .btn-secondary { background:#f5f5f5; color:#444; border:1px solid #e0e0e0; }
        .btn-secondary:hover { background:#ebebeb; }
        .btn-danger { background:#fde8e8; color:#c62828; }
        .btn-danger:hover { background:#fbd0d0; }
        .btn-success { background:#e8f5e9; color:#2e7d32; }
        .btn-success:hover { background:#d0edd2; }
        .form-group { margin-bottom:14px; }
        .form-label { display:block; font-size:12px; font-weight:600; color:#444; margin-bottom:6px; }
        .form-control { width:100%; padding:9px 12px; border:1.5px solid #e5e5e5; border-radius:9px;
            font-size:13px; font-family:inherit; outline:none; transition:border-color .2s; }
        .form-control:focus { border-color:#7A0200; box-shadow:0 0 0 3px rgba(122,2,0,.08); }
        select.form-control { cursor:pointer; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        .grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
        .grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
        .stat-box { background:#fff; border-radius:16px; padding:20px;
            box-shadow:0 2px 12px rgba(0,0,0,.06); display:flex; align-items:center; gap:16px; }
        .stat-icon { width:46px; height:46px; border-radius:12px;
            display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .stat-icon.red    { background:#fde8e8; }
        .stat-icon.green  { background:#e8f5e9; }
        .stat-icon.blue   { background:#e3f2fd; }
        .stat-icon.yellow { background:#fff8e1; }
        .stat-icon.orange { background:#fff3e0; }
        .stat-val   { font-size:28px; font-weight:800; color:#1a1a1a; line-height:1; }
        .stat-label { font-size:12px; color:#888; margin-top:3px; }
        .list-row { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #fafafa; }
        .list-row:last-child { border-bottom:none; }
        .list-avatar { width:38px; height:38px; border-radius:50%; background:#fde8e8; color:#7A0200;
            display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0; }
        .list-info  { flex:1; min-width:0; }
        .list-name  { font-size:13px; font-weight:600; color:#1a1a1a; }
        .list-meta  { font-size:12px; color:#888; margin-top:2px; }
        .room-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:12px; }
        .room-box { border-radius:12px; padding:14px; position:relative; cursor:pointer;
            transition:transform .15s,box-shadow .15s; border:1.5px solid transparent; text-decoration:none; display:block; }
        .room-box:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.1); }
        .room-box.vd { background:#fde8e8; border-color:#ef9a9a; }
        .room-box.vc { background:#e3f2fd; border-color:#90caf9; }
        .room-box.vr { background:#e8f5e9; border-color:#a5d6a7; }
        .room-box.oc { background:#fff8e1; border-color:#ffe082; }
        .room-box.ed { background:#fff3e0; border-color:#ffcc80; }
        .room-num    { font-size:22px; font-weight:800; color:#1a1a1a; }
        .room-type   { font-size:10px; color:#666; margin-top:2px; }
        .room-status { font-size:10px; font-weight:700; margin-top:6px; }
        .room-dot { width:8px; height:8px; border-radius:50%; position:absolute; top:10px; right:10px; }
        .vd .room-dot { background:#e53935; box-shadow:0 0 5px #e53935; }
        .vc .room-dot { background:#1e88e5; box-shadow:0 0 5px #1e88e5; }
        .vr .room-dot { background:#43a047; box-shadow:0 0 5px #43a047; }
        .oc .room-dot { background:#f9a825; box-shadow:0 0 5px #f9a825; }
        .ed .room-dot { background:#fb8c00; box-shadow:0 0 5px #fb8c00; }
        .progress { height:6px; background:#f5e5e5; border-radius:3px; overflow:hidden; }
        .progress-bar { height:100%; background:#7A0200; border-radius:3px; }
        .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4);
            z-index:200; align-items:center; justify-content:center; }
        .modal-bg.open { display:flex; }
        .modal { background:#fff; border-radius:18px; padding:28px; width:90%; max-width:460px;
            box-shadow:0 20px 60px rgba(0,0,0,.2); }
        .modal-title { font-size:17px; font-weight:700; margin-bottom:18px; color:#1a1a1a; }
        .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
        @media(max-width:768px){
            .grid-2,.grid-3,.grid-4{grid-template-columns:1fr;}
        }
    </style>
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

        <nav>@yield('sidebar-nav')</nav>

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
@extends('layouts.topbar')
@section('title', 'Dashboard Supervisor')

@section('content')
<link rel="stylesheet" href="{{ asset('css/supervisor/dashboard.css') }}">
{{-- Welcome Card --}}
<div class="welcome-card">
    <div class="welcome-text">
        <div class="greeting">Selamat Datang, {{ auth()->user()->name }}</div>
        <div class="subtext">Panel Supervisor · {{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="welcome-btn">Logout</button>
    </form>
</div>

{{-- Stats ringkas --}}
@if(isset($pendingTasks) || isset($totalRooms))
<div class="sv-stats">
    @if(isset($pendingTasks))
    <div class="sv-stat-box">
        <div class="sv-stat-val">{{ $pendingTasks }}</div>
        <div class="sv-stat-label">Tugas Menunggu Review</div>
    </div>
    @endif
    @if(isset($inProgressTasks))
    <div class="sv-stat-box">
        <div class="sv-stat-val sv-stat-val--warn">{{ $inProgressTasks }}</div>
        <div class="sv-stat-label">Sedang Dikerjakan</div>
    </div>
    @endif
    @if(isset($todayAttendance) && isset($totalStaff))
    <div class="sv-stat-box">
        <div class="sv-stat-val sv-stat-val--ok">{{ $todayAttendance }}<span class="sv-stat-denom">/{{ $totalStaff }}</span></div>
        <div class="sv-stat-label">Hadir Hari Ini</div>
    </div>
    @endif
</div>
@endif

{{-- Menu Grid --}}
<div class="menu-grid">
    <a href="{{ route('supervisor.attendance.index') }}" class="menu-card">
        <div class="menu-icon">📋</div>
        <div class="menu-title">Absensi</div>
        <div class="menu-desc">Catat dan lihat rekap kehadiran</div>
        <span class="menu-link">Lihat Absensi ›</span>
    </a>
    <a href="{{ route('supervisor.tasks.index') }}" class="menu-card">
        <div class="menu-icon">✅</div>
        <div class="menu-title">Review Tugas</div>
        <div class="menu-desc">Periksa dan approve tugas RA</div>
        <span class="menu-link">Review Sekarang ›</span>
    </a>
    <a href="{{ route('supervisor.history.index') }}" class="menu-card">
        <div class="menu-icon">📊</div>
        <div class="menu-title">Log Aktivitas</div>
        <div class="menu-desc">Riwayat perubahan dan tugas selesai</div>
        <span class="menu-link">Lihat Log ›</span>
    </a>
</div>

@endsection
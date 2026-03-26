@extends('layouts.topbar')
@section('title', 'Dashboard Manager')

@section('content')

{{-- Welcome Card --}}
<div class="welcome-card">
    <div class="welcome-text">
        <div class="greeting">Selamat Datang, {{ auth()->user()->name }}</div>
        <div class="subtext">Panel Manager · {{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="welcome-btn">Logout</button>
    </form>
</div>

{{-- Stats ringkas (opsional — tampil jika controller pass variabel ini) --}}
@if(isset($pendingFromSupervisor) || isset($inProgressTasks))
<div class="mg-stats">
    @if(isset($pendingFromSupervisor))
    <div class="mg-stat-box">
        <div class="mg-stat-val mg-stat-val--warn">{{ $pendingFromSupervisor }}</div>
        <div class="mg-stat-label">Menunggu Review Manager</div>
    </div>
    @endif
    @if(isset($inProgressTasks))
    <div class="mg-stat-box">
        <div class="mg-stat-val">{{ $inProgressTasks }}</div>
        <div class="mg-stat-label">Sedang Dikerjakan</div>
    </div>
    @endif
    @if(isset($todayAttendance) && isset($totalStaff))
    <div class="mg-stat-box">
        <div class="mg-stat-val mg-stat-val--ok">{{ $todayAttendance }}<span class="mg-stat-denom">/{{ $totalStaff }}</span></div>
        <div class="mg-stat-label">Hadir Hari Ini</div>
    </div>
    @endif
</div>
@endif

{{-- Menu Grid --}}
<div class="menu-grid">
    <a href="{{ route('manager.attendance.index') }}" class="menu-card">
        <div class="menu-icon">📋</div>
        <div class="menu-title">Absensi</div>
        <div class="menu-desc">Catat dan lihat rekap kehadiran</div>
        <span class="menu-link">Lihat Absensi ›</span>
    </a>
    <a href="{{ route('manager.inspections.index') }}" class="menu-card">
        <div class="menu-icon">✅</div>
        <div class="menu-title">Review Tugas</div>
        <div class="menu-desc">Approve tugas dari supervisor</div>
        <span class="menu-link">Review Sekarang ›</span>
    </a>
    <a href="{{ route('manager.history.index') }}" class="menu-card">
        <div class="menu-icon">📊</div>
        <div class="menu-title">Log Aktivitas</div>
        <div class="menu-desc">Riwayat perubahan dan tugas selesai</div>
        <span class="menu-link">Lihat Log ›</span>
    </a>
</div>

@endsection
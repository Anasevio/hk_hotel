@extends('layouts.ra')
@section('title','Dashboard')
@section('content')

{{-- Welcome Card --}}
<div class="welcome-card">
    <div class="welcome-text">
        <div class="greeting">Selamat Datang, {{ auth()->user()->name }}</div>
        <div class="subtext">Kelas Aktivitas Sekolahmu Hari Ini · {{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="welcome-btn">Logout</button>
    </form>
</div>

@if($activeTask)
<div class="alert alert-warn" style="margin-bottom:16px">
    ⚡ Kamu punya tugas aktif — Kamar <strong>{{ $activeTask->room->room_number }}</strong>
    ({{ $activeTask->overall_progress }}%)
    <a href="{{ route('ra.tasks.show', $activeTask->id) }}" style="font-weight:700;color:inherit;margin-left:8px">Lanjutkan →</a>
</div>
@endif

{{-- Menu Grid --}}
<div class="menu-grid">
    <a href="{{ route('ra.attendance.index') }}" class="menu-card">
        <div class="menu-icon">📋</div>
        <div class="menu-title">Absensi</div>
        <div class="menu-desc">Catat Kehadiran Secara Online</div>
        <span class="menu-link">Lihat Absensi ›</span>
    </a>
    <a href="{{ route('ra.rooms.index') }}" class="menu-card">
        <div class="menu-icon">🛏️</div>
        <div class="menu-title">Room</div>
        <div class="menu-desc">Lihat dan Kumpulkan Tugas</div>
        <span class="menu-link">Lihat Kamar ›</span>
    </a>
    <a href="{{ route('ra.history.index') }}" class="menu-card">
        <div class="menu-icon">📢</div>
        <div class="menu-title">Riwayat</div>
        <div class="menu-desc">Riwayat Tugas</div>
        <span class="menu-link">Lihat Riwayat ›</span>
    </a>
</div>

@endsection

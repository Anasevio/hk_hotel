@extends('layouts.admin')
@section('title','Dashboard Admin')
@section('page-title','Dashboard')
@section('page-subtitle','Selamat datang, ' . auth()->user()->name . ' 👋')

@section('content')
<div class="grid-4" style="margin-bottom:24px">
    <div class="stat-box">
        <div class="stat-icon red">🏨</div>
        <div><div class="stat-val">{{ $totalRooms }}</div><div class="stat-label">Total Kamar</div></div>
    </div>
    <div class="stat-box">
        <div class="stat-icon red">🧹</div>
        <div><div class="stat-val" style="color:#c62828">{{ $vacantDirty }}</div><div class="stat-label">Vacant Dirty</div></div>
    </div>
    <div class="stat-box">
        <div class="stat-icon orange">⏳</div>
        <div><div class="stat-val" style="color:#e65100">{{ $inProgress }}</div><div class="stat-label">Sedang Dikerjakan</div></div>
    </div>
    <div class="stat-box">
        <div class="stat-icon green">✅</div>
        <div><div class="stat-val" style="color:#2e7d32">{{ $todayAttendance }}<span style="font-size:14px;color:#888">/{{ $totalStaff }}</span></div><div class="stat-label">Absen Hari Ini</div></div>
    </div>
</div>

<div class="grid-2">
<div class="card">
    <div class="card-header">
        <span class="card-title">⚡ Aktivitas Terkini</span>
        <a href="{{ route('admin.history.index') }}" class="btn-sm btn-secondary">Lihat Semua</a>
    </div>
    @forelse($recentLogs as $log)
    <div class="list-row">
        <div class="list-avatar">{{ strtoupper(substr($log->changedByUser->name ?? '?', 0, 2)) }}</div>
        <div class="list-info">
            <div class="list-name">Kamar {{ $log->room->room_number }} — {{ $log->changedByUser->name ?? '-' }}</div>
            <div class="list-meta">
                <span class="badge badge-orange" style="font-size:10px">{{ $log->from_status_label }}</span>
                → <span class="badge badge-green" style="font-size:10px">{{ $log->to_status_label }}</span>
                · {{ $log->created_at->format('H:i') }}
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;color:#999;padding:20px;font-size:13px">Belum ada aktivitas</div>
    @endforelse
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">👥 Absensi Hari Ini</span>
        <a href="{{ route('admin.attendance.index') }}" class="btn-sm btn-secondary">Detail</a>
    </div>
    @foreach($staffList as $staff)
    @php
        $att   = $staff->todayAttendance;
        $badge = $att
            ? ($badgeMap[$att->status] ?? ['label' => ucfirst($att->status), 'class' => 'badge-gray'])
            : ['label' => 'Belum', 'class' => 'badge-red'];
    @endphp
    <div class="list-row">
        <div class="list-avatar">{{ strtoupper(substr($staff->name, 0, 2)) }}</div>
        <div class="list-info">
            <div class="list-name">{{ $staff->name }}</div>
            <div class="list-meta">{{ ucfirst($staff->role) }}
                @if($att) · Masuk {{ $att->check_in }} @endif
            </div>
        </div>
        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
    </div>
    @endforeach
</div>
</div>
@endsection
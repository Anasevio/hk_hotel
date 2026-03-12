@extends('layouts.admin')
@section('title','Dashboard Manager')
@section('page-title','Dashboard')
@section('page-subtitle','Manager — ' . auth()->user()->name)
@section('sidebar-nav')
<a href="{{ route('manager.dashboard') }}" class="active"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('manager.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('manager.inspections.index') }}"><i data-feather="check-circle"></i> Final Inspeksi</a>
<a href="{{ route('manager.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('manager.history.index') }}"><i data-feather="activity"></i> Histori</a>
@endsection
@section('content')
<div class="grid-4" style="margin-bottom:24px">
    <div class="stat-box"><div class="stat-icon green">✅</div><div><div class="stat-val" style="color:#2e7d32">{{ $vacantReady }}</div><div class="stat-label">Vacant Ready</div></div></div>
    <div class="stat-box"><div class="stat-icon yellow">🏨</div><div><div class="stat-val" style="color:#f57f17">{{ $occupied }}</div><div class="stat-label">Occupied</div></div></div>
    <div class="stat-box"><div class="stat-icon red">🧹</div><div><div class="stat-val" style="color:#c62828">{{ $vacantDirty }}</div><div class="stat-label">Perlu Dibersihkan</div></div></div>
    <div class="stat-box"><div class="stat-icon blue">🔍</div><div><div class="stat-val" style="color:#1565c0">{{ $pendingApprove }}</div><div class="stat-label">Menunggu Approve</div></div></div>
</div>
<div class="grid-2">
<div class="card">
    <div class="card-header"><span class="card-title">📊 Ringkasan Kamar</span></div>
    @php $colors=['vacant_dirty'=>'#e53935','vacant_clean'=>'#1e88e5','vacant_ready'=>'#43a047','occupied'=>'#f9a825','expected_departure'=>'#fb8c00'];
    $labels=['vacant_dirty'=>'Vacant Dirty','vacant_clean'=>'Vacant Clean','vacant_ready'=>'Vacant Ready','occupied'=>'Occupied','expected_departure'=>'Exp. Departure'];
    $total = $roomSummary->sum('total') ?: 1; @endphp
    @foreach($roomSummary as $item)
    <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
            <span>{{ $labels[$item->status] ?? $item->status }}</span>
            <span style="color:#888">{{ $item->total }} kamar</span>
        </div>
        <div class="progress"><div class="progress-bar" style="width:{{ round($item->total/$total*100) }}%;background:{{ $colors[$item->status] ?? '#7A0200' }}"></div></div>
    </div>
    @endforeach
</div>
<div class="card">
    <div class="card-header"><span class="card-title">🔍 Menunggu Approve</span>
        <a href="{{ route('manager.inspections.index') }}" class="btn-sm btn-secondary">Lihat Semua</a></div>
    @forelse($pendingTasks as $task)
    <div class="list-row">
        <div class="list-avatar">{{ strtoupper(substr($task->assignedUser->name ?? '?', 0, 2)) }}</div>
        <div class="list-info">
            <div class="list-name">Kamar {{ $task->room->room_number }}</div>
            <div class="list-meta">{{ $task->assignedUser->name }} · Supervisor ✓</div>
        </div>
        <a href="{{ route('manager.inspections.show', $task->id) }}" class="btn-sm btn-primary">Approve</a>
    </div>
    @empty
    <div style="text-align:center;color:#999;padding:24px;font-size:13px">Tidak ada yang menunggu ✓</div>
    @endforelse
</div>
</div>
@endsection

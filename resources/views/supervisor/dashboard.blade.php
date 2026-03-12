@extends('layouts.admin')
@section('title','Dashboard Supervisor')
@section('page-title','Dashboard')
@section('page-subtitle','Supervisor — ' . auth()->user()->name)
@section('sidebar-nav')
<a href="{{ route('supervisor.dashboard') }}" class="active"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('supervisor.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('supervisor.tasks.index') }}"><i data-feather="clipboard"></i> Kelola Tugas</a>
<a href="{{ route('supervisor.special-cases.index') }}"><i data-feather="alert-circle"></i> Special Case</a>
<a href="{{ route('supervisor.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('supervisor.history.index') }}"><i data-feather="activity"></i> Histori</a>
@endsection

@section('content')
<div class="grid-4" style="margin-bottom:24px">
    <div class="stat-box"><div class="stat-icon red">🧹</div>
        <div><div class="stat-val" style="color:#c62828">{{ $vacantDirty }}</div><div class="stat-label">Vacant Dirty</div></div></div>
    <div class="stat-box"><div class="stat-icon yellow">🔍</div>
        <div><div class="stat-val" style="color:#f57f17">{{ $pendingInspection }}</div><div class="stat-label">Perlu Inspeksi</div></div></div>
    <div class="stat-box"><div class="stat-icon orange">⏳</div>
        <div><div class="stat-val" style="color:#e65100">{{ $inProgress }}</div><div class="stat-label">Sedang Dikerjakan</div></div></div>
    <div class="stat-box"><div class="stat-icon green">✅</div>
        <div><div class="stat-val" style="color:#2e7d32">{{ $vacantReady }}</div><div class="stat-label">Vacant Ready</div></div></div>
</div>

@if($pendingTasks->count())
<div class="card" style="border-left:4px solid #f9a825;margin-bottom:20px">
    <div class="card-header">
        <span class="card-title">⚡ Perlu Inspeksi Segera</span>
        <span class="badge badge-yellow">{{ $pendingTasks->count() }}</span>
    </div>
    @foreach($pendingTasks as $task)
    <div class="list-row">
        <div class="list-avatar">{{ strtoupper(substr($task->assignedUser->name ?? '?', 0, 2)) }}</div>
        <div class="list-info">
            <div class="list-name">Kamar {{ $task->room->room_number }} — {{ $task->assignedUser->name }}</div>
            <div class="list-meta">Selesai · {{ $task->submitted_at?->format('H:i') ?? '-' }}</div>
        </div>
        <a href="{{ route('supervisor.tasks.show', $task->id) }}" class="btn-sm btn-primary">Inspeksi →</a>
    </div>
    @endforeach
</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">👷 Status Room Attendant</span>
        <a href="{{ route('supervisor.tasks.index') }}" class="btn-sm btn-primary">+ Assign Tugas</a>
    </div>
    @foreach($raList as $ra)
    @php $t = $ra->tasks->first(); @endphp
    <div class="list-row">
        <div class="list-avatar">{{ strtoupper(substr($ra->name, 0, 2)) }}</div>
        <div class="list-info">
            <div class="list-name">{{ $ra->name }}</div>
            <div class="list-meta">Shift {{ ucfirst($ra->shift) }}
                @if($t) · Kamar {{ $t->room->room_number }} · {{ $t->overall_progress }}% @endif
            </div>
        </div>
        <span class="badge {{ $t ? 'badge-orange' : 'badge-green' }}">{{ $t ? 'Busy' : 'Available' }}</span>
    </div>
    @endforeach
</div>
@endsection

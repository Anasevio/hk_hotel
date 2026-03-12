@extends('layouts.admin')
@section('title','Final Inspeksi')
@section('page-title','Final Inspeksi')
@section('sidebar-nav')
<a href="{{ route('manager.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('manager.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('manager.inspections.index') }}" class="active"><i data-feather="check-circle"></i> Final Inspeksi</a>
<a href="{{ route('manager.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('manager.history.index') }}"><i data-feather="activity"></i> Histori</a>
@endsection
@section('content')
<div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">🔍 Menunggu Final Approve ({{ $pendingTasks->count() }})</span></div>
    @forelse($pendingTasks as $task)
    <div class="list-row">
        <div class="list-avatar">{{ strtoupper(substr($task->assignedUser->name ?? '?', 0, 2)) }}</div>
        <div class="list-info">
            <div class="list-name">Kamar {{ $task->room->room_number }} — {{ ucfirst($task->room->room_type) }}</div>
            <div class="list-meta">RA: {{ $task->assignedUser->name }} · Supervisor: {{ $task->assignedByUser->name }}
                · Approve: {{ $task->supervisor_approved_at?->format('H:i') }}</div>
        </div>
        <a href="{{ route('manager.inspections.show', $task->id) }}" class="btn-sm btn-primary">Periksa →</a>
    </div>
    @empty
    <div style="text-align:center;color:#999;padding:24px">Tidak ada yang menunggu</div>
    @endforelse
</div>
<div class="card">
    <div class="card-header"><span class="card-title">✅ Selesai Hari Ini</span></div>
    @forelse($completedTasks as $task)
    <div class="list-row">
        <div class="list-avatar">{{ strtoupper(substr($task->assignedUser->name ?? '?', 0, 2)) }}</div>
        <div class="list-info">
            <div class="list-name">Kamar {{ $task->room->room_number }}</div>
            <div class="list-meta">{{ $task->assignedUser->name }} · Selesai {{ $task->completed_at?->format('H:i') }}</div>
        </div>
        <span class="badge badge-green">Selesai</span>
    </div>
    @empty
    <div style="text-align:center;color:#999;padding:24px">Belum ada</div>
    @endforelse
</div>
@endsection

@extends('layouts.admin')
@section('title','Kelola Tugas')
@section('page-title','Kelola Tugas')
@section('sidebar-nav')
<a href="{{ route('supervisor.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('supervisor.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('supervisor.tasks.index') }}" class="active"><i data-feather="clipboard"></i> Kelola Tugas</a>
<a href="{{ route('supervisor.special-cases.index') }}"><i data-feather="alert-circle"></i> Special Case</a>
<a href="{{ route('supervisor.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('supervisor.history.index') }}"><i data-feather="activity"></i> Histori</a>
@endsection

@section('content')
{{-- ASSIGN TUGAS BARU --}}
@if($unassignedRooms->count())
<div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">➕ Assign Tugas Baru</span></div>
    <form method="POST" action="{{ route('supervisor.tasks.store') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        @csrf
        <div class="form-group" style="margin:0;flex:1;min-width:160px">
            <label class="form-label">Kamar</label>
            <select name="room_id" class="form-control" required>
                <option value="">Pilih kamar...</option>
                @foreach($unassignedRooms as $room)
                <option value="{{ $room->id }}">{{ $room->room_number }} — {{ $room->status_label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:160px">
            <label class="form-label">Room Attendant</label>
            <select name="assigned_to" class="form-control" required>
                <option value="">Pilih RA...</option>
                @foreach($allRA as $ra)
                <option value="{{ $ra->id }}">{{ $ra->name }} (Shift {{ ucfirst($ra->shift) }})</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-sm btn-primary" style="height:38px">Assign</button>
    </form>
</div>
@endif

{{-- DAFTAR TUGAS AKTIF --}}
<div class="card">
    <div class="card-header"><span class="card-title">Tugas Berjalan ({{ $activeTasks->count() }})</span></div>
    <div style="overflow-x:auto">
    <table class="tbl">
        <thead><tr><th>Kamar</th><th>Room Attendant</th><th>Status</th><th>Progress</th><th>Mulai</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($activeTasks as $task)
        <tr>
            <td><strong>{{ $task->room->room_number }}</strong></td>
            <td>{{ $task->assignedUser->name ?? '-' }}</td>
            <td><span class="badge
                {{ $task->status === 'pending_supervisor' ? 'badge-yellow' : ($task->status === 'returned_to_ra' ? 'badge-red' : 'badge-blue') }}"
                style="font-size:10px">{{ $task->status_label }}</span></td>
            <td style="min-width:100px">
                <div class="progress"><div class="progress-bar" style="width:{{ $task->overall_progress }}%"></div></div>
                <div style="font-size:11px;color:#888;margin-top:3px">{{ $task->overall_progress }}%</div>
            </td>
            <td>{{ $task->started_at?->format('H:i') ?? '-' }}</td>
            <td>
                @if($task->status === 'pending_supervisor')
                <a href="{{ route('supervisor.tasks.show', $task->id) }}" class="btn-sm btn-primary">Inspeksi</a>
                @else
                <a href="{{ route('supervisor.tasks.show', $task->id) }}" class="btn-sm btn-secondary">Detail</a>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#999;padding:24px">Tidak ada tugas aktif</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection

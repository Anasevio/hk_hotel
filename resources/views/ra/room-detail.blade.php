@extends('layouts.ra')
@section('title','Detail Kamar')
@section('page-title','Kamar ' . $room->room_number)
@section('content')
<div class="card">
    <div class="card-header"><span class="card-title">Info Kamar</span></div>
    <div style="font-size:13px;line-height:2.2">
        <div>🏨 Nomor: <strong>{{ $room->room_number }}</strong></div>
        <div>🛏️ Tipe: {{ ucfirst($room->room_type) }}</div>
        <div>📍 Lantai: {{ $room->floor }}</div>
        <div>📋 Status: <span class="badge badge-red">{{ $room->status_label }}</span></div>
        @if($room->notes)<div>📝 Catatan: {{ $room->notes }}</div>@endif
    </div>
</div>
@if($task)
<div class="card" style="border-left:4px solid #7A0200">
    <div class="card-header">
        <span class="card-title">Tugas Aktif</span>
        <span class="badge {{ $task->status === 'pending' ? 'badge-gray' : 'badge-orange' }}">{{ $task->status_label }}</span>
    </div>
    <div style="font-size:13px;color:#666;margin-bottom:14px">Progress: {{ $task->overall_progress }}%</div>
    <div class="progress" style="margin-bottom:16px"><div class="progress-bar" style="width:{{ $task->overall_progress }}%"></div></div>
    @if($task->supervisor_note)
    <div class="alert alert-warn" style="margin-bottom:12px">📋 {{ $task->supervisor_note }}</div>
    @endif
    <a href="{{ route('ra.tasks.show', $task->id) }}" class="btn-sm btn-primary">Buka Tugas →</a>
</div>
@else
<div class="alert" style="background:#f0f4ff;border:1px solid #c5d5f0;color:#1a3a80">Belum ada tugas aktif untuk kamar ini.</div>
@endif
@endsection

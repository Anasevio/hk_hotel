@extends('layouts.admin')
@section('title','Detail Tugas')
@section('page-title','Inspeksi Tugas')
@section('sidebar-nav')
<a href="{{ route('supervisor.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('supervisor.tasks.index') }}" class="active"><i data-feather="clipboard"></i> Kelola Tugas</a>
@endsection

@section('content')
<div class="grid-2">
<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-header"><span class="card-title">Info Tugas</span></div>
        <div style="font-size:13px;line-height:2">
            <div>🏨 <strong>Kamar {{ $task->room->room_number }}</strong> — {{ $task->room->room_type }}</div>
            <div>👷 RA: <strong>{{ $task->assignedUser->name }}</strong></div>
            <div>📋 Status: <span class="badge badge-yellow">{{ $task->status_label }}</span></div>
            <div>⏱ Mulai: {{ $task->started_at?->format('H:i') ?? '-' }}</div>
            <div>✅ Submit: {{ $task->submitted_at?->format('H:i') ?? '-' }}</div>
            <div>⏳ Batas: {{ $task->time_limit }} menit
                @if($task->isOvertime()) <span class="badge badge-red">Overtime!</span> @endif
            </div>
        </div>
    </div>

    @if($task->status === 'pending_supervisor')
    <div class="card">
        <div class="card-header"><span class="card-title">Keputusan Inspeksi</span></div>
        <form method="POST" action="{{ route('supervisor.tasks.approve', $task->id) }}" style="margin-bottom:12px">
            @csrf
            <button type="submit" class="btn-sm btn-success" style="width:100%;padding:12px;font-size:14px">
                ✅ Approve — Teruskan ke Manager
            </button>
        </form>
        <form method="POST" action="{{ route('supervisor.tasks.return', $task->id) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Catatan untuk RA (wajib)</label>
                <textarea name="note" class="form-control" rows="3" required placeholder="Apa yang perlu diperbaiki..."></textarea>
            </div>
            <button type="submit" class="btn-sm btn-danger" style="width:100%;padding:12px;font-size:14px">
                ↩ Kembalikan ke RA
            </button>
        </form>
    </div>
    @endif
</div>

<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-header"><span class="card-title">✅ Checklist Persiapan ({{ $task->checklist1_progress }}%)</span></div>
        <div class="progress" style="margin-bottom:12px"><div class="progress-bar" style="width:{{ $task->checklist1_progress }}%"></div></div>
        @foreach($task->preparationChecklists as $item)
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #fafafa">
            <span style="font-size:16px">{{ $item->is_checked ? '✅' : '⬜' }}</span>
            <span style="font-size:13px;{{ $item->is_checked ? 'text-decoration:line-through;color:#999' : '' }}">{{ $item->item_name }}</span>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">🧹 Checklist Pembersihan ({{ $task->checklist2_progress }}%)</span></div>
        <div class="progress" style="margin-bottom:12px"><div class="progress-bar" style="width:{{ $task->checklist2_progress }}%"></div></div>
        @foreach($task->cleaningChecklists as $item)
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #fafafa">
            <span style="font-size:16px">{{ $item->is_checked ? '✅' : '⬜' }}</span>
            <span style="font-size:13px;{{ $item->is_checked ? 'text-decoration:line-through;color:#999' : '' }}">{{ $item->item_name }}</span>
            @if($item->estimated_minutes)
            <span style="font-size:11px;color:#aaa;margin-left:auto">{{ $item->estimated_minutes }}m</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
</div>
@endsection

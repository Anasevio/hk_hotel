@extends('layouts.admin')
@section('title','Detail Inspeksi')
@section('page-title','Detail Inspeksi')
@section('sidebar-nav')
<a href="{{ route('manager.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('manager.inspections.index') }}" class="active"><i data-feather="check-circle"></i> Final Inspeksi</a>
@endsection
@section('content')
<div class="grid-2">
<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-header"><span class="card-title">Info Tugas</span></div>
        <div style="font-size:13px;line-height:2.2">
            <div>🏨 Kamar <strong>{{ $task->room->room_number }}</strong> — {{ ucfirst($task->room->room_type) }}</div>
            <div>👷 RA: <strong>{{ $task->assignedUser->name }}</strong></div>
            <div>👔 Supervisor: <strong>{{ $task->assignedByUser->name }}</strong></div>
            <div>⏱ Mulai: {{ $task->started_at?->format('H:i') }}</div>
            <div>📤 Submit: {{ $task->submitted_at?->format('H:i') }}</div>
            <div>🔍 Approve SPV: {{ $task->supervisor_approved_at?->format('H:i') }}</div>
            <div>⏳ Durasi: {{ $task->duration_minutes ? $task->duration_minutes.' menit' : '-' }}
                @if($task->isOvertime()) <span class="badge badge-red">Overtime</span> @endif
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">Keputusan Final</span></div>
        <form method="POST" action="{{ route('manager.inspections.approve', $task->id) }}" style="margin-bottom:12px">
            @csrf
            <button type="submit" class="btn-sm btn-success" style="width:100%;padding:12px;font-size:14px">
                ✅ Final Approve — Kamar jadi Vacant Ready
            </button>
        </form>
        <form method="POST" action="{{ route('manager.inspections.return', $task->id) }}">
            @csrf
            <div class="form-group"><label class="form-label">Catatan untuk Supervisor</label>
                <textarea name="note" class="form-control" rows="3" required></textarea></div>
            <button type="submit" class="btn-sm btn-danger" style="width:100%;padding:12px;font-size:14px">
                ↩ Kembalikan ke Supervisor
            </button>
        </form>
    </div>
</div>
<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-header"><span class="card-title">Checklist Persiapan ({{ $task->checklist1_progress }}%)</span></div>
        <div class="progress" style="margin-bottom:12px"><div class="progress-bar" style="width:{{ $task->checklist1_progress }}%"></div></div>
        @foreach($task->preparationChecklists as $item)
        <div style="display:flex;gap:10px;padding:7px 0;border-bottom:1px solid #fafafa">
            <span>{{ $item->is_checked ? '✅' : '⬜' }}</span>
            <span style="font-size:13px;{{ $item->is_checked ? 'color:#999;text-decoration:line-through' : '' }}">{{ $item->item_name }}</span>
        </div>
        @endforeach
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">Checklist Pembersihan ({{ $task->checklist2_progress }}%)</span></div>
        <div class="progress" style="margin-bottom:12px"><div class="progress-bar" style="width:{{ $task->checklist2_progress }}%"></div></div>
        @foreach($task->cleaningChecklists as $item)
        <div style="display:flex;gap:10px;padding:7px 0;border-bottom:1px solid #fafafa">
            <span>{{ $item->is_checked ? '✅' : '⬜' }}</span>
            <span style="font-size:13px;{{ $item->is_checked ? 'color:#999;text-decoration:line-through' : '' }}">{{ $item->item_name }}</span>
        </div>
        @endforeach
    </div>
</div>
</div>
@endsection

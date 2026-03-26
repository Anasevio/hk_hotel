@extends('layouts.topbar')
@section('title', 'Inspeksi Kamar ' . $task->room->room_number)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/supervisor/task.css') }}">
<link rel="stylesheet" href="{{ asset('css/supervisor/task_detail.css') }}">
@endpush

@section('content')

<a href="{{ route('supervisor.tasks.index') }}" class="back-link">← Kembali ke Daftar Tugas</a>

{{-- Header kamar --}}
<div class="sv-detail-header">
    <div class="sv-detail-badge">{{ $task->room->room_number }}</div>
    <div class="sv-detail-info">
        <div class="sv-detail-title">Kamar {{ $task->room->room_number }}</div>
        <div class="sv-detail-meta">
            <span class="sv-room-type-pill">{{ ucfirst($task->room->room_type) }}</span>
            <span class="sv-ra-name">👤 {{ $task->assignedUser->name ?? '-' }}</span>
            @if($task->submitted_at)
            <span class="sv-submitted-at">· Disubmit {{ $task->submitted_at->format('H:i') }}</span>
            @endif
        </div>
    </div>
    @if($task->duration_minutes)
    <div class="sv-detail-duration">
        <div class="sv-duration-val">{{ $task->duration_minutes }}<span class="sv-duration-unit">mnt</span></div>
        <div class="sv-duration-label">Durasi kerja</div>
        @if($task->time_limit && $task->duration_minutes > $task->time_limit)
        <div class="sv-overtime-badge">+{{ $task->duration_minutes - $task->time_limit }} OT</div>
        @endif
    </div>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">⚠️ {{ session('error') }}</div>
@endif

{{-- Catatan dari RA --}}
@if($task->ra_notes)
<div class="sv-ra-notes">
    <div class="sv-notes-label">📝 Catatan dari RA</div>
    <div class="sv-notes-text">{{ $task->ra_notes }}</div>
</div>
@endif

{{-- Progress keseluruhan --}}
<div class="sv-overall-progress">
    <div class="sv-overall-top">
        <span class="sv-overall-label">Progress Keseluruhan</span>
        <span class="sv-overall-pct">{{ $task->overall_progress }}%</span>
    </div>
    <div class="sv-progress-wrap">
        <div class="sv-progress-bar" style="width:{{ $task->overall_progress }}%"></div>
    </div>
    <div class="sv-progress-detail">
        Persiapan {{ $task->checklist1_progress }}%
        · Kebersihan {{ $task->checklist2_progress }}%
    </div>
</div>

{{-- ══════════════════════════════════════════
     CHECKLIST PERSIAPAN
     ══════════════════════════════════════════ --}}
<div class="sv-cl-card">
    <div class="sv-cl-header">
        <span class="sv-cl-title">Checklist Persiapan</span>
        <span class="sv-cl-count">
            {{ $task->preparationChecklists->where('is_checked', true)->count() }}
            / {{ $task->preparationChecklists->count() }}
        </span>
    </div>
    <div class="sv-cl-list">
        @foreach($task->preparationChecklists as $item)
        <div class="sv-cl-item {{ $item->is_checked ? 'sv-cl-item--done' : 'sv-cl-item--miss' }}">
            <div class="sv-cl-check">{{ $item->is_checked ? '✓' : '✗' }}</div>
            <span class="sv-cl-label">{{ $item->item_name }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════
     CHECKLIST KEBERSIHAN
     ══════════════════════════════════════════ --}}
<div class="sv-cl-card">
    <div class="sv-cl-header">
        <span class="sv-cl-title">Checklist Kebersihan</span>
        <span class="sv-cl-count">
            {{ $task->cleaningChecklists->where('is_checked', true)->count() }}
            / {{ $task->cleaningChecklists->count() }}
        </span>
    </div>
    <div class="sv-cl-list">
        @foreach($task->cleaningChecklists as $item)
        <div class="sv-cl-item {{ $item->is_checked ? 'sv-cl-item--done' : 'sv-cl-item--miss' }}">
            <div class="sv-cl-check">{{ $item->is_checked ? '✓' : '✗' }}</div>
            <span class="sv-cl-label">{{ $item->item_name }}</span>
            @if($item->estimated_minutes)
            <span class="sv-cl-est">{{ $item->estimated_minutes }}mnt</span>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════
     AKSI: APPROVE / KEMBALIKAN
     ══════════════════════════════════════════ --}}
@if($task->status === 'pending_supervisor')
<div class="sv-action-card">

    {{-- Approve --}}
    <form method="POST" action="{{ route('supervisor.tasks.approve', $task->id) }}" class="sv-action-form">
        @csrf
        <div class="sv-action-label">Catatan Supervisor (opsional)</div>
        <textarea name="note" class="sv-action-textarea"
                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        <button type="submit" class="sv-btn-approve"
                onclick="return confirm('Approve tugas kamar {{ $task->room->room_number }} dan teruskan ke Manager?')">
            ✅ Approve & Teruskan ke Manager
        </button>
    </form>

    <div class="sv-action-divider">atau</div>

    {{-- Return to RA --}}
    <form method="POST" action="{{ route('supervisor.tasks.return', $task->id) }}" class="sv-action-form">
        @csrf
        <div class="sv-action-label sv-action-label--danger">Alasan Dikembalikan <span>*wajib diisi</span></div>
        <textarea name="note" class="sv-action-textarea sv-action-textarea--danger" required
                  placeholder="Jelaskan apa yang perlu diperbaiki RA..."></textarea>
        <button type="submit" class="sv-btn-return"
                onclick="return confirm('Kembalikan tugas ke RA? Kamar akan kembali ke Vacant Dirty.')">
            ↩ Kembalikan ke RA
        </button>
    </form>

</div>
@else
<div class="sv-status-info">
    <span class="sv-status-pill-lg">{{ $task->status_label }}</span>
    @if($task->supervisor_note)
    <div class="sv-notes-text" style="margin-top:10px">{{ $task->supervisor_note }}</div>
    @endif
</div>
@endif

@endsection
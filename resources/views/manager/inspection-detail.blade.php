@extends('layouts.topbar')

@section('title', 'Review Manager Kamar ' . $task->room->room_number)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/supervisor/task.css') }}">
<link rel="stylesheet" href="{{ asset('css/supervisor/task_detail.css') }}">
@endpush

@section('content')

<a href="{{ route('manager.inspections.index') }}" class="back-link">
    ← Kembali ke Review
</a>

{{-- HEADER --}}
<div class="sv-detail-header">
    <div class="sv-detail-badge">{{ $task->room->room_number }}</div>

    <div class="sv-detail-info">
        <div class="sv-detail-title">Kamar {{ $task->room->room_number }}</div>

        <div class="sv-detail-meta">
            <span class="sv-room-type-pill">{{ ucfirst($task->room->room_type) }}</span>
            <span class="sv-ra-name">👤 {{ $task->assignedUser->name ?? '-' }}</span>
            <span class="sv-ra-name">SPV: {{ $task->assignedByUser->name ?? '-' }}</span>
        </div>
    </div>
</div>

{{-- PROGRESS --}}
<div class="sv-overall-progress">
    <div class="sv-overall-top">
        <span class="sv-overall-label">Progress</span>
        <span class="sv-overall-pct">{{ $task->overall_progress }}%</span>
    </div>

    <div class="sv-progress-wrap">
        <div class="sv-progress-bar" style="width:{{ $task->overall_progress }}%"></div>
    </div>
</div>

{{-- CHECKLIST PERSIAPAN --}}
<div class="sv-cl-card">
    <div class="sv-cl-header">
        <span class="sv-cl-title">Checklist Persiapan</span>
    </div>

    @foreach($task->preparationChecklists as $item)
    <div class="sv-cl-item {{ $item->is_checked ? 'sv-cl-item--done' : 'sv-cl-item--miss' }}">
        <div class="sv-cl-check">{{ $item->is_checked ? '✓' : '✗' }}</div>
        <span class="sv-cl-label">{{ $item->item_name }}</span>
    </div>
    @endforeach
</div>

{{-- CHECKLIST CLEANING --}}
<div class="sv-cl-card">
    <div class="sv-cl-header">
        <span class="sv-cl-title">Checklist Kebersihan</span>
    </div>

    @foreach($task->cleaningChecklists as $item)
    <div class="sv-cl-item {{ $item->is_checked ? 'sv-cl-item--done' : 'sv-cl-item--miss' }}">
        <div class="sv-cl-check">{{ $item->is_checked ? '✓' : '✗' }}</div>
        <span class="sv-cl-label">{{ $item->item_name }}</span>
    </div>
    @endforeach
</div>

{{-- ACTION --}}
<div class="sv-action-card">

    {{-- APPROVE --}}
    <form method="POST" action="{{ route('manager.inspections.approve', $task->id) }}">
        @csrf

        <textarea name="note" class="sv-action-textarea"
                  placeholder="Catatan Manager (opsional)"></textarea>

        <button type="submit" class="sv-btn-approve">
            ✅ Approve Final
        </button>
    </form>

    <div class="sv-action-divider">atau</div>

    {{-- RETURN --}}
    <form method="POST" action="{{ route('manager.inspections.return', $task->id) }}">
        @csrf

        <textarea name="note" class="sv-action-textarea sv-action-textarea--danger"
                  required placeholder="Alasan dikembalikan ke Supervisor..."></textarea>

        <button type="submit" class="sv-btn-return">
            ↩ Kembalikan ke Supervisor
        </button>
    </form>

</div>

@endsection
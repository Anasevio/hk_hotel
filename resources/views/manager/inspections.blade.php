@extends('layouts.topbar')

@section('title', 'Review Tugas Manager')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/supervisor/task.css') }}">
@endpush

@section('content')

<a href="{{ route('manager.dashboard') }}" class="back-link">← Kembali</a>

{{-- HEADER --}}
<div class="sv-task-header">
    <div>
        <div class="sv-task-title">Review Tugas Manager</div>
        <div class="sv-task-sub">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>

    <div class="sv-approved-badge">
        {{ $pendingTasks->count() }} Pending
    </div>
</div>

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success">✅ {{ session('success') }}</div>
@endif

{{-- SECTION --}}
<div class="sv-section-label">
    <span>Menunggu Approval</span>
    <span class="sv-section-count sv-section-count--urgent">
        {{ $pendingTasks->count() }}
    </span>
</div>

@forelse($pendingTasks as $task)
<div class="sv-task-card sv-task-card--pending">

    <div class="sv-task-card-top">

        <div class="sv-room-badge">
            {{ $task->room->room_number }}
        </div>

        <div class="sv-task-info">
            <div class="sv-task-room">
                Kamar {{ $task->room->room_number }}
                <span class="sv-room-type">· {{ ucfirst($task->room->room_type) }}</span>
            </div>

            <div class="sv-task-meta">
                👤 {{ $task->assignedUser->name ?? '-' }}
                · SPV: {{ $task->assignedByUser->name ?? '-' }}
                · {{ $task->supervisor_approved_at?->diffForHumans() }}
            </div>
        </div>

        <a href="{{ route('manager.inspections.show', $task->id) }}" class="btn-inspect">
            Review →
        </a>

    </div>

    {{-- PROGRESS --}}
    <div class="sv-progress-row">
        <div class="sv-progress-wrap">
            <div class="sv-progress-bar"
                 style="width:{{ $task->overall_progress }}%">
            </div>
        </div>
        <span class="sv-progress-pct">
            {{ $task->overall_progress }}%
        </span>
    </div>

</div>
@empty
<div class="sv-empty">
    <div class="sv-empty-icon">✅</div>
    <div class="sv-empty-title">Tidak ada tugas menunggu approval</div>
    <div class="sv-empty-desc">Semua tugas sudah selesai.</div>
</div>
@endforelse

@endsection
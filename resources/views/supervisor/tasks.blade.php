@extends('layouts.topbar')
@section('title', 'Review Tugas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/supervisor/task.css') }}">
@endpush

@section('content')

<a href="{{ route('supervisor.dashboard') }}" class="back-link">← Kembali</a>

{{-- Header --}}
<div class="sv-task-header">
    <div>
        <div class="sv-task-title">Review Tugas</div>
        <div class="sv-task-sub">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
    @if($approvedToday > 0)
    <div class="sv-approved-badge">✓ {{ $approvedToday }} diapprove hari ini</div>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">⚠️ {{ session('error') }}</div>
@endif

{{-- ══════════════════════════════════════════
     SECTION 1: Menunggu Inspeksi Supervisor
     ══════════════════════════════════════════ --}}
<div class="sv-section-label">
    <span>Menunggu Inspeksi</span>
    <span class="sv-section-count sv-section-count--urgent">{{ $pendingTasks->count() }}</span>
</div>

@forelse($pendingTasks as $task)
<div class="sv-task-card 
    {{ $task->status === 'returned_to_supervisor' 
        ? 'sv-task-card--returned' 
        : 'sv-task-card--pending' }}">
        
    <div class="sv-task-card-top">

    @if($task->status === 'returned_to_supervisor' && $task->manager_note)
    <div style="margin-top:8px; padding:10px; background:#fde8e8; border-radius:8px; font-size:13px; color:#c62828;">
        <strong>Catatan Manager:</strong><br>
        {{ $task->manager_note }}
    </div>
@endif

        <div class="sv-room-badge">{{ $task->room->room_number }}</div>
        <div class="sv-task-info">
           <div class="sv-task-room">
    Kamar {{ $task->room->room_number }}

    @if($task->status === 'returned_to_supervisor')
        <span style="color:#e53935; font-weight:600; margin-left:6px;">
            ↩ Revisi dari Manager
        </span>
    @endif

    <span class="sv-room-type">· {{ ucfirst($task->room->room_type) }}</span>
</div>
            <div class="sv-task-meta">
                👤 {{ $task->assignedUser->name ?? '-' }}
                @if($task->status === 'returned_to_supervisor')
                    · Dikembalikan Manager
                    @if($task->updated_at)
                        · {{ $task->updated_at->diffForHumans() }}
                    @endif
                @else
                    · Selesai {{ $task->submitted_at?->format('H:i') ?? '-' }}
                    @if($task->submitted_at)
                        · {{ $task->submitted_at->diffForHumans() }}
                    @endif
                @endif
            </div>
        </div>
        <a href="{{ route('supervisor.tasks.show', $task->id) }}" class="btn-inspect">
            Inspeksi →
        </a>
    </div>

    {{-- Progress bar --}}
    <div class="sv-progress-row">
        <div class="sv-progress-wrap">
            <div class="sv-progress-bar" style="width:{{ $task->overall_progress }}%"></div>
        </div>
        <span class="sv-progress-pct">{{ $task->overall_progress }}%</span>
    </div>

    {{-- Durasi pengerjaan --}}
    @if($task->duration_minutes)
    <div class="sv-task-duration">
        ⏱ Dikerjakan selama
        <strong>{{ $task->duration_minutes }} menit</strong>
        @if($task->time_limit)
            dari batas {{ $task->time_limit }} menit
            @if($task->duration_minutes > $task->time_limit)
                <span class="sv-overtime">+{{ $task->duration_minutes - $task->time_limit }} menit overtime</span>
            @endif
        @endif
    </div>
    @endif
</div>
@empty
<div class="sv-empty">
    <div class="sv-empty-icon">✅</div>
    <div class="sv-empty-title">Tidak ada tugas menunggu inspeksi</div>
    <div class="sv-empty-desc">Semua tugas sudah ditangani.</div>
</div>
@endforelse

{{-- ══════════════════════════════════════════
     SECTION 2: Tugas Aktif (Monitoring)
     ══════════════════════════════════════════ --}}
@if($activeTasks->count())
<div class="sv-section-label" style="margin-top:28px">
    <span>Sedang Dikerjakan</span>
    <span class="sv-section-count">{{ $activeTasks->count() }}</span>
</div>

<div class="sv-active-grid">
    @foreach($activeTasks as $task)
    <div class="sv-active-card">
        <div class="sv-active-top">
            <div class="sv-active-room">{{ $task->room->room_number }}</div>
            <span class="sv-status-pill
                @if($task->status === 'in_progress') sv-status--progress
                @elseif($task->status === 'returned_to_ra') sv-status--returned
                @else sv-status--pending
                @endif">
                @if($task->status === 'in_progress')        ▶ Dikerjakan
                @elseif($task->status === 'returned_to_ra') ⚠ Dikembalikan
                @else                                       ⏸ Menunggu
                @endif
            </span>
        </div>
        <div class="sv-active-ra">{{ $task->assignedUser->name ?? '-' }}</div>
        <div class="sv-progress-wrap" style="margin-top:8px">
            <div class="sv-progress-bar sv-progress-bar--muted"
                 style="width:{{ $task->overall_progress }}%"></div>
        </div>
        <div class="sv-active-pct">{{ $task->overall_progress }}%</div>
    </div>
    @endforeach
</div>
@endif

@endsection
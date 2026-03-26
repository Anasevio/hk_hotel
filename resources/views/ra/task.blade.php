@extends('layouts.topbar')
@section('title', 'Kamar Saya')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ra/rooms.css') }}">
@endpush

@section('content')

@php
$clsMap = ['vacant_dirty'=>'vd','vacant_clean'=>'vc','vacant_ready'=>'vr','occupied'=>'oc','expected_departure'=>'ed'];
$lblMap = ['vacant_dirty'=>'Vacant Dirty','vacant_clean'=>'Vacant Clean','vacant_ready'=>'Vacant Ready','occupied'=>'Occupied','expected_departure'=>'Exp. Departure'];
@endphp

@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">⚠️ {{ session('error') }}</div>
@endif

{{-- Header --}}
<div class="page-header">
    <div>
        <div class="page-title">Kamar Saya</div>
        <div class="page-subtitle">
            {{ $rooms->count() }} kamar ditugaskan
            · Klik <strong>Vacant Dirty</strong> untuk mulai membersihkan
        </div>
    </div>
    <div class="shift-badge">🕐 Shift {{ ucfirst(auth()->user()->shift) }}</div>
</div>

{{-- Legend --}}
<div class="legend-bar">
    <div class="legend-item"><div class="legend-dot" style="background:var(--vd)"></div>Vacant Dirty</div>
    <div class="legend-item"><div class="legend-dot" style="background:var(--vc)"></div>Vacant Clean</div>
    <div class="legend-item"><div class="legend-dot" style="background:var(--vr)"></div>Vacant Ready</div>
    <div class="legend-item"><div class="legend-dot" style="background:var(--oc)"></div>Occupied</div>
    <div class="legend-item"><div class="legend-dot" style="background:var(--ed)"></div>Exp. Departure</div>
</div>

{{-- Grid --}}
@if($rooms->count())
<div class="room-grid">
    @foreach($rooms as $room)
        @php
            $cls       = $clsMap[$room->status] ?? 'vd';
            $lbl       = $lblMap[$room->status] ?? $room->status;
            $task      = $room->activeTask;
            $state     = $task?->status;
            $clickable = $task && in_array($state, ['pending', 'in_progress', 'returned_to_ra']);
        @endphp

        @if($clickable)
        <a href="{{ route('ra.rooms.show', $room->id) }}" class="room-card {{ $cls }} clickable">
        @else
        <div class="room-card {{ $cls }} {{ !$task ? 'disabled' : 'non-clickable' }}">
        @endif

            {{-- Badge --}}
            @if(in_array($state, ['pending','in_progress']))
                <div class="room-badge st-inprogress" title="Sedang dikerjakan">▶</div>
            @elseif($state === 'pending_supervisor')
                <div class="room-badge st-waiting" title="Menunggu supervisor">⏳</div>
            @elseif($state === 'returned_to_ra')
                <div class="room-badge st-returned" title="Dikembalikan">!</div>
            @elseif($state === 'pending_manager')
                <div class="room-badge st-manager" title="Menunggu manager">✓</div>
            @endif

            <div class="room-number">{{ $room->room_number }}</div>
            <div class="room-status-pill">{{ $lbl }}</div>
            <div class="room-meta">{{ ucfirst($room->room_type) }} · Lantai {{ $room->floor }}</div>

            @if($task)
                <div class="room-progress-wrap">
                    <div class="room-progress-bar" style="width:{{ $task->overall_progress }}%"></div>
                </div>
                <div class="room-state">
                    @if($state === 'pending')            ⏸ Belum dimulai
                    @elseif($state === 'in_progress')    ▶ Sedang dikerjakan
                    @elseif($state === 'pending_supervisor') ⏳ Menunggu supervisor
                    @elseif($state === 'returned_to_ra') ⚠ Dikembalikan
                    @elseif($state === 'pending_manager') ✓ Menunggu manager
                    @else {{ $state }}
                    @endif
                </div>
            @endif

        @if($clickable) </a> @else </div> @endif
    @endforeach
</div>
@else
<div class="empty-state">
    <div class="empty-icon">🏨</div>
    <div class="empty-title">Belum Ada Kamar Ditugaskan</div>
    <div class="empty-desc">Hubungi admin untuk mendapatkan tugas kamar hari ini.</div>
</div>
@endif

@endsection
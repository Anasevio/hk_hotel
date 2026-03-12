@extends('layouts.ra')
@section('title','Kamar Saya')
@section('page-title','Kamar Saya')
@section('content')
@php $clsMap=['vacant_dirty'=>'vd','vacant_clean'=>'vc','vacant_ready'=>'vr','occupied'=>'oc','expected_departure'=>'ed'];
$lblMap=['vacant_dirty'=>'Vacant Dirty','vacant_clean'=>'Vacant Clean','vacant_ready'=>'Vacant Ready','occupied'=>'Occupied','expected_departure'=>'Exp. Departure']; @endphp
@if($rooms->count())
<div class="card">
    <div class="card-header"><span class="card-title">Kamar yang Ditugaskan ({{ $rooms->count() }})</span></div>
    <div class="room-grid">
        @foreach($rooms as $room)
        @php $cls = $clsMap[$room->status] ?? 'vd'; @endphp
        <a href="{{ route('ra.rooms.show', $room->id) }}" class="room-box {{ $cls }}">
            <div class="room-dot"></div>
            <div class="room-num">{{ $room->room_number }}</div>
            <div class="room-status">{{ $lblMap[$room->status] ?? $room->status }}</div>
            @if($room->activeTask)
            <div style="font-size:10px;color:#666;margin-top:4px">{{ $room->activeTask->overall_progress }}%</div>
            @endif
        </a>
        @endforeach
    </div>
</div>
@else
<div class="alert" style="background:#f0f4ff;border:1px solid #c5d5f0;color:#1a3a80">Belum ada kamar yang ditugaskan ke kamu.</div>
@endif
@endsection

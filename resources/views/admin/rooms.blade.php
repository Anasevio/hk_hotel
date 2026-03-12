@extends('layouts.admin')
@section('title','Status Kamar')
@section('page-title','Status Kamar')
@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('admin.rooms.index') }}" class="active"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('admin.users.index') }}"><i data-feather="users"></i> Kelola Akun</a>
<a href="{{ route('admin.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('admin.timer.index') }}"><i data-feather="clock"></i> Timer Tugas</a>
<a href="{{ route('admin.history.index') }}"><i data-feather="activity"></i> Log Aktivitas</a>
@endsection

@section('content')
@php
$clsMap = ['vacant_dirty'=>'vd','vacant_clean'=>'vc','vacant_ready'=>'vr','occupied'=>'oc','expected_departure'=>'ed'];
$lblMap  = ['vacant_dirty'=>'Vacant Dirty','vacant_clean'=>'Vacant Clean','vacant_ready'=>'Vacant Ready','occupied'=>'Occupied','expected_departure'=>'Exp. Departure'];
$floorGroups = $rooms->groupBy('floor');
@endphp

@foreach($floorGroups as $floor => $floorRooms)
<div class="card" style="margin-bottom:16px">
    <div class="card-header"><span class="card-title">Lantai {{ $floor }}</span></div>
    <div class="room-grid">
        @foreach($floorRooms as $room)
        @php $cls = $clsMap[$room->status] ?? 'vd'; @endphp
        <div class="room-box {{ $cls }}" onclick="openStatusModal({{ $room->id }},'{{ $room->room_number }}','{{ $room->status }}')">
            <div class="room-dot"></div>
            <div class="room-num">{{ $room->room_number }}</div>
            <div class="room-type">{{ ucfirst($room->room_type) }}</div>
            <div class="room-status">{{ $lblMap[$room->status] ?? $room->status }}</div>
            @if($room->assignedUser)
            <div style="font-size:10px;color:#666;margin-top:4px">👤 {{ $room->assignedUser->name }}</div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- MODAL UBAH STATUS --}}
<div class="modal-bg" id="modalStatus">
    <div class="modal">
        <div class="modal-title">🏨 Ubah Status Kamar <span id="modalRoomNum" style="color:#7A0200"></span></div>
        <form method="POST" id="formStatus">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Status Baru</label>
                <select name="status" id="statusSelect" class="form-control">
                    <option value="vacant_dirty">Vacant Dirty</option>
                    <option value="vacant_clean">Vacant Clean</option>
                    <option value="vacant_ready">Vacant Ready</option>
                    <option value="occupied">Occupied</option>
                    <option value="expected_departure">Expected Departure</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Alasan (opsional)</label>
                <input name="reason" class="form-control" placeholder="Contoh: Tamu check-out">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-secondary" onclick="document.getElementById('modalStatus').classList.remove('open')">Batal</button>
                <button type="submit" class="btn-sm btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openStatusModal(id, num, status) {
    document.getElementById('formStatus').action = '/admin/rooms/' + id + '/status';
    document.getElementById('modalRoomNum').textContent = num;
    document.getElementById('statusSelect').value = status;
    document.getElementById('modalStatus').classList.add('open');
}
document.querySelectorAll('.modal-bg').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); }));
</script>
@endpush

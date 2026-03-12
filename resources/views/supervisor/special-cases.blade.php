@extends('layouts.admin')
@section('title','Special Case')
@section('page-title','Special Case')
@section('sidebar-nav')
<a href="{{ route('supervisor.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('supervisor.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('supervisor.tasks.index') }}"><i data-feather="clipboard"></i> Kelola Tugas</a>
<a href="{{ route('supervisor.special-cases.index') }}" class="active"><i data-feather="alert-circle"></i> Special Case</a>
<a href="{{ route('supervisor.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('supervisor.history.index') }}"><i data-feather="activity"></i> Histori</a>
@endsection
@section('content')
<div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">➕ Buat Special Case</span></div>
    <form method="POST" action="{{ route('supervisor.special-cases.store') }}">
        @csrf
        <div class="grid-2">
            <div class="form-group"><label class="form-label">Kamar</label>
                <select name="room_id" class="form-control" required>
                    @foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->room_number }} — {{ $r->status_label }}</option>@endforeach
                </select></div>
            <div class="form-group"><label class="form-label">Jenis</label>
                <select name="type" class="form-control">
                    <option value="vip_room">VIP Room</option><option value="do_not_disturb">Do Not Disturb</option>
                    <option value="guest_sick">Tamu Sakit</option><option value="damage_report">Laporan Kerusakan</option>
                    <option value="lost_found">Lost & Found</option><option value="other">Lainnya</option>
                </select></div>
        </div>
        <div class="grid-2">
            <div class="form-group"><label class="form-label">Prioritas</label>
                <select name="priority" class="form-control">
                    <option value="normal">Normal</option><option value="high">High</option><option value="urgent">Urgent</option>
                </select></div>
            <div class="form-group"><label class="form-label">Assign ke RA (opsional)</label>
                <select name="assigned_to" class="form-control">
                    <option value="">- Tidak diassign -</option>
                    @foreach($raList as $ra)<option value="{{ $ra->id }}">{{ $ra->name }}</option>@endforeach
                </select></div>
        </div>
        <div class="form-group"><label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="2" required></textarea></div>
        <button type="submit" class="btn-sm btn-primary">Buat Special Case</button>
    </form>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Daftar Special Case</span></div>
    @forelse($cases as $c)
    <div style="padding:14px;border-bottom:1px solid #fafafa">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
            <div>
                <div style="font-weight:600;font-size:14px">{{ $c->type_label }} — Kamar {{ $c->room->room_number }}</div>
                <div style="font-size:12px;color:#666;margin-top:3px">{{ $c->description }}</div>
                <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap">
                    <span class="badge {{ $c->priority === 'urgent' ? 'badge-red' : ($c->priority === 'high' ? 'badge-orange' : 'badge-gray') }}">{{ ucfirst($c->priority) }}</span>
                    <span class="badge {{ $c->status === 'resolved' ? 'badge-green' : ($c->status === 'in_progress' ? 'badge-yellow' : 'badge-blue') }}">{{ ucfirst($c->status) }}</span>
                    @if($c->assignedUser)<span class="badge badge-gray">👤 {{ $c->assignedUser->name }}</span>@endif
                </div>
            </div>
            @if($c->status !== 'resolved')
            <button class="btn-sm btn-secondary" onclick="openResolve({{ $c->id }})">Selesaikan</button>
            @else
            <div style="font-size:11px;color:#888">{{ $c->resolved_at?->format('d/m H:i') }}</div>
            @endif
        </div>
        @if($c->resolution_notes)
        <div style="font-size:12px;color:#2e7d32;background:#e8f5e9;padding:8px;border-radius:6px;margin-top:8px">✅ {{ $c->resolution_notes }}</div>
        @endif
    </div>
    @empty
    <div style="text-align:center;color:#999;padding:24px">Tidak ada special case</div>
    @endforelse
</div>

<div class="modal-bg" id="modalResolve"><div class="modal">
    <div class="modal-title">✅ Selesaikan Special Case</div>
    <form method="POST" id="fResolve">@csrf
        <div class="form-group"><label class="form-label">Catatan Penyelesaian</label>
            <textarea name="resolution_notes" class="form-control" rows="3" required></textarea></div>
        <div class="modal-footer">
            <button type="button" class="btn-sm btn-secondary" onclick="document.getElementById('modalResolve').classList.remove('open')">Batal</button>
            <button type="submit" class="btn-sm btn-success">Tandai Selesai</button>
        </div>
    </form>
</div></div>
@endsection
@push('scripts')
<script>
function openResolve(id){
    document.getElementById('fResolve').action='/supervisor/special-cases/'+id+'/resolve';
    document.getElementById('modalResolve').classList.add('open');
}
document.querySelectorAll('.modal-bg').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open');}));
</script>
@endpush

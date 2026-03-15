@extends('layouts.admin')
@section('title', 'Status Kamar')
@section('page-title', 'Status Kamar')
@section('page-subtitle', 'Kelola status dan assign tugas ke Room Attendant')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('admin.rooms.index') }}" class="active"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('admin.users.index') }}"><i data-feather="users"></i> Kelola Akun</a>
<a href="{{ route('admin.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('admin.timer.index') }}"><i data-feather="clock"></i> Timer Tugas</a>
<a href="{{ route('admin.history.index') }}"><i data-feather="activity"></i> Log Aktivitas</a>
@endsection

@push('styles')
<style>
/* ── STATS ROW ── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
.stat-box {
    background: #fff;
    border-radius: 14px;
    padding: 16px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    border-left: 4px solid transparent;
}
.stat-box.vd { border-color: #e53935; }
.stat-box.vc { border-color: #1e88e5; }
.stat-box.vr { border-color: #43a047; }
.stat-box.oc { border-color: #f9a825; }
.stat-box.ed { border-color: #fb8c00; }
.stat-val   { font-size: 26px; font-weight: 800; color: #1a1a1a; line-height: 1; }
.stat-label { font-size: 11px; color: #888; margin-top: 4px; font-weight: 500; }

/* ── FILTER BAR ── */
.filter-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.filter-btn {
    padding: 6px 16px;
    border-radius: 20px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
    color: #666;
}
.filter-btn:hover, .filter-btn.active { background: #7A0200; color: #fff; border-color: #7A0200; }

/* ── ROOM GRID ── */
.room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 12px;
    margin-bottom: 8px;
}

/* ── ROOM BOX ── */
.room-box {
    border-radius: 12px;
    padding: 14px;
    position: relative;
    cursor: pointer;
    transition: transform .18s, box-shadow .18s;
    border: 1.5px solid transparent;
}
.room-box:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.room-box.vd { background: #fde8e8; border-color: #ef9a9a; }
.room-box.vc { background: #e3f2fd; border-color: #90caf9; }
.room-box.vr { background: #e8f5e9; border-color: #a5d6a7; }
.room-box.oc { background: #fff8e1; border-color: #ffe082; }
.room-box.ed { background: #fff3e0; border-color: #ffcc80; }

.room-dot { width: 8px; height: 8px; border-radius: 50%; position: absolute; top: 10px; right: 10px; }
.vd .room-dot { background: #e53935; box-shadow: 0 0 5px #e53935; }
.vc .room-dot { background: #1e88e5; box-shadow: 0 0 5px #1e88e5; }
.vr .room-dot { background: #43a047; box-shadow: 0 0 5px #43a047; }
.oc .room-dot { background: #f9a825; box-shadow: 0 0 5px #f9a825; }
.ed .room-dot { background: #fb8c00; box-shadow: 0 0 5px #fb8c00; }

.room-num    { font-size: 22px; font-weight: 800; color: #1a1a1a; }
.room-type   { font-size: 10px; color: #666; margin-top: 1px; }
.room-status { font-size: 10px; font-weight: 700; margin-top: 6px; }
.vd .room-status { color: #c62828; }
.vc .room-status { color: #1565c0; }
.vr .room-status { color: #2e7d32; }
.oc .room-status { color: #f57f17; }
.ed .room-status { color: #e65100; }

/* Assigned label */
.room-assigned {
    font-size: 10px;
    color: #555;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 3px;
    font-weight: 500;
}

/* Task progress bar di room box */
.room-task-progress {
    margin-top: 6px;
    height: 3px;
    background: rgba(0,0,0,0.1);
    border-radius: 3px;
    overflow: hidden;
}
.room-task-bar {
    height: 100%;
    background: #7A0200;
    border-radius: 3px;
}

/* Assign badge */
.need-assign-badge {
    position: absolute;
    top: -4px; right: -4px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: #e53935;
    border: 2px solid #fff;
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.2); }
}

/* ── FLOOR LABEL ── */
.floor-label {
    font-size: 12px;
    font-weight: 700;
    color: #999;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 10px;
    margin-top: 4px;
}

/* ── MODAL ── */
.modal-bg {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 200;
    align-items: center;
    justify-content: center;
}
.modal-bg.open { display: flex; }

.modal {
    background: #fff;
    border-radius: 18px;
    padding: 28px;
    width: 90%;
    max-width: 480px;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { transform: translateY(16px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
.modal-title { font-size: 17px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; }
.modal-sub   { font-size: 12px; color: #888; margin-bottom: 20px; }
.modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }

/* Tab di dalam modal */
.modal-tabs {
    display: flex;
    border-bottom: 2px solid #f5f5f5;
    margin-bottom: 18px;
    gap: 0;
}
.modal-tab {
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
}
.modal-tab.active { color: #7A0200; border-color: #7A0200; }

.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* Info row di modal */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 13px;
    border-bottom: 1px solid #fafafa;
}
.info-row:last-child { border-bottom: none; }
.info-key { color: #888; }
.info-val { color: #1a1a1a; font-weight: 600; }

/* Divider */
.divider { height: 1px; background: #f5f5f5; margin: 16px 0; }

@media (max-width: 768px) {
    .stats-row { grid-template-columns: repeat(3, 1fr); }
}
</style>
@endpush

@section('content')

@php
$clsMap = ['vacant_dirty'=>'vd','vacant_clean'=>'vc','vacant_ready'=>'vr','occupied'=>'oc','expected_departure'=>'ed'];
$lblMap  = ['vacant_dirty'=>'Vacant Dirty','vacant_clean'=>'Vacant Clean','vacant_ready'=>'Vacant Ready','occupied'=>'Occupied','expected_departure'=>'Exp. Departure'];

// Hitung stats
$stats = [
    'vd' => $rooms->where('status','vacant_dirty')->count(),
    'vc' => $rooms->where('status','vacant_clean')->count(),
    'vr' => $rooms->where('status','vacant_ready')->count(),
    'oc' => $rooms->where('status','occupied')->count(),
    'ed' => $rooms->where('status','expected_departure')->count(),
];
$floorGroups = $rooms->groupBy('floor');
@endphp

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box vd">
        <div class="stat-val">{{ $stats['vd'] }}</div>
        <div class="stat-label">Vacant Dirty</div>
    </div>
    <div class="stat-box vc">
        <div class="stat-val">{{ $stats['vc'] }}</div>
        <div class="stat-label">Vacant Clean</div>
    </div>
    <div class="stat-box vr">
        <div class="stat-val">{{ $stats['vr'] }}</div>
        <div class="stat-label">Vacant Ready</div>
    </div>
    <div class="stat-box oc">
        <div class="stat-val">{{ $stats['oc'] }}</div>
        <div class="stat-label">Occupied</div>
    </div>
    <div class="stat-box ed">
        <div class="stat-val">{{ $stats['ed'] }}</div>
        <div class="stat-label">Exp. Departure</div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar">
    <button class="filter-btn active" onclick="filterRooms('all', this)">Semua</button>
    <button class="filter-btn" onclick="filterRooms('vd', this)">Vacant Dirty</button>
    <button class="filter-btn" onclick="filterRooms('vc', this)">Vacant Clean</button>
    <button class="filter-btn" onclick="filterRooms('vr', this)">Vacant Ready</button>
    <button class="filter-btn" onclick="filterRooms('oc', this)">Occupied</button>
    <button class="filter-btn" onclick="filterRooms('ed', this)">Exp. Departure</button>
</div>

{{-- Room Grid per Lantai --}}
@foreach($floorGroups as $floor => $floorRooms)
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span class="card-title">Lantai {{ $floor }}</span>
        <span style="font-size:12px;color:#888">{{ $floorRooms->count() }} kamar</span>
    </div>
    <div class="room-grid">
        @foreach($floorRooms as $room)
            @php
                $cls       = $clsMap[$room->status] ?? 'vd';
                $lbl       = $lblMap[$room->status] ?? $room->status;
                $activeTask = $room->activeTask;
                $needAssign = in_array($room->status, ['vacant_dirty','expected_departure'])
                              && !$activeTask;
            @endphp
            <div class="room-box {{ $cls }}"
                 data-status="{{ $cls }}"
                 onclick="openRoomModal(
                    {{ $room->id }},
                    '{{ $room->room_number }}',
                    '{{ $room->status }}',
                    '{{ $lbl }}',
                    '{{ $room->room_type }}',
                    {{ $room->floor }},
                    {{ $room->assigned_to ?? 'null' }},
                    {{ $activeTask ? $activeTask->id : 'null' }},
                    '{{ $activeTask?->status ?? '' }}'
                 )">

                <div class="room-dot"></div>

                {{-- Pulse badge jika perlu di-assign --}}
                @if($needAssign)
                    <div class="need-assign-badge" title="Butuh RA"></div>
                @endif

                <div class="room-num">{{ $room->room_number }}</div>
                <div class="room-type">{{ ucfirst($room->room_type) }}</div>
                <div class="room-status">{{ $lbl }}</div>

                @if($room->assignedUser)
                    <div class="room-assigned">
                        👤 {{ $room->assignedUser->name }}
                    </div>
                @endif

                @if($activeTask)
                    <div class="room-task-progress">
                        <div class="room-task-bar"
                             style="width:{{ $activeTask->overall_progress }}%"></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- ============================================
     MODAL DETAIL KAMAR
     ============================================ --}}
<div class="modal-bg" id="modalRoom">
    <div class="modal">
        <div class="modal-title" id="modal-room-title">Kamar —</div>
        <div class="modal-sub" id="modal-room-sub">—</div>

        {{-- Tabs --}}
        <div class="modal-tabs">
            <div class="modal-tab active" onclick="switchTab('tab-info')">Info & Status</div>
            <div class="modal-tab" id="tab-assign-btn" onclick="switchTab('tab-assign')">Assign ke RA</div>
        </div>

        {{-- Tab: Info --}}
        <div class="tab-panel active" id="tab-info">
            <div class="info-row">
                <span class="info-key">Status</span>
                <span class="info-val" id="info-status">—</span>
            </div>
            <div class="info-row">
                <span class="info-key">Tipe</span>
                <span class="info-val" id="info-type">—</span>
            </div>
            <div class="info-row">
                <span class="info-key">Lantai</span>
                <span class="info-val" id="info-floor">—</span>
            </div>
            <div class="info-row">
                <span class="info-key">RA Ditugaskan</span>
                <span class="info-val" id="info-assigned">Belum ada</span>
            </div>
            <div class="info-row" id="info-task-row">
                <span class="info-key">Status Tugas</span>
                <span class="info-val" id="info-task-status">—</span>
            </div>

            <div class="divider"></div>

            {{-- Ubah Status Kamar --}}
            <form method="POST" id="form-status">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Ubah Status Kamar</label>
                    <select name="status" id="status-select" class="form-control">
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
                    <button type="button" class="btn-sm btn-secondary"
                            onclick="closeModal()">Tutup</button>
                    <button type="submit" class="btn-sm btn-primary">Simpan Status</button>
                </div>
            </form>
        </div>

        {{-- Tab: Assign ke RA --}}
        <div class="tab-panel" id="tab-assign">
            <div id="assign-warning" style="display:none;padding:10px 14px;background:#fff8e1;
                border:1px solid #ffe082;border-radius:10px;font-size:12px;
                color:#5a3e00;margin-bottom:16px">
            </div>

            <form method="POST" action="{{ route('admin.tasks.assign') }}" id="form-assign">
                @csrf
                <input type="hidden" name="room_id" id="assign-room-id">
                <div class="form-group">
                    <label class="form-label">Pilih Room Attendant</label>
                    <select name="assigned_to" class="form-control" id="assign-ra-select">
                        <option value="">— Pilih RA —</option>
                        @foreach($raList as $ra)
                            <option value="{{ $ra->id }}">
                                {{ $ra->name }}
                                (Shift {{ ucfirst($ra->shift) }})
                                — {{ $ra->assignedRooms->count() }} kamar aktif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-secondary"
                            onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-sm btn-primary"
                            id="btn-assign">Assign Tugas</button>
                </div>
            </form>

            {{-- Cancel task (jika sudah ada task aktif) --}}
            <div id="cancel-task-section" style="display:none">
                <div class="divider"></div>
                <div style="font-size:12px;color:#888;margin-bottom:10px">
                    Sudah ada tugas aktif untuk kamar ini.
                </div>
                <form method="POST" id="form-cancel">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm btn-danger"
                            onclick="return confirm('Batalkan tugas ini?')">
                        Batalkan Tugas
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
const TASK_STATUS_LABELS = {
    'pending'                : 'Menunggu Dimulai',
    'in_progress'            : 'Sedang Dikerjakan',
    'pending_supervisor'     : 'Menunggu Supervisor',
    'returned_to_ra'         : 'Dikembalikan ke RA',
    'pending_manager'        : 'Menunggu Manager',
    'returned_to_supervisor' : 'Dikembalikan ke Supervisor',
    'completed'              : 'Selesai',
};

const RA_NAMES = {
    @foreach($raList as $ra)
    {{ $ra->id }}: '{{ $ra->name }}',
    @endforeach
};

// ── OPEN MODAL ────────────────────────────────────────────
function openRoomModal(id, num, status, statusLabel, type, floor, assignedTo, taskId, taskStatus) {
    // Set info
    document.getElementById('modal-room-title').textContent = `Kamar ${num}`;
    document.getElementById('modal-room-sub').textContent   = `${type.charAt(0).toUpperCase() + type.slice(1)} · Lantai ${floor}`;
    document.getElementById('info-status').textContent      = statusLabel;
    document.getElementById('info-type').textContent        = type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('info-floor').textContent       = `Lantai ${floor}`;
    document.getElementById('info-assigned').textContent    = assignedTo ? (RA_NAMES[assignedTo] ?? 'RA #' + assignedTo) : 'Belum ada';

    // Task status row
    const taskRow = document.getElementById('info-task-row');
    if (taskStatus) {
        taskRow.style.display = 'flex';
        document.getElementById('info-task-status').textContent = TASK_STATUS_LABELS[taskStatus] ?? taskStatus;
    } else {
        taskRow.style.display = 'none';
    }

    // Form ubah status
    document.getElementById('form-status').action = `/admin/rooms/${id}/status`;
    document.getElementById('status-select').value = status;

    // Form assign
    document.getElementById('assign-room-id').value = id;

    // Tab assign — warning jika status bukan VD/ED
    const canAssign = ['vacant_dirty', 'expected_departure'].includes(status);
    const warning   = document.getElementById('assign-warning');
    const btnAssign = document.getElementById('btn-assign');

    if (!canAssign) {
        warning.style.display  = 'block';
        warning.textContent    = `⚠ Kamar ini berstatus "${statusLabel}" — hanya Vacant Dirty atau Expected Departure yang bisa diassign.`;
        btnAssign.disabled     = true;
    } else {
        warning.style.display  = 'none';
        btnAssign.disabled     = false;
    }

    // Cancel task section
    const cancelSection = document.getElementById('cancel-task-section');
    if (taskId && ['pending','in_progress'].includes(taskStatus)) {
        cancelSection.style.display = 'block';
        document.getElementById('form-cancel').action = `/admin/tasks/${taskId}/cancel`;
    } else {
        cancelSection.style.display = 'none';
    }

    // Reset ke tab info
    switchTab('tab-info');
    document.getElementById('modalRoom').classList.add('open');
}

function closeModal() {
    document.getElementById('modalRoom').classList.remove('open');
}

// Tutup modal klik backdrop
document.getElementById('modalRoom').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// ── TABS ──────────────────────────────────────────────────
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    // Highlight tab button yang aktif
    const idx = tabId === 'tab-info' ? 0 : 1;
    document.querySelectorAll('.modal-tab')[idx].classList.add('active');
}

// ── FILTER ────────────────────────────────────────────────
function filterRooms(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.room-box').forEach(box => {
        const show = status === 'all' || box.dataset.status === status;
        box.style.display = show ? '' : 'none';
    });
}
</script>
@endpush
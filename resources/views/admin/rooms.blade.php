@extends('layouts.admin')
@section('title', 'Status Kamar')
@section('page-title', 'Status Kamar')
@section('page-subtitle', 'Kelola status dan assign tugas ke Room Attendant')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/rooms.css') }}">
@endpush

@section('content')

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box vd">
        <div class="stat-val">{{ $rooms->where('status','vacant_dirty')->count() }}</div>
        <div class="stat-label">Vacant Dirty</div>
    </div>
    <div class="stat-box vc">
        <div class="stat-val">{{ $rooms->where('status','vacant_clean')->count() }}</div>
        <div class="stat-label">Vacant Clean</div>
    </div>
    <div class="stat-box vr">
        <div class="stat-val">{{ $rooms->where('status','vacant_ready')->count() }}</div>
        <div class="stat-label">Vacant Ready</div>
    </div>
    <div class="stat-box oc">
        <div class="stat-val">{{ $rooms->where('status','occupied')->count() }}</div>
        <div class="stat-label">Occupied</div>
    </div>
    <div class="stat-box ed">
        <div class="stat-val">{{ $rooms->where('status','expected_departure')->count() }}</div>
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
                // Loop helpers — tetap di blade, terlalu granular untuk controller
                $cls        = $clsMap[$room->status] ?? 'vd';
                $lbl        = $lblMap[$room->status] ?? $room->status;
                $activeTask = $room->activeTask;
                $needAssign = in_array($room->status, ['vacant_dirty','expected_departure']) && !$activeTask;
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

{{-- MODAL DETAIL KAMAR --}}
<div class="modal-bg" id="modalRoom">
    <div class="modal">
        <div class="modal-title" id="modal-room-title">Kamar —</div>
        <div class="modal-sub" id="modal-room-sub">—</div>

        <div class="modal-tabs">
            <div class="modal-tab active" onclick="switchTab('tab-info')">Info & Status</div>
            <div class="modal-tab" id="tab-assign-btn" onclick="switchTab('tab-assign')">Assign ke RA</div>
        </div>

        {{-- Tab: Info & Status --}}
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

            <form method="POST" id="form-status">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Ubah Status Kamar</label>
                    <select name="status" id="status-select" class="form-control">
                        <option value="vacant_dirty">Vacant Dirty</option>
                        <option value="occupied">Occupied</option>
                        <option value="expected_departure">Expected Departure</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Alasan (opsional)</label>
                    <input name="reason" class="form-control" placeholder="Contoh: Tamu check-out">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-secondary" onclick="closeModal()">Tutup</button>
                    <button type="submit" class="btn-sm btn-primary">Simpan Status</button>
                </div>
            </form>
        </div>

        {{-- Tab: Assign ke RA --}}
        <div class="tab-panel" id="tab-assign">
            <div id="assign-warning" style="display:none; padding:10px 14px; background:#fff8e1;
                border:1px solid #ffe082; border-radius:10px; font-size:12px; color:#856404; margin-bottom:12px">
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
— {{ $ra->assignedRooms->count() }} kamar aktif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Durasi Timer
                        <span class="timer-hint" id="timer-default-hint"></span>
                    </label>
                    <div class="timer-input-wrap">
                        <input type="number" name="time_limit" id="assign-duration"
                               class="form-control" min="1" placeholder="20" />
                        <span style="font-size:13px; color:#888">menit</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-sm btn-primary" id="btn-assign">Assign Tugas</button>
                </div>
            </form>

            <div id="cancel-task-section" style="display:none">
                <div class="divider"></div>
                <div style="font-size:12px; color:#888; margin-bottom:10px">
                    Sudah ada tugas aktif untuk kamar ini.
                </div>
                <form method="POST" id="form-cancel" data-base-url="{{ route('admin.tasks.cancel', ['task' => '__ID__']) }}">
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
const TIMER_DEFAULTS = {
    @foreach($timerSettings as $t)
    '{{ $t->key }}': { label: '{{ $t->label }}', duration: {{ $t->duration_minutes }} },
    @endforeach
};

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

function openRoomModal(id, num, status, statusLabel, type, floor, assignedTo, taskId, taskStatus) {
    document.getElementById('modal-room-title').textContent = `Kamar ${num}`;
    document.getElementById('modal-room-sub').textContent   = `${type.charAt(0).toUpperCase() + type.slice(1)} · Lantai ${floor}`;
    document.getElementById('info-status').textContent      = statusLabel;
    document.getElementById('info-type').textContent        = type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('info-floor').textContent       = `Lantai ${floor}`;
    document.getElementById('info-assigned').textContent    = assignedTo ? (RA_NAMES[assignedTo] ?? 'RA #' + assignedTo) : 'Belum ada';

    const taskRow = document.getElementById('info-task-row');
    if (taskStatus) {
        taskRow.style.display = 'flex';
        document.getElementById('info-task-status').textContent = TASK_STATUS_LABELS[taskStatus] ?? taskStatus;
    } else {
        taskRow.style.display = 'none';
    }

    document.getElementById('form-status').action = `/admin/rooms/${id}/status`;
    document.getElementById('status-select').value = status;
    document.getElementById('assign-room-id').value = id;

    const roomKey  = `room_${num}`;
    const typeKey  = type.toLowerCase().replace(' ', '_');
    const timerData = TIMER_DEFAULTS[roomKey] ?? TIMER_DEFAULTS[typeKey] ?? null;
    const durationInput = document.getElementById('assign-duration');
    const timerHint     = document.getElementById('timer-default-hint');

    if (timerData) {
        durationInput.value   = timerData.duration;
        timerHint.textContent = `(default ${timerData.label}: ${timerData.duration} menit)`;
    } else {
        durationInput.value   = '';
        timerHint.textContent = '';
    }

    const canAssign = ['vacant_dirty', 'expected_departure'].includes(status);
    const warning   = document.getElementById('assign-warning');
    const btnAssign = document.getElementById('btn-assign');

    if (!canAssign) {
        warning.style.display = 'block';
        warning.textContent   = `⚠ Kamar ini berstatus "${statusLabel}" — hanya Vacant Dirty atau Expected Departure yang bisa diassign.`;
        btnAssign.disabled    = true;
    } else {
        warning.style.display = 'none';
        btnAssign.disabled    = false;
    }

    const cancelSection = document.getElementById('cancel-task-section');
    if (taskId && ['pending', 'in_progress'].includes(taskStatus)) {
        cancelSection.style.display = 'block';
        document.getElementById('form-cancel').action = document.getElementById('form-cancel').dataset.baseUrl.replace('__ID__', taskId);
    } else {
        cancelSection.style.display = 'none';
    }

    switchTab('tab-info');
    document.getElementById('modalRoom').classList.add('open');
}

function closeModal() {
    document.getElementById('modalRoom').classList.remove('open');
}

document.getElementById('modalRoom').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    const idx = tabId === 'tab-info' ? 0 : 1;
    document.querySelectorAll('.modal-tab')[idx].classList.add('active');
}

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
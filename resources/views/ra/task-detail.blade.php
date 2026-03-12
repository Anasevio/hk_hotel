@extends('layouts.ra')
@section('title','Detail Tugas')
@section('page-title','Tugas Kamar ' . $task->room->room_number)
@section('content')

<div class="card" style="margin-bottom:16px">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
        <div style="font-size:13px;color:#666">
            Status: <span class="badge badge-orange">{{ $task->status_label }}</span>
            · Batas: <strong>{{ $task->time_limit }} menit</strong>
            @if($task->started_at) · Mulai: <strong>{{ $task->started_at->format('H:i') }}</strong> @endif
            @if($task->isOvertime()) <span class="badge badge-red">Overtime!</span> @endif
        </div>
        @if(in_array($task->status, ['pending', 'returned_to_ra']))
        <form method="POST" action="{{ route('ra.tasks.start', $task->id) }}">
            @csrf <button type="submit" class="btn-sm btn-primary">▶ Mulai Tugas</button>
        </form>
        @endif
    </div>
</div>

@if($task->supervisor_note)
<div class="alert alert-warn" style="margin-bottom:16px">📋 <strong>Catatan Supervisor:</strong> {{ $task->supervisor_note }}</div>
@endif

{{-- CHECKLIST 1: PERSIAPAN --}}
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span class="card-title">📦 Checklist Persiapan</span>
        <span style="font-size:12px;color:#888" id="prep-pct">{{ $task->checklist1_progress }}%</span>
    </div>
    <div class="progress" style="margin-bottom:14px" id="prep-bar-wrap">
        <div class="progress-bar" id="prep-bar" style="width:{{ $task->checklist1_progress }}%"></div>
    </div>
    @foreach($task->preparationChecklists as $item)
    <div class="checklist-item {{ $item->is_checked ? 'checked' : '' }}" id="item-{{ $item->id }}"
        onclick="{{ $task->status === 'in_progress' ? 'toggleCheck('.$item->id.', this)' : '' }}"
        style="{{ $task->status !== 'in_progress' ? 'cursor:default;opacity:.8' : '' }}">
        <div class="check-box">{{ $item->is_checked ? '✓' : '' }}</div>
        <span class="item-label">{{ $item->item_name }}</span>
    </div>
    @endforeach
</div>

{{-- CHECKLIST 2: PEMBERSIHAN --}}
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span class="card-title">🧹 Checklist Pembersihan</span>
        <span style="font-size:12px;color:#888" id="clean-pct">{{ $task->checklist2_progress }}%</span>
    </div>
    <div class="progress" style="margin-bottom:14px">
        <div class="progress-bar" id="clean-bar" style="width:{{ $task->checklist2_progress }}%"></div>
    </div>
    @foreach($task->cleaningChecklists as $item)
    <div class="checklist-item {{ $item->is_checked ? 'checked' : '' }}" id="item-{{ $item->id }}"
        onclick="{{ $task->status === 'in_progress' ? 'toggleCheck('.$item->id.', this)' : '' }}"
        style="{{ $task->status !== 'in_progress' ? 'cursor:default;opacity:.8' : '' }}">
        <div class="check-box">{{ $item->is_checked ? '✓' : '' }}</div>
        <span class="item-label">{{ $item->item_name }}</span>
        @if($item->estimated_minutes)<span class="item-time">~{{ $item->estimated_minutes }}m</span>@endif
    </div>
    @endforeach
</div>

@if($task->status === 'in_progress')
<form method="POST" action="{{ route('ra.tasks.submit', $task->id) }}">
    @csrf
    <button type="submit" class="btn-sm btn-success" style="width:100%;padding:14px;font-size:15px">
        📤 Submit ke Supervisor
    </button>
</form>
@endif
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;
function toggleCheck(id, el) {
    const isChecked = !el.classList.contains('checked');
    fetch('{{ route("ra.tasks.checklist", $task->id) }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({checklist_id: id, is_checked: isChecked})
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            el.classList.toggle('checked', isChecked);
            el.querySelector('.check-box').textContent = isChecked ? '✓' : '';
            document.getElementById('prep-bar').style.width = d.checklist1_progress + '%';
            document.getElementById('prep-pct').textContent = d.checklist1_progress + '%';
            document.getElementById('clean-bar').style.width = d.checklist2_progress + '%';
            document.getElementById('clean-pct').textContent = d.checklist2_progress + '%';
        }
    });
}
</script>
@endpush

@extends('layouts.supervisor')
@section('title','Riwayat')
@section('content')

<a href="{{ route('supervisor.dashboard') }}" class="back-link">← Kembali</a>

<div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary-pink));border-radius:16px;padding:20px 24px;
    text-align:center;margin-bottom:20px;box-shadow:0 4px 16px rgba(168,85,120,.4)">
    <div style="color:#fff;font-size:22px;font-weight:700">Riwayat Tugas</div>
    <div style="color:rgba(255,255,255,.75);font-size:13px;margin-top:4px">{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

{{-- Stats --}}
<div class="abs-grid" style="margin-bottom:20px">
    <div class="abs-box">
        <div class="abs-icon">✅</div>
        <div class="abs-val" style="color:#2e7d32">{{ $stats['approved'] }}</div>
        <div class="abs-label">Approved</div>
    </div>
    <div class="abs-box">
        <div class="abs-icon">↩️</div>
        <div class="abs-val" style="color:#f57f17">{{ $stats['returned'] }}</div>
        <div class="abs-label">Dikembalikan</div>
    </div>
    <div class="abs-box">
        <div class="abs-icon">📋</div>
        <div class="abs-val" style="color:#1565c0">{{ $stats['total'] }}</div>
        <div class="abs-label">Total</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

{{-- Profil --}}
<div>
    <div class="card" style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
        <div style="width:52px;height:52px;border-radius:50%;background:#fde8e8;color:#7A0200;
            display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0">
            {{ strtoupper(substr(auth()->user()->name,0,1)) }}
        </div>
        <div style="flex:1">
            <div style="font-weight:700;font-size:14px">{{ auth()->user()->name }}</div>
            <div style="font-size:11px;color:#888">Supervisor</div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;text-align:center">
            <div><div style="font-size:18px;font-weight:800;color:#2e7d32">{{ $stats['approved'] }}</div><div style="font-size:10px;color:#888">Approved</div></div>
            <div><div style="font-size:18px;font-weight:800;color:#f57f17">{{ $stats['returned'] }}</div><div style="font-size:10px;color:#888">Return</div></div>
            <div><div style="font-size:18px;font-weight:800;color:#1565c0">{{ $stats['total'] }}</div><div style="font-size:10px;color:#888">Total</div></div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card" style="margin-bottom:0">
        <div style="font-size:13px;font-weight:600;color:#444;margin-bottom:10px">Filter Status</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button onclick="filterRiwayat('all', this)" class="filter-btn active-filter"
                style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;
                border:2px solid #c2718f;background:#c2718f;color:#fff;cursor:pointer;font-family:inherit;transition:all .2s">
                Semua
            </button>
            <button onclick="filterRiwayat('completed', this)" class="filter-btn"
                style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;
                border:2px solid #2e7d32;background:#fff;color:#2e7d32;cursor:pointer;font-family:inherit;transition:all .2s">
                ✅ Approved
            </button>
            <button onclick="filterRiwayat('cancelled', this)" class="filter-btn"
                style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;
                border:2px solid #f57f17;background:#fff;color:#f57f17;cursor:pointer;font-family:inherit;transition:all .2s">
                ↩️ Dikembalikan
            </button>
        </div>
    </div>
</div>

{{-- List Riwayat --}}
<div>
    <div style="font-size:14px;font-weight:700;color:#f57f17;margin-bottom:10px">Riwayat Tugas</div>

    @forelse($history as $task)
    <div class="riwayat-item" data-status="{{ $task->status }}">
        <div class="riwayat-icon">
            @if($task->status === 'completed') ✅
            @else ↩️
            @endif
        </div>
        <div class="riwayat-info">
            <div class="riwayat-date">
                {{ $task->updated_at->translatedFormat('l, d F Y') }} · {{ $task->updated_at->format('H:i') }}
            </div>
            <div class="riwayat-label">
                Kamar <strong>{{ $task->room->room_number ?? '-' }}</strong>
                @if($task->notes) · {{ Str::limit($task->notes, 40) }} @endif
            </div>
        </div>
        <span class="riwayat-badge {{ $task->status === 'completed' ? 'rb-masuk' : 'rb-izin' }}">
            {{ $task->status === 'completed' ? 'Approved' : 'Dikembalikan' }}
        </span>
    </div>
    @empty
    <div style="text-align:center;color:#aaa;padding:20px;font-size:13px">Belum ada riwayat tugas</div>
    @endforelse
</div>

</div>

@endsection

@push('scripts')
<script>
function filterRiwayat(status, el) {
    // Update button style
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('active-filter');
        b.style.background = '#fff';
        b.style.color = b.dataset.origColor ?? b.style.color;
    });
    el.style.background = '#c2718f';
    el.style.color = '#fff';
    el.style.borderColor = '#c2718f';

    // Filter items
    document.querySelectorAll('.riwayat-item').forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

document.querySelectorAll('.filter-btn').forEach(b => {
    b.dataset.origColor = b.style.color;
});
</script>
@endpush
@extends('layouts.supervisor')
@section('title','Absensi')
@section('content')

<a href="{{ route('supervisor.dashboard') }}" class="back-link">← Kembali</a>
<link rel="stylesheet" href="{{ asset('css/supervisor/absensi_sv.css') }}">

<div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary-pink));border-radius:16px;padding:20px 24px;
    text-align:center;margin-bottom:20px;box-shadow:0 4px 16px rgba(168,85,120,.4)">
    <div style="color:#fff;font-size:22px;font-weight:700">Absensi</div>
    <div style="color:rgba(255,255,255,.75);font-size:13px;margin-top:4px">{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div>
    <div class="card">
        <div style="font-size:12px;color:#888;margin-bottom:2px">Status Hari Ini</div>
        <div style="font-size:11px;color:#aaa;margin-bottom:12px">{{ now()->translatedFormat('l, d F Y') }}</div>
        <div style="font-size:12px;color:#888">Nama Petugas</div>
        <div style="font-size:15px;font-weight:700;color:#1a1a1a;margin-bottom:2px">{{ auth()->user()->name }}</div>
        <div style="font-size:11px;color:#aaa;margin-bottom:16px">Username: {{ auth()->user()->username }}</div>
        <hr style="border:none;border-top:1px solid #f0f0f0;margin-bottom:16px">

        @if(!$todayAtt)
        <div style="font-size:13px;font-weight:600;color:#1a1a1a;margin-bottom:10px">Detail Absensi</div>
        <div style="font-size:12px;color:#888;margin-bottom:8px">Status Kehadiran</div>
        <form method="POST" action="{{ route('supervisor.attendance.checkin') }}" id="formAbsen">
            @csrf
            <input type="hidden" name="status" id="statusInput" value="">
            <div style="display:flex;gap:8px;margin-bottom:16px">
                <button type="button" onclick="pilihStatus('hadir', this)" class="status-btn"
                    style="padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;
                    border:2px solid #2e7d32;background:#fff;color:#2e7d32;cursor:pointer;font-family:inherit;transition:all .2s">
                    ✅ Hadir
                </button>
                <button type="button" onclick="pilihStatus('izin', this)" class="status-btn"
                    style="padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;
                    border:2px solid #f57f17;background:#fff;color:#f57f17;cursor:pointer;font-family:inherit;transition:all .2s">
                    📝 Izin
                </button>
                <button type="button" onclick="pilihStatus('sakit', this)" class="status-btn"
                    style="padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;
                    border:2px solid #1565c0;background:#fff;color:#1565c0;cursor:pointer;font-family:inherit;transition:all .2s">
                    🏥 Sakit
                </button>
            </div>
            <div id="keteranganWrap" style="display:none;margin-bottom:14px">
                <label style="font-size:12px;font-weight:600;color:#444;display:block;margin-bottom:6px">Keterangan</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Tulis keterangan..."></textarea>
            </div>
            <button type="submit" id="btnSubmit" disabled
                style="width:100%;padding:12px;border-radius:10px;font-size:14px;font-weight:700;
                border:none;cursor:not-allowed;font-family:inherit;background:#e0e0e0;color:#aaa;transition:all .2s">
                Pilih status dahulu
            </button>
        </form>

        @elseif($todayAtt->status === 'hadir' && !$todayAtt->check_out)
        {{-- Hadir, belum absen keluar --}}
        <div style="background:#e8f5e9;border-radius:10px;padding:12px;margin-bottom:14px">
            <div style="font-size:12px;color:#2e7d32;font-weight:600">✅ Sudah Absen Masuk</div>
            <div style="font-size:13px;color:#1a1a1a;margin-top:4px">
                Pukul <strong>{{ $todayAtt->check_in }}</strong>
            </div>
        </div>
        <form method="POST" action="{{ route('supervisor.attendance.checkout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-block">🏠 Absen Keluar</button>
        </form>

        @else
        <div style="background:#e8f5e9;border-radius:10px;padding:14px">
            <div style="font-size:13px;color:#2e7d32;font-weight:600;margin-bottom:6px">✅ Absensi Selesai</div>
            <div style="font-size:13px;color:#444;line-height:2">
                Masuk: <strong>{{ $todayAtt->check_in }}</strong><br>
                Keluar: <strong>{{ $todayAtt->check_out }}</strong><br>
                Durasi: <strong>{{ $todayAtt->work_duration ?? '-' }}</strong><br>
                Status: <strong>{{ ucfirst($todayAtt->status) }}</strong>
            </div>
        </div>
        @endif
    </div>

    <div class="abs-grid">
        <div class="abs-box"><div class="abs-icon">✅</div><div class="abs-val" style="color:#2e7d32">{{ $stats['hadir'] }}</div><div class="abs-label">Hadir</div></div>
        <div class="abs-box"><div class="abs-icon">📝</div><div class="abs-val" style="color:#f57f17">{{ $stats['izin'] }}</div><div class="abs-label">Izin</div></div>
        <div class="abs-box"><div class="abs-icon">🏥</div><div class="abs-val" style="color:#1565c0">{{ $stats['sakit'] }}</div><div class="abs-label">Sakit</div></div>
        <div class="abs-box"><div class="abs-icon">❌</div><div class="abs-val" style="color:#c62828">{{ $stats['alfa'] }}</div><div class="abs-label">Alpha</div></div>
    </div>
</div>

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
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;text-align:center">
            <div><div style="font-size:18px;font-weight:800">{{ $stats['hadir'] }}</div><div style="font-size:10px;color:#888">Hadir</div></div>
            <div><div style="font-size:18px;font-weight:800;color:#f57f17">{{ $stats['izin'] }}</div><div style="font-size:10px;color:#888">Izin</div></div>
            <div><div style="font-size:18px;font-weight:800;color:#1565c0">{{ $stats['sakit'] }}</div><div style="font-size:10px;color:#888">Skt</div></div>
            <div><div style="font-size:18px;font-weight:800;color:#c62828">{{ $stats['alfa'] }}</div><div style="font-size:10px;color:#888">Alfa</div></div>
        </div>
    </div>

    <div style="font-size:14px;font-weight:700;color:#f57f17;margin-bottom:10px">Riwayat Absensi</div>
    @forelse($history->take(6) as $r)
    <div class="riwayat-item">
        <div class="riwayat-icon">📊</div>
        <div class="riwayat-info">
            <div class="riwayat-date">{{ $r->date->format('l, d F Y') }} · {{ $r->check_in ?? '-' }}</div>
            <div class="riwayat-label">
                @if($r->status==='hadir') ↩ Absen Masuk
                @elseif($r->status==='izin') 📝 Izin
                @elseif($r->status==='sakit') 🏥 Sakit
                @else ❌ Alpha @endif
            </div>
        </div>
        <span class="riwayat-badge {{ $r->status==='hadir'?'rb-masuk':($r->status==='izin'?'rb-izin':'rb-sakit') }}">
            {{ ucfirst($r->status) }}
        </span>
    </div>
    @empty
    <div style="text-align:center;color:#aaa;padding:20px;font-size:13px">Belum ada riwayat</div>
    @endforelse
</div>
</div>

@endsection

@push('scripts')
<script>
const colors = {
    hadir: { bg:'#2e7d32', text:'#fff' },
    izin:  { bg:'#f57f17', text:'#fff' },
    sakit: { bg:'#1565c0', text:'#fff' },
};
function pilihStatus(status, el) {
    document.querySelectorAll('.status-btn').forEach(b => {
        b.style.background = '#fff';
        b.style.color = b.dataset.origColor;
    });
    const c = colors[status];
    el.style.background = c.bg;
    el.style.color = c.text;
    document.getElementById('statusInput').value = status;
    document.getElementById('keteranganWrap').style.display =
        (status === 'izin' || status === 'sakit') ? 'block' : 'none';
    const btn = document.getElementById('btnSubmit');
    btn.disabled = false;
    btn.style.background = '#c2718f';
    btn.style.color = '#fff';
    btn.style.cursor = 'pointer';
    btn.textContent = 'Absen Sekarang';
}
document.querySelectorAll('.status-btn').forEach(b => {
    b.dataset.origColor = b.style.color;
});
</script>
@endpush
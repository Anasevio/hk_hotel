@extends('layouts.ra')
@section('title', 'Absensi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ra/absensi_ra.css') }}">
@endpush

@section('content')

<a href="{{ route('ra.dashboard') }}" class="back-link">← Kembali</a>

{{-- Header --}}
<div class="abs-header">
    <div class="abs-header-title">Absensi</div>
    <div class="abs-header-sub">{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">⚠️ {{ session('error') }}</div>
@endif

<div class="abs-layout">

    {{-- Kolom Kiri: Form absensi --}}
    <div>
        <div class="card">
            <div class="abs-user-info">
                <div class="abs-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="abs-user-name">{{ auth()->user()->name }}</div>
                    <div class="abs-user-meta">{{ ucfirst(auth()->user()->role) }} · {{ auth()->user()->username }}</div>
                </div>
            </div>

            <hr class="abs-divider">

            @if(!$todayAtt)
                {{-- Belum absen --}}
                <div class="abs-form-label">Status Kehadiran</div>
                <form method="POST" action="{{ route('ra.attendance.checkin') }}" id="formAbsen">
                    @csrf
                    <input type="hidden" name="status" id="statusInput" value="">
                    <div class="abs-status-btns">
                        <button type="button" onclick="pilihStatus('hadir', this)"
                                class="status-btn status-hadir">✅ Hadir</button>
                        <button type="button" onclick="pilihStatus('izin', this)"
                                class="status-btn status-izin">📝 Izin</button>
                        <button type="button" onclick="pilihStatus('sakit', this)"
                                class="status-btn status-sakit">🏥 Sakit</button>
                    </div>
                    <div id="keteranganWrap" class="abs-keterangan" style="display:none">
                        <label class="abs-form-label">Keterangan</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Tulis keterangan..."></textarea>
                    </div>
                    <button type="submit" id="btnSubmit" disabled class="btn-absen btn-absen--disabled">
                        Pilih status dahulu
                    </button>
                </form>

            @elseif($todayAtt->status === 'hadir' && !$todayAtt->check_out)
                {{-- Hadir, belum absen keluar --}}
                <div class="abs-info-box abs-info-box--success">
                    <div class="abs-info-title">✅ Sudah Absen Masuk</div>
                    <div class="abs-info-val">Pukul <strong>{{ $todayAtt->check_in }}</strong></div>
                </div>
                <form method="POST" action="{{ route('ra.attendance.checkout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-block">🏠 Absen Keluar</button>
                </form>

            @else
                {{-- Absensi selesai --}}
                <div class="abs-info-box abs-info-box--success">
                    <div class="abs-info-title">✅ Absensi Selesai</div>
                    <div class="abs-info-rows">
                        <div class="abs-info-row"><span>Masuk</span><strong>{{ $todayAtt->check_in }}</strong></div>
                        <div class="abs-info-row"><span>Keluar</span><strong>{{ $todayAtt->check_out }}</strong></div>
                        <div class="abs-info-row"><span>Durasi</span><strong>{{ $todayAtt->work_duration ?? '-' }}</strong></div>
                        <div class="abs-info-row"><span>Status</span><strong>{{ ucfirst($todayAtt->status) }}</strong></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Stats bulan ini --}}
        <div class="abs-stats">
            <div class="abs-stat-item">
                <div class="abs-stat-icon">✅</div>
                <div class="abs-stat-val abs-stat-val--hadir">{{ $stats['hadir'] }}</div>
                <div class="abs-stat-label">Hadir</div>
            </div>
            <div class="abs-stat-item">
                <div class="abs-stat-icon">📝</div>
                <div class="abs-stat-val abs-stat-val--izin">{{ $stats['izin'] }}</div>
                <div class="abs-stat-label">Izin</div>
            </div>
            <div class="abs-stat-item">
                <div class="abs-stat-icon">🏥</div>
                <div class="abs-stat-val abs-stat-val--sakit">{{ $stats['sakit'] }}</div>
                <div class="abs-stat-label">Sakit</div>
            </div>
            <div class="abs-stat-item">
                <div class="abs-stat-icon">❌</div>
                <div class="abs-stat-val abs-stat-val--alfa">{{ $stats['alfa'] }}</div>
                <div class="abs-stat-label">Alpha</div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Riwayat --}}
    <div>
        <div class="abs-riwayat-title">Riwayat Absensi</div>
        @forelse($history->take(10) as $r)
        <div class="riwayat-item">
            <div class="riwayat-icon">📊</div>
            <div class="riwayat-info">
                <div class="riwayat-date">{{ $r->date->format('d F Y') }} · {{ $r->check_in ?? '-' }}</div>
                <div class="riwayat-label">
                    @if($r->status === 'hadir')     ↩ Absen Masuk
                    @elseif($r->status === 'izin')  📝 Izin
                    @elseif($r->status === 'sakit') 🏥 Sakit
                    @else                           ❌ Alpha
                    @endif
                </div>
            </div>
            <span class="riwayat-badge {{ $r->status === 'hadir' ? 'rb-masuk' : ($r->status === 'izin' ? 'rb-izin' : 'rb-sakit') }}">
                {{ ucfirst($r->status) }}
            </span>
        </div>
        @empty
        <div class="abs-empty">Belum ada riwayat</div>
        @endforelse
    </div>

</div>

@endsection

@push('scripts')
<script>
function pilihStatus(status, el) {
    document.querySelectorAll('.status-btn').forEach(b => b.classList.remove('is-active'));
    el.classList.add('is-active');
    document.getElementById('statusInput').value = status;
    document.getElementById('keteranganWrap').style.display =
        (status === 'izin' || status === 'sakit') ? 'block' : 'none';

    const btn = document.getElementById('btnSubmit');
    btn.disabled = false;
    btn.className = 'btn-absen btn-absen--active';
    btn.textContent = 'Absen Sekarang';
}
</script>
@endpush
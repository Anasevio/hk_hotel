@extends('layouts.topbar')
@section('title', 'Absensi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ra/absensi_ra.css') }}">
@endpush

@php $role = auth()->user()->role; @endphp

@section('content')

<a href="{{ route($role . '.dashboard') }}" class="back-link">← Kembali</a>

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

    {{-- KIRI --}}
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

                <div class="abs-form-label">Status Kehadiran</div>

                {{-- Pilih status dulu --}}
                <div class="abs-status-btns">
                    <button type="button" onclick="pilihStatus('hadir', this)"
                            class="status-btn status-hadir">✅ Hadir</button>

                    <button type="button" onclick="pilihStatus('izin', this)"
                            class="status-btn status-izin">📝 Izin</button>

                    <button type="button" onclick="pilihStatus('sakit', this)"
                            class="status-btn status-sakit">🏥 Sakit</button>
                </div>

                {{-- FORM 1: Hadir — action sudah fix dari awal --}}
                <form method="POST"
                      action="{{ route($role . '.attendance.checkin') }}"
                      id="formHadir"
                      style="display:none">
                    @csrf
                    <button type="submit" class="btn-absen btn-absen--active">
                        Absen Sekarang
                    </button>
                </form>

                {{-- FORM 2: Izin / Sakit — action sudah fix dari awal --}}
                <form method="POST"
                      action="{{ route($role . '.attendance.izin') }}"
                      id="formIzin"
                      style="display:none">
                    @csrf
                    <input type="hidden" name="status" id="statusIzinInput">

                    <div class="abs-keterangan">
                        <label class="abs-form-label">Keterangan</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Tulis keterangan..." required></textarea>
                    </div>

                    <button type="submit" class="btn-absen btn-absen--active">
                        Absen Sekarang
                    </button>
                </form>

                {{-- Placeholder button sebelum status dipilih --}}
                <button type="button" id="btnPlaceholder" disabled
                        class="btn-absen btn-absen--disabled">
                    Pilih status dahulu
                </button>

            @elseif(in_array($todayAtt->status, ['hadir','terlambat']) && !$todayAtt->check_out)

                <div class="abs-info-box abs-info-box--success">
                    <div class="abs-info-title">✅ Sudah Absen Masuk</div>
                    <div class="abs-info-val">
                        Pukul <strong>{{ $todayAtt->check_in?->format('H:i') }}</strong>
                    </div>
                    <div>
                        Status: <strong>{{ $todayAtt->status_label }}</strong>
                    </div>
                </div>

                <form method="POST" action="{{ route($role . '.attendance.checkout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-block">🏠 Absen Keluar</button>
                </form>

            @else

                <div class="abs-info-box abs-info-box--success">
                    <div class="abs-info-title">✅ Absensi Selesai</div>
                    <div class="abs-info-rows">
                        <div class="abs-info-row">
                            <span>Masuk</span>
                            <strong>{{ $todayAtt->check_in?->format('H:i') }}</strong>
                        </div>
                        <div class="abs-info-row">
                            <span>Keluar</span>
                            <strong>{{ $todayAtt->check_out?->format('H:i') }}</strong>
                        </div>
                        <div class="abs-info-row">
                            <span>Durasi</span>
                            <strong>{{ $todayAtt->work_duration ?? '-' }}</strong>
                        </div>
                        <div class="abs-info-row">
                            <span>Status</span>
                            <strong>{{ $todayAtt->status_label }}</strong>
                        </div>
                    </div>
                </div>

            @endif
        </div>

        {{-- STATS --}}
        <div class="abs-stats">
            <div class="abs-stat-item">
                <div class="abs-stat-icon">✅</div>
                <div class="abs-stat-val abs-stat-val--hadir">{{ $stats['hadir'] }}</div>
                <div class="abs-stat-label">Hadir</div>
            </div>
            <div class="abs-stat-item">
                <div class="abs-stat-icon">⏰</div>
                <div class="abs-stat-val">{{ $stats['terlambat'] ?? 0 }}</div>
                <div class="abs-stat-label">Terlambat</div>
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
                <div class="abs-stat-label">Alpa</div>
            </div>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div>
        <div class="abs-riwayat-title">Riwayat Absensi</div>

        @forelse($history->take(10) as $r)

        @php
            $badgeClass = match($r->status) {
                'hadir'     => 'rb-masuk',
                'terlambat' => 'rb-terlambat',
                'izin'      => 'rb-izin',
                'sakit'     => 'rb-sakit',
                'alfa'      => 'rb-alfa',
                default     => ''
            };
        @endphp

        <div class="riwayat-item">
            <div class="riwayat-icon">📊</div>

            <div class="riwayat-info">
                <div class="riwayat-date">
                    {{ $r->date->format('d F Y') }} · {{ $r->check_in?->format('H:i') ?? '-' }}
                </div>
                <div class="riwayat-label">
                    {{ $r->status_label }}
                </div>
            </div>

            <span class="riwayat-badge {{ $badgeClass }}">
                {{ $r->status_label }}
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
    // Highlight button yang dipilih
    document.querySelectorAll('.status-btn').forEach(b => b.classList.remove('is-active'));
    el.classList.add('is-active');

    // Sembunyikan placeholder button
    document.getElementById('btnPlaceholder').style.display = 'none';

    if (status === 'izin' || status === 'sakit') {
        // Tampilkan form izin/sakit, sembunyikan form hadir
        document.getElementById('formHadir').style.display = 'none';
        document.getElementById('formIzin').style.display  = 'block';
        document.getElementById('statusIzinInput').value   = status;
    } else {
        // Tampilkan form hadir, sembunyikan form izin/sakit
        document.getElementById('formIzin').style.display  = 'none';
        document.getElementById('formHadir').style.display = 'block';
    }
}
</script>
@endpush
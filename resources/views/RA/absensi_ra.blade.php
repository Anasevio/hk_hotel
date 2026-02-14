@extends('layouts.app')

@section('title', 'Absensi')

@section('content')

<link rel="stylesheet" href="{{ asset('css/absensi_ra.css') }}">

<div class="logo-area">
    <img src="{{ asset('images/Logo SIG.png') }}">
    <img src="{{ asset('images/LOGO PH.png') }}">
</div>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <h1>Absensi</h1>
        <p>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
    </div>

    <div class="content">

        <!-- LEFT -->
        <div class="left">

            <!-- STATUS -->
            <div class="card">
                <h3>Status Hari Ini</h3>
                <p class="date">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>

                <p>
                    <strong>Nama Petugas</strong><br>
                    {{ auth()->user()->name }}
                </p>

                <hr>

                <!-- FORM ABSENSI -->
                <form action="{{ route('ra.absensi.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="status" id="statusInput">

                    <h4>Detail Absensi</h4>
                <div class="status-btn">
                    <button type="button" class="btn hadir" onclick="pilihStatus(this, 'hadir')">Hadir</button>
                    <button type="button" class="btn izin" onclick="pilihStatus(this, 'izin')">Izin</button>
                    <button type="button" class="btn sakit" onclick="pilihStatus(this, 'sakit')">Sakit</button>
                </div>


                    <textarea name="catatan" placeholder="Catatan tambahan (opsional)"></textarea>

                    <button type="submit" class="send"><i data-feather="arrow-right"></i></button>
                </form>

                @if (session('success'))
                    <p style="color: green">{{ session('success') }}</p>
                @endif

                @if (session('error'))
                    <p style="color: red">{{ session('error') }}</p>
                @endif
            </div>

            <!-- SUMMARY -->
            <div class="summary">
                <div class="box">
                    <p>Hadir</p>
                    <strong>{{ $rekap['hadir'] ?? 0 }}</strong>
                </div>
                <div class="box">
                    <p>Izin</p>
                    <strong>{{ $rekap['izin'] ?? 0 }}</strong>
                </div>
                <div class="box">
                    <p>Sakit</p>
                    <strong>{{ $rekap['sakit'] ?? 0 }}</strong>
                </div>
                <div class="box">
                    <p>Alpha</p>
                    <strong>{{ $rekap['alpha'] ?? 0 }}</strong>
                </div>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="right">

            <!-- PROFILE -->
            <div class="profile card">
                <img src="https://i.imgur.com/0y0y0y0.png">
                <div>
                    <h4>{{ auth()->user()->name }}</h4>
                    <small>{{ auth()->user()->role }}</small>
                </div>
            </div>

            <!-- RIWAYAT -->
            <h3 class="riwayat-title">Riwayat Absensi</h3>

            @forelse ($riwayat ?? [] as $absen)
                <div class="riwayat-card {{ $absen->status }}-bg">
                    <p>
                        {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d F Y') }}
                        @if ($absen->jam_masuk)
                            - {{ $absen->jam_masuk }}
                        @endif
                    </p>
                    <button>{{ strtoupper($absen->status) }}</button>
                </div>
            @empty
                <p>Tidak ada riwayat absensi</p>
            @endforelse

        </div>

    </div>
</div>

<script>
function setStatus(value) {
    document.getElementById('statusInput').value = value;
}
</script>
    <script>
      feather.replace();
    </script>

<script>
function pilihStatus(button, value) {

    // set value ke input hidden
    document.getElementById('statusInput').value = value;

    // ambil semua tombol dalam status-btn
    const buttons = document.querySelectorAll('.status-btn .btn');

    // hapus active dari semua
    buttons.forEach(function(btn) {
        btn.classList.remove('active');
    });

    // aktifkan yang diklik saja
    button.classList.add('active');
}
</script>


    @endsection
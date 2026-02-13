<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi</title>
    <link rel="stylesheet" href="{{ asset('css/absensi_ra.css') }}">
</head>
<body>

        <div class="logo-area">
            <img src="{{ asset('images/Logo SIG.png') }}">
            <img src="{{ asset('images/LOGO PH.png') }}">
        </div>
        
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <h1>Absensi</h1>
        <p>Rabu, 4 Februari 2026</p>
    </div>

    <div class="content">

        <!-- LEFT -->
        <div class="left">

            <!-- STATUS -->
            <div class="card">
                <h3>Status Hari Ini</h3>
                <p class="date">Rabu, 14 Januari 2026</p>

                <p><strong>Nama Petugas</strong><br>Mahfud</p>
                <p><strong>Waktu Masuk</strong><br>07:19</p>

                <hr>

                <h4>Detail Absensi</h4>
                <div class="status-btn">
                    <button class="btn hadir">Hadir</button>
                    <button class="btn izin">Izin</button>
                    <button class="btn sakit">Sakit</button>
                </div>

                <textarea placeholder="Catatan tambahan (opsional)"></textarea>
                <button class="send">➤</button>
            </div>

            <!-- SUMMARY -->
            <div class="summary">
                <div class="box">
                    <span>✔</span>
                    <p>Hadir</p>
                    <strong>1</strong>
                </div>
                <div class="box">
                    <span>✖</span>
                    <p>Izin</p>
                    <strong>10000</strong>
                </div>
                <div class="box">
                    <span>☺</span>
                    <p>Sakit</p>
                    <strong>10000</strong>
                </div>
                <div class="box">
                    <span>☹</span>
                    <p>Alpha</p>
                    <strong>10000</strong>
                </div>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="right">

            <!-- PROFILE -->
            <div class="profile card">
                <img src="https://i.imgur.com/0y0y0y0.png" alt="avatar">
                <div>
                    <h4>Mahfud Anjayy</h4>
                    <small>Bagian IT</small>
                </div>
                <div class="stats">
                    <span>Hadir<br><b>28</b></span>
                    <span>Izin<br><b>8</b></span>
                    <span>Sakit<br><b>3</b></span>
                    <span>Alpha<br><b class="red">50</b></span>
                </div>
            </div>

            <!-- RIWAYAT -->
            <h3 class="riwayat-title">Riwayat Absensi</h3>

            <div class="riwayat-card hadir-bg">
                <p>Kamis, 14 Desember 2023 - 15:59</p>
                <button>→ Absen Masuk</button>
            </div>

            <div class="riwayat-card izin-bg">
                <p>Kamis, 14 Desember 2023 - 16:54</p>
                <button>← Izin</button>
            </div>

        </div>

    </div>
</div>

</body>
</html>
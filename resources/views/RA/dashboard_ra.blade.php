<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    <div class="dashboard-wrapper">

        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h1>Selamat Datang, Mahfud</h1>
                <p>Kelola Aktivitas Sekolahmu Hari Ini.</p>
            </div>
            <a href="{{ route('logout') }}" class="btn-logout">Logout</a>
        </div>

        <!-- Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <i data-feather="check-square"></i>
                <h3>Absensi</h3>
                <p>Catat Kehadiran Secara Online</p>
                <a href="{{ route('absensi') }}" class="btn-card">Lihat Absensi</a>
            </div>

            <div class="card">
                <i data-feather="clipboard"></i>
                <h3>Room</h3>
                <p>Lihat dan Kumpulkan Tugas</p>
                <a href="{{ route('room') }}" class="btn-card">Lihat Kamar</a>
            </div>

            <div class="card">
                <i data-feather="bell"></i>
                <h3>Riwayat</h3>
                <p>Riwayat Tugas</p>
                <a href="{{ route('riwayat') }}" class="btn-card">Lihat Aspirasi</a>
            </div>
        </div>
    </div>

    <script>
        feather.replace(); // inisialisasi Feather icons
    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Tugas</title>
    <link rel="stylesheet" href="{{ asset('css/history.css') }}">
</head>
<body>

<div class="container">

    <div class="card">
        <div class="header">
            <a href="{{ auth()->user()->role == 'manager' ? route('manager.dashboard') : route('siswa.dashboard') }}" class="btn-back">← Kembali</a>

            <h1>Riwayat Tugas</h1>
            <span class="badge">Terakhir 30 hari</span>
        </div>

        <div class="content">

            <!-- FILTER -->
            <div class="left">
                <div class="filter-box">
                    <h3>Filter</h3>

                    <form method="GET">
    <div class="filter-box">
        <h3>Filter</h3>

        <label>Tanggal</label>
        <input type="date" name="tanggal" value="{{ request('tanggal') }}">

        <label>Status</label>
        <select name="status">
            <option value="">Semua Status</option>
            <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="proses" {{ request('status')=='proses' ? 'selected' : '' }}>Proses</option>
        </select>

        <label>Hasil</label>
        <select name="hasil">
            <option value="">Semua Hasil</option>
            <option value="100" {{ request('hasil')=='100' ? 'selected' : '' }}>100%</option>
            <option value="50" {{ request('hasil')=='50' ? 'selected' : '' }}>50%</option>
        </select>

        <input type="text" name="search" placeholder="Cari kamar..." value="{{ request('search') }}">

        <button type="submit" class="btn-search">🔍 Cari</button>
    </div>
</form>
                </div>

                <div class="warning-box">
                    <h4>⚠ Peringatan</h4>
                    <div class="warning-content">
                        TUGAS JANGAN TERLALU SERING TELAT DEMI
                        KENYAMANAN KONSUMEN
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="right">
               <div class="table-wrapper">
    <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama RA</th>
                            <th>SuperVisor</th>
                            <th>Kamar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($history as $task)
<tr>
    <!-- Tanggal --> 
    <td>
        {{ $task->created_at->format('d M Y') }}
    <!-- RA -->
    <td>
        {{ $task->assignedUser->name ?? '-' }}
    </td>

    <!-- Supervisor -->
    <td>
        {{ $task->supervisor->name ?? '-' }}
    </td>

    <!-- Kamar -->
    <td>
        {{ $task->room->room_number ?? '-' }}
    </td>

    <!-- Status -->
    <td>
        <span class="status">
            {{ $task->status_label }}
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" style="text-align:center;">
        Tidak ada data
    </td>
</tr>
@endforelse
</tbody>
                </table>
            </div>
                @if ($history->count())
                    <div class="pagination-info">
                        Menampilkan {{ $history->firstItem() }} - {{ $history->lastItem() }}
                        dari {{ $history->total() }} data
                    </div>
                @endif
                <div class="pagination">
                 {{ $history->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

</div>
</body>
</html>
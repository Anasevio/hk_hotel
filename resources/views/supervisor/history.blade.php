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
            <a href="{{ url()->previous() }}" class="btn-back">← Kembali</a>

            <h1>Riwayat Tugas</h1>
            <span class="badge">Terakhir 30 hari</span>
        </div>

        <div class="content">

            <!-- FILTER -->
            <div class="left">
                <div class="filter-box">
                    <h3>Filter</h3>

                    <label>Tanggal</label>
                    <select>
                        <option>Semua Tanggal</option>
                    </select>

                    <label>Status</label>
                    <select>
                        <option>Semua Status</option>
                    </select>

                    <label>Hasil</label>
                    <select>
                        <option>Semua Hasil</option>
                    </select>

                    <input type="text" placeholder="Cari...">
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
                            <th>Nama RA</th>
                            <th>Kamar</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $task)
<tr>
    <!-- Nama RA -->
    <td>
        {{ $task->assignedUser->name ?? '-' }}
    </td>

    <!-- Kamar -->
    <td>
        {{ $task->room->room_number ?? '-' }}
    </td>

    <!-- Progress -->
    <td>
        {{ $task->overall_progress }}%
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
    <td colspan="4" style="text-align:center;">
        Tidak ada data
    </td>
</tr>
@endforelse
                    </tbody>  
                </table>
            </div>

                <div class="pagination">
                 {{ $history->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

</div>
</body>
</html>
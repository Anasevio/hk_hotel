<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Riwayat Tugas</title>
<link rel="stylesheet" href="{{ asset('css/admin/history_admin.css') }}">
</head>

<body>

<!-- TOPBAR -->
<header class="topbar">
  <div class="logo">
    <h2>HK Perhotelan</h2>
    <span>Admin Panel</span>
  </div>

  <div class="user">
    <span>Admin</span>
    <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="logout">Logout</button>
</form>
  </div>
</header>

<!-- CONTAINER -->
<div class="container">

  <!-- HEADER CARD -->
  <div class="header-card">
    <h2>Riwayat Tugas Murid</h2>
    <p>Lihat riwayat aktivitas dan tugas Murid</p>
  </div>

  <!-- FILTER -->
  <div class="filter-box">
    <input type="date">
    <select>
      <option>Semua Murid</option>
    </select>
    <select>
      <option>Semua Tugas</option>
    </select>
    <button class="reset">Reset</button>
    <button class="cari">Cari</button>
  </div>

  <!-- TABLE -->
  <div class="table-box">
    <table>
      <thead>
        <tr>
          <th>Nama Staff</th>
          <th>Tanggal</th>
          <th>Tugas</th>
          <th>Status</th>
          <th>Waktu</th>
        </tr>
      </thead>

      <tbody>
@forelse($history as $task)
<tr>

    <!-- 👷 RA -->
    <td>{{ $task->assignedUser->name ?? '-' }}</td>

    <!-- 📅 Tanggal -->
    <td>{{ $task->updated_at->format('d M Y') }}</td>

    <!-- 🏨 Tugas -->
    <td>Pembersihan Kamar {{ $task->room->room_number ?? '-' }}</td>

    <!-- 📊 Status -->
    <td>
        <span class="status selesai">
            {{ $task->status_label }}
        </span>
    </td>

    <!-- ⏰ Waktu -->
    <td>{{ $task->updated_at->format('H:i') }}</td>

</tr>

<!-- 🔥 TAMBAHAN INFO (SPV & Manager) -->
<tr>
    <td colspan="5" style="font-size:12px; color:gray;">
        SPV: {{ $task->supervisor->name ?? '-' }} |
        Manager: {{ $task->manager->name ?? '-' }}
    </td>
</tr>

@empty
<tr>
    <td colspan="5" style="text-align:center;">Tidak ada data</td>
</tr>
@endforelse
</tbody>
    </table>
  </div>

</div>

</body>
</html>
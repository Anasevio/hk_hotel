@extends('layouts.admin')
@section('title', 'Riwayat Tugas')
@section('page-title', 'Riwayat Tugas')
@section('page-subtitle', 'Lihat riwayat aktivitas dan tugas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/history_admin.css') }}">
@endpush

@section('content')
<!-- CONTAINER -->
<div class="container">

  <!-- HEADER CARD -->
  <div class="header-card" style="display:flex; justify-content:space-between; align-items:center;">

  <div>
    <h2>Riwayat Tugas Murid</h2>
    <p>Lihat riwayat aktivitas dan tugas Murid</p>
  </div>

  <a href="{{ route('admin.dashboard') }}" class="btn-back">
  ← Kembali
</a>

</div>

  <!-- FILTER -->
  <form method="GET" class="filter-box">

    <input type="date" name="tanggal" value="{{ request('tanggal') }}">

    <select name="user">
        <option value="">Semua Murid</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
        @endforeach
    </select>

    <select name="status">
        <option value="">Semua Tugas</option>
        <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
        <option value="proses" {{ request('status')=='proses' ? 'selected' : '' }}>Proses</option>
    </select>

    <!-- RESET -->
    <a href="{{ route('admin.history.index') }}" class="reset">
        Reset
    </a>

    <!-- CARI -->
    <button type="submit" class="cari">
        🔍 Cari
    </button>

</form>

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

        @php
            $durasi = ($task->started_at && $task->completed_at)
                ? \Carbon\Carbon::parse($task->started_at)->diffInMinutes($task->completed_at)
                : null;
        @endphp

        ⏱ {{ $durasi ?? '-' }} menit 
        | SPV: {{ $task->supervisor->name ?? '-' }} 
        | Manager: {{ $task->manager->name ?? '-' }}

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
@endsection
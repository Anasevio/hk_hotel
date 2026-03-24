@extends('layouts.admin')
@section('title','Absensi')
@section('page-title','Rekap Absensi')

@section('content')
<div class="card" style="margin-bottom:20px">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        <div class="form-group" style="margin:0">
            <label class="form-label">Filter Bulan</label>
            <input type="month" name="month" value="{{ $month }}" class="form-control" style="width:180px">
        </div>
        <button type="submit" class="btn-sm btn-primary">Filter</button>
        <a href="{{ route('admin.attendance.export', ['month' => $month]) }}" class="btn-sm btn-secondary">⬇ Export CSV</a>
    </form>
</div>

{{-- RINGKASAN --}}
<div style="overflow-x:auto;margin-bottom:20px">
<table class="tbl">
    <thead><tr><th>Nama</th><th>Role</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alfa</th></tr></thead>
    <tbody>
    @foreach($summary as $s)
    <tr>
        <td><strong>{{ $s['user']->name }}</strong></td>
        <td><span class="badge badge-red">{{ ucfirst($s['user']->role) }}</span></td>
        <td><span class="badge badge-green">{{ $s['hadir'] }}</span></td>
        <td><span class="badge badge-yellow">{{ $s['izin'] }}</span></td>
        <td><span class="badge badge-blue">{{ $s['sakit'] }}</span></td>
        <td><span class="badge badge-gray">{{ $s['alfa'] }}</span></td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>

{{-- DETAIL RECORDS --}}
<div class="card">
    <div class="card-header"><span class="card-title">Detail Absensi — {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</span></div>
    <div style="overflow-x:auto">
    <table class="tbl">
        <thead><tr><th>Tanggal</th><th>Nama</th><th>Role</th><th>Masuk</th><th>Keluar</th><th>Durasi</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($records as $r)
        <tr>
            <td>{{ $r->date->format('d/m/Y') }}</td>
            <td>{{ $r->user->name }}</td>
            <td><span class="badge badge-red" style="font-size:10px">{{ ucfirst($r->user->role) }}</span></td>
            <td>{{ $r->check_in ?? '-' }}</td>
            <td>{{ $r->check_out ?? '-' }}</td>
            <td>{{ $r->work_duration ?? '-' }}</td>
            <td><span class="badge {{ $r->status === 'hadir' ? 'badge-green' : ($r->status === 'alfa' ? 'badge-red' : 'badge-yellow') }}">{{ ucfirst($r->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#999;padding:24px">Tidak ada data</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
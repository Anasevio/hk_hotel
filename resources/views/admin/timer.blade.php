@extends('layouts.admin')
@section('title','Pengaturan Timer')
@section('page-title','Pengaturan Timer')
@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('admin.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('admin.users.index') }}"><i data-feather="users"></i> Kelola Akun</a>
<a href="{{ route('admin.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('admin.timer.index') }}" class="active"><i data-feather="clock"></i> Timer Tugas</a>
<a href="{{ route('admin.history.index') }}"><i data-feather="activity"></i> Log Aktivitas</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Durasi Pengerjaan per Jenis Kamar</span>
    </div>
    <p style="font-size:13px;color:#888;margin-bottom:20px">Timer ini akan digunakan sebagai batas waktu RA saat mengerjakan tugas pembersihan.</p>

    @foreach($settings as $s)
    <div style="display:flex;align-items:center;gap:16px;padding:16px;background:#fafafa;border-radius:12px;margin-bottom:12px">
        <div style="flex:1">
            <div style="font-weight:600;font-size:14px">{{ $s->label }}</div>
            <div style="font-size:12px;color:#888">Key: <code>{{ $s->key }}</code>
                @if($s->updatedByUser) · Diubah oleh {{ $s->updatedByUser->name }} @endif
            </div>
        </div>
        <form method="POST" action="{{ route('admin.timer.update', $s->key) }}" style="display:flex;align-items:center;gap:8px">
            @csrf @method('PUT')
            <input type="number" name="duration_minutes" value="{{ $s->duration_minutes }}"
                min="5" max="180" class="form-control" style="width:90px">
            <span style="font-size:13px;color:#666">menit</span>
            <button type="submit" class="btn-sm btn-primary">Simpan</button>
        </form>
    </div>
    @endforeach
</div>
@endsection

@extends('layouts.admin')
@section('title','Absensi')
@section('page-title','Rekap Absensi')
@section('sidebar-nav')
<a href="{{ route('supervisor.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('supervisor.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('supervisor.tasks.index') }}"><i data-feather="clipboard"></i> Kelola Tugas</a>
<a href="{{ route('supervisor.special-cases.index') }}"><i data-feather="alert-circle"></i> Special Case</a>
<a href="{{ route('supervisor.attendance.index') }}" class="active"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('supervisor.history.index') }}"><i data-feather="activity"></i> Histori</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header"><span class="card-title">Absensi Hari Ini</span>
        <a href="{{ route('admin.attendance.index') }}" class="btn-sm btn-secondary">Rekap Lengkap (Admin)</a>
    </div>
    <p style="font-size:13px;color:#888">Untuk rekap dan export, gunakan panel Admin.</p>
    @if(isset($summary))
    @foreach($summary as $s)
    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #fafafa">
        <div><div style="font-weight:600;font-size:13px">{{ $s['user']->name }}</div>
            <div style="font-size:11px;color:#888">{{ ucfirst($s['user']->role) }} · Shift {{ ucfirst($s['user']->shift) }}</div></div>
        <span class="badge {{ $s['hadir'] > 0 ? 'badge-green' : 'badge-gray' }}">{{ $s['hadir'] }} hadir</span>
    </div>
    @endforeach
    @endif
</div>
@endsection

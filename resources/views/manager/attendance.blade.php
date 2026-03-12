@extends('layouts.admin')
@section('title', 'Absensi')
@section('page-title', 'Rekap Absensi')
@section('sidebar-nav')
<a href="{{ route('manager.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('manager.rooms.index') }}" class="{{ request()->routeIs('manager.rooms*') ? 'active' : '' }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('manager.inspections.index') }}"><i data-feather="check-circle"></i> Final Inspeksi</a>
<a href="{{ route('manager.attendance.index') }}" class="{{ request()->routeIs('manager.attendance*') ? 'active' : '' }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('manager.history.index') }}" class="{{ request()->routeIs('manager.history*') ? 'active' : '' }}"><i data-feather="activity"></i> Histori</a>
@endsection
@section('content')
@include('admin.attendance')
@endsection

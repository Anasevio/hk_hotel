@extends('layouts.admin')
@section('title','Log Aktivitas')
@section('page-title','Log Aktivitas')

@section('content')
<div class="card">
    <div class="card-header"><span class="card-title">Log Perubahan Status Kamar</span></div>
    <div style="overflow-x:auto">
    <table class="tbl">
        <thead><tr><th>Waktu</th><th>Kamar</th><th>Oleh</th><th>Dari</th><th>Ke</th><th>Alasan</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
        <tr>
            <td style="white-space:nowrap">{{ $log->created_at->format('d/m H:i') }}</td>
            <td><strong>{{ $log->room->room_number }}</strong></td>
            <td>{{ $log->changedByUser->name ?? '-' }}</td>
            <td><span class="badge badge-red" style="font-size:10px">{{ $log->from_status_label }}</span></td>
            <td><span class="badge badge-green" style="font-size:10px">{{ $log->to_status_label }}</span></td>
            <td style="color:#888">{{ $log->reason ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#999;padding:24px">Belum ada log</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:16px">{{ $logs->links() }}</div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Riwayat Tugas</span></div>
    <div style="overflow-x:auto">
    <table class="tbl">
        <thead><tr><th>Kamar</th><th>RA</th><th>Supervisor</th><th>Status</th><th>Durasi</th><th>Selesai</th></tr></thead>
        <tbody>
        @forelse($tasks as $t)
        <tr>
            <td><strong>{{ $t->room->room_number }}</strong></td>
            <td>{{ $t->assignedUser->name ?? '-' }}</td>
            <td>{{ $t->assignedByUser->name ?? '-' }}</td>
            <td><span class="badge {{ $t->status === 'completed' ? 'badge-green' : 'badge-yellow' }}" style="font-size:10px">{{ $t->status_label }}</span></td>
            <td>{{ $t->duration_minutes ? $t->duration_minutes . ' mnt' : '-' }}</td>
            <td>{{ $t->completed_at ? $t->completed_at->format('d/m H:i') : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#999;padding:24px">Belum ada data</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:16px">{{ $tasks->links() }}</div>
</div>
@endsection
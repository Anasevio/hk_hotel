@extends('layouts.ra')
@section('title','Histori')
@section('page-title','Histori Tugas')
@section('content')
<div class="card">
    <div class="card-header"><span class="card-title">Riwayat Tugas Saya</span></div>
    <div style="overflow-x:auto">
    <table class="tbl">
        <thead><tr><th>Kamar</th><th>Status</th><th>Mulai</th><th>Selesai</th><th>Durasi</th></tr></thead>
        <tbody>
        @forelse($tasks as $t)
        <tr>
            <td><strong>{{ $t->room->room_number }}</strong></td>
            <td><span class="badge {{ $t->status === 'completed' ? 'badge-green' : 'badge-yellow' }}" style="font-size:10px">{{ $t->status_label }}</span></td>
            <td>{{ $t->started_at?->format('d/m H:i') ?? '-' }}</td>
            <td>{{ $t->completed_at?->format('d/m H:i') ?? '-' }}</td>
            <td>{{ $t->duration_minutes ? $t->duration_minutes.' mnt' : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#999;padding:24px">Belum ada tugas selesai</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:16px">{{ $tasks->links() }}</div>
</div>
@endsection

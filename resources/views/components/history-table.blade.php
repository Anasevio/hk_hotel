<table border="1">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kamar</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
    @forelse($history as $task)
        <tr>
            <td>{{ $task->updated_at->format('d M Y') }}</td>
            <td>{{ $task->room->room_number ?? '-' }}</td>
            <td>{{ $task->status }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3">Tidak ada data</td>
        </tr>
    @endforelse
    </tbody>
</table>
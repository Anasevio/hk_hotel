<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number', 'room_type', 'status', 'assigned_to', 'floor', 'notes'
    ];

    // ── Relasi ────────────────────────────────────────────

    // RA yang sedang ditugaskan ke kamar ini
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Semua tugas kamar ini (riwayat lengkap)
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Tugas aktif (yang sedang berjalan, bukan completed)
    public function activeTask()
    {
        return $this->hasOne(Task::class)
            ->whereNotIn('status', ['completed'])
            ->latest();
    }

    // Log perubahan status kamar ini
    public function statusLogs()
    {
        return $this->hasMany(RoomStatusLog::class)->latest();
    }

    // Special case aktif untuk kamar ini
    public function activeSpecialCase()
    {
        return $this->hasOne(SpecialCase::class)
            ->whereIn('status', ['open', 'in_progress'])
            ->latest();
    }

    // ── Helper Methods ────────────────────────────────────

    // Label status yang readable
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'vacant_dirty'       => 'Vacant Dirty',
            'vacant_clean'       => 'Vacant Clean',
            'vacant_ready'       => 'Vacant Ready',
            'occupied'           => 'Occupied',
            'expected_departure' => 'Expected Departure',
            default              => $this->status,
        };
    }

    // Warna badge status (untuk UI)
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'vacant_dirty'       => 'red',
            'vacant_clean'       => 'blue',
            'vacant_ready'       => 'green',
            'occupied'           => 'yellow',
            'expected_departure' => 'orange',
            default              => 'gray',
        };
    }

    // Singkatan status (untuk tampil di kotak kamar)
    public function getStatusShortAttribute(): string
    {
        return match ($this->status) {
            'vacant_dirty'       => 'VD',
            'vacant_clean'       => 'VC',
            'vacant_ready'       => 'VR',
            'occupied'           => 'OC',
            'expected_departure' => 'ED',
            default              => '??',
        };
    }

    // Apakah kamar perlu dibersihkan
    public function needsCleaning(): bool
    {
        return in_array($this->status, ['vacant_dirty', 'expected_departure']);
    }

    // Ubah status + catat di log secara otomatis
    public function changeStatus(string $newStatus, User $changedBy, string $reason = null, Task $task = null): void
    {
        $oldStatus = $this->status;

        $this->update(['status' => $newStatus]);

        RoomStatusLog::create([
            'room_id'     => $this->id,
            'changed_by'  => $changedBy->id,
            'from_status' => $oldStatus,
            'to_status'   => $newStatus,
            'reason'      => $reason,
            'task_id'     => $task?->id,
        ]);
    }
}

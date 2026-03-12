<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'room_id', 'assigned_to', 'assigned_by', 'status',
        'started_at', 'submitted_at', 'supervisor_approved_at', 'completed_at',
        'time_limit', 'supervisor_note', 'manager_note',
        'checklist1_progress', 'checklist2_progress'
    ];

    protected $casts = [
        'started_at'             => 'datetime',
        'submitted_at'           => 'datetime',
        'supervisor_approved_at' => 'datetime',
        'completed_at'           => 'datetime',
    ];

    // ── Relasi ────────────────────────────────────────────

    // Kamar yang dikerjakan
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // RA yang mengerjakan
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Supervisor yang memberikan tugas
    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Semua item checklist tugas ini
    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('order');
    }

    // Hanya checklist persiapan (type = preparation)
    public function preparationChecklists()
    {
        return $this->hasMany(TaskChecklist::class)
            ->where('type', 'preparation')
            ->orderBy('order');
    }

    // Hanya checklist pembersihan (type = cleaning)
    public function cleaningChecklists()
    {
        return $this->hasMany(TaskChecklist::class)
            ->where('type', 'cleaning')
            ->orderBy('order');
    }

    // ── Helper Methods ────────────────────────────────────

    // Label status yang readable
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'                  => 'Menunggu Dimulai',
            'in_progress'              => 'Sedang Dikerjakan',
            'pending_supervisor'       => 'Menunggu Cek Supervisor',
            'returned_to_ra'           => 'Dikembalikan ke RA',
            'pending_manager'          => 'Menunggu Approve Manager',
            'returned_to_supervisor'   => 'Dikembalikan ke Supervisor',
            'completed'                => 'Selesai',
            default                    => $this->status,
        };
    }

    // Hitung durasi pengerjaan dalam menit
    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->started_at || !$this->submitted_at) return null;
        return (int) $this->started_at->diffInMinutes($this->submitted_at);
    }

    // Apakah melewati batas waktu
    public function isOvertime(): bool
    {
        if (!$this->started_at) return false;
        $elapsed = now()->diffInMinutes($this->started_at);
        return $elapsed > $this->time_limit;
    }

    // Progress keseluruhan (rata-rata checklist 1 & 2)
    public function getOverallProgressAttribute(): int
    {
        return (int) (($this->checklist1_progress + $this->checklist2_progress) / 2);
    }
}

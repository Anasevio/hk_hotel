<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'room_id', 'assigned_to', 'assigned_by', 'status',
        'started_at', 'submitted_at', 'supervisor_approved_at', 'completed_at',
        'time_limit', 'supervisor_note', 'manager_note', 'ra_notes',
        'checklist1_progress', 'checklist2_progress',
        'sop_viewed_at'
    ];

    protected $casts = [
        'started_at'             => 'datetime',
        'submitted_at'           => 'datetime',
        'supervisor_approved_at' => 'datetime',
        'completed_at'           => 'datetime',
        'sop_viewed_at' => 'datetime',
    ];

    // ── Relasi ────────────────────────────────────────────────────

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('order');
    }

    public function preparationChecklists()
    {
        return $this->hasMany(TaskChecklist::class)
            ->where('type', 'preparation')
            ->orderBy('order');
    }

    public function cleaningChecklists()
    {
        return $this->hasMany(TaskChecklist::class)
            ->where('type', 'cleaning')
            ->orderBy('order');
    }

    public function isSopDone(): bool
{
    return !is_null($this->sop_viewed_at);
}

    // ── Accessors ─────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'                => 'Menunggu Dimulai',
            'in_progress'            => 'Sedang Dikerjakan',
            'pending_supervisor'     => 'Menunggu Cek Supervisor',
            'returned_to_ra'         => 'Dikembalikan ke RA',
            'pending_manager'        => 'Menunggu Approve Manager',
            'returned_to_supervisor' => 'Dikembalikan ke Supervisor',
            'completed'              => 'Selesai',
            default                  => $this->status,
        };
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->started_at || !$this->submitted_at) return null;
        return (int) $this->started_at->diffInMinutes($this->submitted_at);
    }

    public function getOverallProgressAttribute(): int
    {
        return (int) (($this->checklist1_progress + $this->checklist2_progress) / 2);
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isOvertime(): bool
    {
        if (!$this->started_at) return false;
        return now()->diffInMinutes($this->started_at) > $this->time_limit;
    }

    // Apakah task bisa distart ulang oleh RA
    public function canBeStartedBy(User $user): bool
    {
        return $this->assigned_to === $user->id
            && in_array($this->status, ['pending', 'returned_to_ra']);
    }

    // Apakah task sedang dikerjakan oleh RA ini
    public function isInProgressBy(User $user): bool
    {
        return $this->assigned_to === $user->id
            && $this->status === 'in_progress';
    }
        public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
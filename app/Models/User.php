<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'password', 'role', 'shift', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'  => 'hashed',
        'is_active' => 'boolean',
    ];

    // Login pakai username, bukan email

    // ── Relasi ────────────────────────────────────────────

    public function assignedRooms()
    {
        return $this->hasMany(Room::class, 'assigned_to');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->whereDate('date', today());
    }

    public function roomStatusLogs()
    {
        return $this->hasMany(RoomStatusLog::class, 'changed_by');
    }

    public function specialCases()
    {
        return $this->hasMany(SpecialCase::class, 'created_by');
    }

    // ── Helper ────────────────────────────────────────────

    public function hasCheckedInToday(): bool
    {
        return $this->attendances()->whereDate('date', today())->exists();
    }

    public function getShiftLabelAttribute(): string
    {
        return ucfirst($this->shift ?? 'pagi');
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 2));
    }
}

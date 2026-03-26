<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'late_minutes'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
    ];

    public const STATUS_HADIR = 'hadir';
    public const STATUS_TERLAMBAT = 'terlambat';
    public const STATUS_IZIN = 'izin';
    public const STATUS_SAKIT = 'sakit';
    public const STATUS_ALFA = 'alfa';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWorkDurationAttribute(): ?string
    {
        if (!$this->check_in || !$this->check_out) return null;

        $diff = $this->check_in->diff($this->check_out);

        return $diff->format('%H jam %I menit');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_HADIR => 'Hadir',
            self::STATUS_TERLAMBAT => 'Terlambat',
            self::STATUS_IZIN => 'Izin',
            self::STATUS_SAKIT => 'Sakit',
            self::STATUS_ALFA => 'Alpa',
            default => ucfirst($this->status),
        };
    }
}

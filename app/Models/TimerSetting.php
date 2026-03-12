<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimerSetting extends Model
{
    protected $fillable = ['key', 'label', 'duration_minutes', 'updated_by'];

    // User yang terakhir mengubah setting ini
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper: ambil durasi berdasarkan status kamar
    public static function getDuration(string $roomStatus): int
    {
        $setting = static::where('key', $roomStatus)->first()
                ?? static::where('key', 'default')->first();
        return $setting?->duration_minutes ?? 45;
    }
}

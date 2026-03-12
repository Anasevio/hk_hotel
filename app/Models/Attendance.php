<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'date', 'check_in', 'check_out', 'status', 'notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // User yang absen
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Hitung durasi kerja dalam jam (jika sudah check out)
    public function getWorkDurationAttribute(): ?string
    {
        if (!$this->check_in || !$this->check_out) return null;
        $in  = \Carbon\Carbon::parse($this->check_in);
        $out = \Carbon\Carbon::parse($this->check_out);
        $diff = $in->diff($out);
        return $diff->format('%H jam %I menit');
    }
}

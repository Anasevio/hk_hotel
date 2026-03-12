<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialCase extends Model
{
    protected $fillable = [
        'room_id', 'created_by', 'assigned_to', 'type',
        'description', 'priority', 'status', 'resolution_notes', 'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Kamar terkait
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Supervisor yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // RA yang ditugaskan
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Label tipe yang readable
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'vip_room'       => 'Kamar VIP',
            'do_not_disturb' => 'Do Not Disturb',
            'guest_sick'     => 'Tamu Sakit',
            'damage_report'  => 'Laporan Kerusakan',
            'lost_found'     => 'Barang Hilang/Ditemukan',
            'other'          => 'Lainnya',
            default          => $this->type,
        };
    }
}

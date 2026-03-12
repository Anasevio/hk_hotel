<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomStatusLog extends Model
{
    protected $fillable = [
        'room_id', 'changed_by', 'from_status', 'to_status', 'reason', 'task_id'
    ];

    // Kamar yang diubah
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // User yang mengubah
    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Relasi ke task (jika ada)
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Label status yang readable
    public function getFromStatusLabelAttribute(): string
    {
        return (new Room(['status' => $this->from_status]))->status_label;
    }

    public function getToStatusLabelAttribute(): string
    {
        return (new Room(['status' => $this->to_status]))->status_label;
    }
}

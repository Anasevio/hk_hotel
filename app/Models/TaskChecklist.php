<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    protected $fillable = [
        'task_id', 'type', 'item_name', 'order',
        'is_checked', 'checked_at', 'estimated_minutes'
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'checked_at' => 'datetime',
    ];

    // Tugas yang memiliki checklist ini
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}

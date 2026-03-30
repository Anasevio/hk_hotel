<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\{Room, Task, User};
use Illuminate\Http\Request;

class TaskController extends Controller
{
public function index()
{
    // 🔥 Task yang menunggu approval SPV
    $pendingTasks = Task::where('status', 'pending_supervisor')
        ->with(['room','assignedUser'])
        ->latest('submitted_at')
        ->get();

    // 🔍 Task yang sedang dikerjakan (monitoring)
    $activeTasks = Task::whereIn('status', [
            'pending',
            'in_progress',
            'returned_to_ra'
        ])
        ->with(['room','assignedUser'])
        ->latest()
        ->get();

    // 📊 Statistik hari ini
    $approvedToday = Task::where('status', 'pending_manager')
        ->whereDate('supervisor_approved_at', today())
        ->count();

    return view('supervisor.tasks', compact(
        'pendingTasks',
        'activeTasks',
        'approvedToday'
    ));
}
}
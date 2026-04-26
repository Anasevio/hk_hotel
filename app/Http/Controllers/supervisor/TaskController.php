<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * 📋 List semua task untuk supervisor
     */
    public function index()
    {
        // Task menunggu approval supervisor
        $pendingTasks = Task::where('status', 'pending_supervisor')
            ->with(['room', 'assignedUser'])
            ->latest('submitted_at')
            ->get();

        // Task aktif (monitoring)
        $activeTasks = Task::whereIn('status', [
                'pending',
                'in_progress',
                'returned_to_ra'
            ])
            ->with(['room', 'assignedUser'])
            ->latest()
            ->get();

        // Statistik hari ini
        $approvedToday = Task::where('status', 'pending_manager')
            ->whereDate('supervisor_approved_at', today())
            ->count();

        return view('supervisor.tasks', compact(
            'pendingTasks',
            'activeTasks',
            'approvedToday'
        ));
    }

    /**
     * 🔍 Detail task
     */
    public function show(Task $task)
{
    $task->load([
        'room',
        'assignedUser',
        'preparationChecklists',
        'cleaningChecklists'
    ]);

    // DEBUG (coba ini dulu)
    // dd($task->checklists->where('is_checked', true)->count());

    return view('supervisor.task_detail', compact('task'));
}

    /**
     * ✅ Approve task → kirim ke Manager
     */
    public function approve(Request $request, Task $task)
    {
        // Validasi optional note
        $request->validate([
            'note' => 'nullable|string|max:1000'
        ]);

        if ($task->status !== 'pending_supervisor') {
            return back()->with('error', 'Task tidak bisa di-approve.');
        }

        $task->update([
            'status' => 'pending_manager',
            'supervisor_note' => $request->note,
            'supervisor_approved_at' => now(),
        ]);

        return redirect()
            ->route('supervisor.tasks.index')
            ->with('success', 'Task berhasil di-approve dan dikirim ke Manager.');
    }

    /**
     * ↩ Return task ke RA
     */
    public function returnToRa(Request $request, Task $task)
    {
        $request->validate([
            'note' => 'required|string|max:1000'
        ]);

        if ($task->status !== 'pending_supervisor') {
            return back()->with('error', 'Task tidak bisa dikembalikan.');
        }

        $task->update([
            'status' => 'returned_to_ra',
            'supervisor_note' => $request->note,
        ]);

        return redirect()
            ->route('supervisor.tasks.index')
            ->with('success', 'Task berhasil dikembalikan ke RA.');
    }
}
<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\{Room, Task, User};
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Daftar tugas yang menunggu review supervisor (pending_supervisor)
     * + tugas aktif lainnya untuk monitoring
     */
    public function index()
    {
        // Tugas yang butuh diinspeksi supervisor
        $pendingTasks = Task::where('status', 'pending_supervisor')
            ->with(['room', 'assignedUser'])
            ->latest('submitted_at')
            ->get();

        // Tugas yang sedang dikerjakan RA (monitoring)
        $activeTasks = Task::whereIn('status', ['pending', 'in_progress', 'returned_to_ra'])
            ->with(['room', 'assignedUser'])
            ->latest()
            ->get();

        // Tugas yang sudah di-approve hari ini
        $approvedToday = Task::where('status', 'pending_manager')
            ->whereDate('supervisor_approved_at', today())
            ->count();

        return view('supervisor.tasks', compact('pendingTasks', 'activeTasks', 'approvedToday'));
    }

    /**
     * Detail tugas — supervisor inspeksi checklist RA
     */
    public function show(Task $task)
    {
        $task->load([
            'room',
            'assignedUser',
            'preparationChecklists',
            'cleaningChecklists',
        ]);

        return view('supervisor.task-detail', compact('task'));
    }

    /**
     * Supervisor approve → status task jadi pending_manager
     * Status kamar tetap vacant_clean sampai manager approve
     */
    public function approve(Request $request, Task $task)
    {
        abort_if($task->status !== 'pending_supervisor', 422);

        $task->update([
            'status'                 => 'pending_manager',
            'supervisor_approved_at' => now(),
            'supervisor_note'        => $request->note,
        ]);

        // Status kamar tetap vacant_clean (tidak berubah saat supervisor approve)

        return back()->with('success',
            "Tugas kamar {$task->room->room_number} diteruskan ke Manager."
        );
    }

    /**
     * Supervisor kembalikan ke RA — ada yang kurang
     */
    public function returnToRa(Request $request, Task $task)
    {
        abort_if($task->status !== 'pending_supervisor', 422);

        $request->validate([
            'note' => 'required|string|max:500',
        ], [
            'note.required' => 'Tulis catatan untuk RA sebelum mengembalikan tugas.',
        ]);

        $task->update([
            'status'        => 'returned_to_ra',
            'supervisor_note' => $request->note,
        ]);

        // Kamar kembali ke vacant_dirty karena perlu dibersihkan ulang
        $task->room->changeStatus(
            'vacant_dirty',
            auth()->user(),
            'Dikembalikan ke RA: ' . $request->note,
            $task
        );

        return back()->with('success',
            "Tugas kamar {$task->room->room_number} dikembalikan ke RA."
        );
    }
}
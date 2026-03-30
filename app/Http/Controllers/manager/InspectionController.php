<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    /**
     * Halaman utama Manager
     * - Pending inspection (dari SPV)
     * - History task yang sudah selesai
     */
    public function index(Request $request)
    {
        // 🔥 TASK MENUNGGU APPROVE MANAGER
        $pendingTasks = Task::with(['room','assignedUser','assignedByUser'])
            ->where('status', 'pending_manager')
            ->latest('supervisor_approved_at')
            ->paginate(10, ['*'], 'pending_page');

        // 🔍 HISTORY (SUDAH DI APPROVE MANAGER)
        $completedTasksQuery = Task::with(['room','assignedUser','assignedByUser'])
            ->where('manager_id', auth()->id())
            ->where('status', 'completed');

        // ✅ FILTER STATUS (optional)
        if ($request->filled('status')) {
            $completedTasksQuery->where('status', $request->status);
        }

        // ✅ FILTER TANGGAL
        if ($request->filled('date')) {
            $completedTasksQuery->whereDate('completed_at', $request->date);
        }

        // ✅ SEARCH KAMAR
        if ($request->filled('search')) {
            $completedTasksQuery->whereHas('room', function ($q) use ($request) {
                $q->where('room_number', 'like', '%' . $request->search . '%');
            });
        }

        $completedTasks = $completedTasksQuery
            ->latest('completed_at')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        // 📊 STATISTIK
        $stats = [
            'approved_today' => Task::where('manager_id', auth()->id())
                ->whereDate('completed_at', today())
                ->count(),

            'total_approved' => Task::where('manager_id', auth()->id())
                ->count(),
        ];

        return view('manager.inspections', compact(
            'pendingTasks',
            'completedTasks',
            'stats'
        ));
    }

    /**
     * Detail task untuk dicek manager
     */
    public function show(Task $task)
    {
        abort_if($task->status !== 'pending_manager', 403);

        $task->load([
            'room',
            'assignedUser',
            'assignedByUser',
            'preparationChecklists',
            'cleaningChecklists'
        ]);

        return view('manager.inspection-detail', compact('task'));
    }

    /**
     * APPROVE FINAL (Manager)
     */
    public function approve(Request $request, Task $task)
    {
        abort_if($task->status !== 'pending_manager', 403);

        $task->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'manager_id'   => auth()->id(),
            'manager_note' => $request->note ?? 'Approved by Manager'
        ]);

        // ✅ Update status kamar → siap dijual
        $task->room->changeStatus(
            'vacant_ready',
            auth()->user(),
            'Final approve manager',
            $task
        );

        // kosongkan assigned
        $task->room->update(['assigned_to' => null]);

        return back()->with('success',
            "Kamar {$task->room->room_number} → Vacant Ready! ✓"
        );
    }

    /**
     * RETURN KE SUPERVISOR
     */
    public function returnToSupervisor(Request $request, Task $task)
    {
        abort_if($task->status !== 'pending_manager', 403);

        $request->validate([
            'note' => 'required|string|max:500'
        ]);

        $task->update([
            'status'       => 'returned_to_supervisor',
            'manager_note' => $request->note
        ]);

        return back()->with('success',
            "Tugas kamar {$task->room->room_number} dikembalikan ke Supervisor."
        );
    }
}
<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index()
    {
        return view('manager.inspections', [
            'pendingTasks'   => Task::where('status','pending_manager')->with(['room','assignedUser','assignedByUser'])->latest('supervisor_approved_at')->get(),
            'completedTasks' => Task::where('status','completed')->with(['room','assignedUser'])->latest('completed_at')->take(10)->get(),
        ]);
    }

    public function show(Task $task)
    {
        abort_if($task->status !== 'pending_manager', 403);
        $task->load(['room','assignedUser','assignedByUser','preparationChecklists','cleaningChecklists']);
        return view('manager.inspection-detail', compact('task'));
    }

    public function approve(Task $task)
    {
        abort_if($task->status !== 'pending_manager', 403);
        $task->update(['status' => 'completed', 'completed_at' => now()]);
        $task->room->changeStatus('vacant_ready', auth()->user(), 'Final approve manager', $task);
        $task->room->update(['assigned_to' => null]);
        return back()->with('success', "Kamar {$task->room->room_number} → Vacant Ready! ✓");
    }

    public function returnToSupervisor(Request $request, Task $task)
    {
        $request->validate(['note' => 'required|string|max:500']);
        abort_if($task->status !== 'pending_manager', 403);
        $task->update(['status' => 'returned_to_supervisor', 'manager_note' => $request->note]);
        return back()->with('success', 'Tugas dikembalikan ke Supervisor.');
    }
}

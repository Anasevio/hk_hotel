<?php
namespace App\Http\Controllers\Ra;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function show(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);
        $task->load(['room','preparationChecklists','cleaningChecklists']);
        return view('ra.task-detail', compact('task'));
    }

    public function start(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);
        abort_if(!in_array($task->status, ['pending','returned_to_ra']), 422);
        $task->update(['status' => 'in_progress', 'started_at' => $task->started_at ?? now()]);
        return back()->with('success', 'Tugas dimulai! 💪');
    }

    public function updateChecklist(Request $request, Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);
        abort_if($task->status !== 'in_progress', 422);
        $request->validate(['checklist_id' => 'required|exists:task_checklists,id', 'is_checked' => 'required|boolean']);
        $item = $task->checklists()->findOrFail($request->checklist_id);
        $item->update(['is_checked' => $request->is_checked, 'checked_at' => $request->is_checked ? now() : null]);
        $this->recalcProgress($task);
        $task->refresh();
        return response()->json([
            'success'             => true,
            'checklist1_progress' => $task->checklist1_progress,
            'checklist2_progress' => $task->checklist2_progress,
            'overall_progress'    => $task->overall_progress,
        ]);
    }

    public function submit(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);
        abort_if($task->status !== 'in_progress', 422);
        if ($task->preparationChecklists()->where('is_checked', false)->exists())
            return back()->with('error', 'Checklist persiapan belum lengkap!');
        if ($task->cleaningChecklists()->where('is_checked', false)->exists())
            return back()->with('error', 'Checklist pembersihan belum lengkap!');
        $task->update(['status' => 'pending_supervisor', 'submitted_at' => now()]);
        return redirect()->route('ra.dashboard')->with('success', "Kamar {$task->room->room_number} berhasil disubmit ke Supervisor! ✓");
    }

    private function recalcProgress(Task $task): void
    {
        $prep  = $task->preparationChecklists();
        $clean = $task->cleaningChecklists();
        $pt = $prep->count();  $ct = $clean->count();
        $task->update([
            'checklist1_progress' => $pt > 0 ? (int)round($prep->where('is_checked', true)->count()  / $pt * 100) : 0,
            'checklist2_progress' => $ct > 0 ? (int)round($clean->where('is_checked', true)->count() / $ct * 100) : 0,
        ]);
    }
}

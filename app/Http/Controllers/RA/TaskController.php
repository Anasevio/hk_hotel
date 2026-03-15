<?php

namespace App\Http\Controllers\Ra;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TimerSetting;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // ── Tampilkan halaman detail task (3-step checklist) ──────────
    public function show(Task $task)
    {
        // Hanya RA yang di-assign boleh lihat
        abort_if($task->assigned_to !== auth()->id(), 403);

        $task->load([
            'room',
            'assignedByUser',      // supervisor yang assign
            'preparationChecklists',
            'cleaningChecklists',
        ]);

        // Hitung sisa waktu (dalam detik) untuk timer di frontend
        $elapsedSeconds  = $task->started_at ? now()->diffInSeconds($task->started_at) : 0;
        $limitSeconds    = $task->time_limit * 60;
        $remainingSeconds = max(0, $limitSeconds - $elapsedSeconds);

        return view('ra.task-detail', compact('task', 'remainingSeconds'));
    }

    // ── RA mulai mengerjakan tugas ────────────────────────────────
    public function start(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);
        abort_if(!in_array($task->status, ['pending', 'returned_to_ra']), 422);

        $task->update([
            'status'     => 'in_progress',
            'started_at' => $task->started_at ?? now(), // tidak reset timer jika returned
        ]);

        return back()->with('success', 'Tugas dimulai! Semangat! 💪');
    }

    // ── Toggle centang item checklist (AJAX) ──────────────────────
    public function updateChecklist(Request $request, Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);
        abort_if($task->status !== 'in_progress', 422);

        $request->validate([
            'checklist_id' => 'required|exists:task_checklists,id',
            'is_checked'   => 'required|boolean',
        ]);

        // Pastikan checklist memang milik task ini
        $item = $task->checklists()->findOrFail($request->checklist_id);

        $item->update([
            'is_checked' => $request->is_checked,
            'checked_at' => $request->is_checked ? now() : null,
        ]);

        // Recalculate progress
        $this->recalcProgress($task);
        $task->refresh();

        return response()->json([
            'success'             => true,
            'checklist1_progress' => $task->checklist1_progress,
            'checklist2_progress' => $task->checklist2_progress,
            'overall_progress'    => $task->overall_progress,
        ]);
    }

    // ── RA submit task ke Supervisor ──────────────────────────────
    public function submit(Request $request, Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);
        abort_if($task->status !== 'in_progress', 422);

        // Validasi: semua checklist harus selesai
        $incompletePrep  = $task->preparationChecklists()->where('is_checked', false)->count();
        $incompleteClean = $task->cleaningChecklists()->where('is_checked', false)->count();

        if ($incompletePrep > 0) {
            return response()->json([
                'success' => false,
                'message' => "Masih ada {$incompletePrep} item persiapan yang belum dicentang.",
            ], 422);
        }

        if ($incompleteClean > 0) {
            return response()->json([
                'success' => false,
                'message' => "Masih ada {$incompleteClean} item kebersihan yang belum dicentang.",
            ], 422);
        }

        // Update status task
        $task->update([
            'status'       => 'pending_supervisor',
            'submitted_at' => now(),
        ]);

        // Update status kamar → vacant_clean (sudah dibersihkan, belum diinspeksi)
        $task->room->changeStatus(
            'vacant_clean',
            auth()->user(),
            'RA submit tugas kebersihan',
            $task
        );

        return response()->json([
            'success'  => true,
            'message'  => "Kamar {$task->room->room_number} berhasil disubmit ke Supervisor! ✓",
            'redirect' => route('ra.rooms.index'),
        ]);
    }

    // ── Recalculate progress checklist ────────────────────────────
    private function recalcProgress(Task $task): void
    {
        $prepTotal   = $task->preparationChecklists()->count();
        $prepDone    = $task->preparationChecklists()->where('is_checked', true)->count();

        $cleanTotal  = $task->cleaningChecklists()->count();
        $cleanDone   = $task->cleaningChecklists()->where('is_checked', true)->count();

        $task->update([
            'checklist1_progress' => $prepTotal  > 0 ? (int) round($prepDone  / $prepTotal  * 100) : 0,
            'checklist2_progress' => $cleanTotal > 0 ? (int) round($cleanDone / $cleanTotal * 100) : 0,
        ]);
    }
}
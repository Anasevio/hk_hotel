<?php

namespace App\Http\Controllers\Ra;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // ── Tampilkan halaman detail task ─────────────────────────────
    public function show(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);

        $task->load(['room', 'assignedByUser', 'checklists']);

        return view('ra.task-detail', compact('task'));
    }

    // ── RA mulai mengerjakan tugas ────────────────────────────────
    public function start(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);

        if (!in_array($task->status, ['pending', 'returned_to_ra'])) {
            return redirect()->route('ra.tasks.show', $task->id)
                ->with('error', "Tugas tidak bisa dimulai — status saat ini: {$task->status_label}.");
        }

        $task->update([
            'status'     => 'in_progress',
            'started_at' => $task->started_at ?? now(),
        ]);

        // Kamar → vacant_clean saat RA mulai mengerjakan
        $task->room->changeStatus(
            'vacant_clean',
            auth()->user(),
            'RA mulai mengerjakan kamar',
            $task
        );

        return back()->with('success', 'Tugas dimulai! Timer berjalan.');
    }

    // ── RA submit task ke Supervisor ──────────────────────────────
    // Checklist dikelola di frontend (JS), validasi hanya cek status task.
    // Tombol submit hanya muncul setelah semua tab checklist selesai dicentang.
    public function submit(Request $request, Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);

        if ($task->status !== 'in_progress') {
            return redirect()->route('ra.tasks.show', $task->id)
                ->with('error', "Tugas tidak bisa disubmit — status saat ini: {$task->status_label}. Pastikan kamu sudah menekan Mulai Tugas.");
        }

        $task->update([
            'status'                => 'pending_supervisor',
            'submitted_at'          => now(),
            'checklist1_progress'   => 100,
            'checklist2_progress'   => 100,
        ]);

        // Status kamar tetap vacant_clean (sudah diset saat RA mulai)

        return redirect()->route('ra.rooms.index')
            ->with('success', "Kamar {$task->room->room_number} berhasil disubmit ke Supervisor! ✓");
    }
}
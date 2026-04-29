<?php

namespace App\Http\Controllers\Ra;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskChecklist;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // ── Detail task ───────────────────────────────────────────────
    public function show(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);

        $task->load([
            'room',
            'assignedByUser',
            'checklists',
            'preparationChecklists',
            'cleaningChecklists'
        ]);

        return view('ra.task-detail', compact('task'));
    }

    public function sopDone(Task $task)
{
    abort_if($task->assigned_to !== auth()->id(), 403);

   if (!$task->sop_viewed_at) {
    $task->update([
        'sop_viewed_at' => now()
    ]);
}

    return back()->with('success', 'SOP sudah dibaca.');
}

    // ── Start task ────────────────────────────────────────────────
    public function start(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);

        if (!in_array($task->status, ['pending', 'returned_to_ra'])) {
            return back()->with('error', "Tugas tidak bisa dimulai — status: {$task->status_label}");
        }

        if (!$task->sop_viewed_at) {
        return back()->with('error', 'Harus baca SOP dulu.');
}

        $task->update([
            'status'     => 'in_progress',
            'started_at' => $task->started_at ?? now(),
        ]);

        // Update status kamar
        $task->room->changeStatus(
            'vacant_clean',
            auth()->user(),
            'RA mulai mengerjakan kamar',
            $task
        );

        return back()->with('success', 'Tugas dimulai.');
    }

    // ── Update checklist (INI YANG SEBELUMNYA KAMU BELUM ADA) ─────
    public function updateChecklist(Request $request, Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);

        foreach ($task->checklists as $item) {
            $item->update([
                'is_checked' => isset($request->checklists[$item->id])
            ]);
        }

        // Hitung ulang progress
        $prepTotal = $task->preparationChecklists()->count();
        $prepDone  = $task->preparationChecklists()->where('is_checked', true)->count();

        $cleanTotal = $task->cleaningChecklists()->count();
        $cleanDone  = $task->cleaningChecklists()->where('is_checked', true)->count();

        $task->update([
            'checklist1_progress' => $prepTotal ? round(($prepDone / $prepTotal) * 100) : 0,
            'checklist2_progress' => $cleanTotal ? round(($cleanDone / $cleanTotal) * 100) : 0,
        ]);

        return back()->with('success', 'Checklist diperbarui.');
    }

    // ── Submit ke Supervisor ──────────────────────────────────────
public function submit(Request $request, Task $task)
{
    abort_if($task->assigned_to !== auth()->id(), 403);

    if ($task->status !== 'in_progress') {
        return back()->with('error', "Tugas belum dimulai.");
    }

    // ambil data dari JS
    $data = json_decode($request->checklists, true);

    if (!$data) {
        return back()->with('error', 'Checklist tidak terbaca.');
    }

    // 🔥 SET SEMUA CHECKLIST JADI DONE
    TaskChecklist::where('task_id', $task->id)
        ->update([
            'is_checked' => true,
            'checked_at' => now()
        ]);

    // 🔥 HITUNG ULANG (biar SPV gak salah baca)
    $prepTotal = $task->preparationChecklists()->count();
    $cleanTotal = $task->cleaningChecklists()->count();

    $task->update([
        'status' => 'pending_supervisor',
        'submitted_at' => now(),
        'ra_notes' => $request->note,
        'checklist1_progress' => $prepTotal ? 100 : 0,
        'checklist2_progress' => $cleanTotal ? 100 : 0,
    ]);

    // 🔥 PENTING: reload relasi
    $task->refresh();

    return redirect()
        ->route('ra.rooms.index')
        ->with('success', "Kamar {$task->room->room_number} berhasil dikirim ke Supervisor.");
}
}
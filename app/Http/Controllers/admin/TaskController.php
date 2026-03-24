<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TimerSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    // ── Checklist Templates ────────────────────────────────────
    // Edit di sini jika ingin menambah/mengubah item checklist.
    // 'order' di-generate otomatis dari urutan array (index + 1).

    private const PREP_ITEMS = [
        'Bed Sheet (2 set)',
        'Pillow Case (4 pcs)',
        'Duvet Cover',
        'Bath Towel (2)',
        'Hand Towel (2)',
        'Face Towel (2)',
        'Mop & Bucket',
        'Vacuum Cleaner',
        'Spray Bottle',
        'Lap Microfiber',
        'Sikat Toilet',
        'Sarung Tangan',
        'Sabun Mandi',
        'Shampoo',
        'Conditioner',
        'Body Lotion',
        'Dental Kit',
        'Coffee & Tea Set',
    ];

    private const CLEAN_ITEMS = [
        ['name' => 'Ganti sprei & sarung bantal',  'minutes' => 10],
        ['name' => 'Bersihkan headboard',           'minutes' => 3],
        ['name' => 'Lap meja & nakas',              'minutes' => 3],
        ['name' => 'Vacuum karpet',                 'minutes' => 5],
        ['name' => 'Rapikan bantal',                'minutes' => 2],
        ['name' => 'Bersihkan cermin',              'minutes' => 2],
        ['name' => 'Lap sofa & meja',               'minutes' => 3],
        ['name' => 'Bersihkan TV & remote',         'minutes' => 2],
        ['name' => 'Lap jendela & kaca',            'minutes' => 3],
        ['name' => 'Vacuum sofa',                   'minutes' => 3],
        ['name' => 'Rapikan dekorasi',              'minutes' => 2],
        ['name' => 'Cek minibar',                   'minutes' => 2],
        ['name' => 'Sikat toilet & wastafel',       'minutes' => 5],
        ['name' => 'Bersihkan shower/bathtub',      'minutes' => 5],
        ['name' => 'Lap cermin kamar mandi',        'minutes' => 2],
        ['name' => 'Ganti handuk & amenities',      'minutes' => 3],
        ['name' => 'Bersihkan lantai',              'minutes' => 5],
        ['name' => 'Cek exhaust fan',               'minutes' => 1],
    ];

    // ── Methods ────────────────────────────────────────────────

    /**
     * Assign tugas kamar ke RA
     * POST /admin/tasks/assign
     */
    public function assign(Request $request)
    {
        $request->validate([
            'room_id'     => 'required|exists:rooms,id',
            'assigned_to' => 'required|exists:users,id',
            'time_limit'  => 'required|integer|min:1|max:480',
        ]);

        $room = Room::findOrFail($request->room_id);
        $ra   = User::where('id', $request->assigned_to)
                    ->where('role', 'ra')
                    ->where('is_active', true)
                    ->firstOrFail();

        if ($room->tasks()->whereNotIn('status', ['completed'])->exists()) {
            return back()->with('error', "Kamar {$room->room_number} sudah memiliki tugas aktif.");
        }

        if (!in_array($room->status, ['vacant_dirty', 'expected_departure'])) {
            return back()->with('error', "Kamar {$room->room_number} tidak perlu dibersihkan (status: {$room->statusLabel}).");
        }

        $task = Task::create([
            'room_id'     => $room->id,
            'assigned_to' => $ra->id,
            'assigned_by' => auth()->id(),
            'status'      => 'pending',
            'time_limit'  => $request->time_limit,
        ]);

        // Insert semua checklist dalam satu query, bukan 36x create()
        TaskChecklist::insert($this->buildChecklists($task->id));

        $room->update(['assigned_to' => $ra->id]);

        return back()->with('success',
            "Tugas kamar {$room->room_number} berhasil diberikan ke {$ra->name} dengan timer {$request->time_limit} menit."
        );
    }

    /**
     * RA mulai mengerjakan tugas
     * POST /ra/tasks/{task}/start
     */
    public function start(Task $task)
    {
        abort_if($task->assigned_to !== auth()->id(), 403);

        if ($task->status !== 'pending') {
            return back()->with('error', 'Tugas sudah dimulai atau tidak bisa diubah.');
        }

        $task->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);

        return back()->with('success', 'Tugas dimulai! Timer berjalan.');
    }

    /**
     * Batalkan / cabut tugas dari RA
     * DELETE /admin/tasks/{task}/cancel
     */
    public function cancel(Task $task)
    {
        if (!in_array($task->status, ['pending', 'in_progress'])) {
            return back()->with('error', 'Tugas tidak bisa dibatalkan karena sudah disubmit.');
        }

        $roomNumber = $task->room->room_number;
        $raName     = $task->assignedUser->name;

        $task->room->update(['assigned_to' => null]);
        $task->checklists()->delete();
        $task->delete();

        return back()->with('success',
            "Tugas kamar {$roomNumber} dari {$raName} berhasil dibatalkan."
        );
    }

    // ── Helpers ────────────────────────────────────────────────

    /**
     * Bangun array checklist siap untuk TaskChecklist::insert().
     * Menggunakan konstanta PREP_ITEMS dan CLEAN_ITEMS di atas.
     */
    private function buildChecklists(int $taskId): array
    {
        $now  = Carbon::now();
        $rows = [];

        foreach (self::PREP_ITEMS as $i => $name) {
            $rows[] = [
                'task_id'           => $taskId,
                'type'              => 'preparation',
                'item_name'         => $name,
                'order'             => $i + 1,
                'estimated_minutes' => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        foreach (self::CLEAN_ITEMS as $i => $item) {
            $rows[] = [
                'task_id'           => $taskId,
                'type'              => 'cleaning',
                'item_name'         => $item['name'],
                'order'             => $i + 1,
                'estimated_minutes' => $item['minutes'],
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        return $rows;
    }
}
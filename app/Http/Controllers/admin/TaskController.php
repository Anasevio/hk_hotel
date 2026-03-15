<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TimerSetting;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Assign tugas kamar ke RA
     * POST /admin/tasks/assign
     */
    public function assign(Request $request)
    {
        $request->validate([
            'room_id'     => 'required|exists:rooms,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $room = Room::findOrFail($request->room_id);
        $ra   = User::where('id', $request->assigned_to)
                    ->where('role', 'ra')
                    ->where('is_active', true)
                    ->firstOrFail();

        // Cek apakah kamar sudah punya task aktif
        $existingTask = $room->tasks()
            ->whereNotIn('status', ['completed'])
            ->exists();

        if ($existingTask) {
            return back()->with('error', "Kamar {$room->room_number} sudah memiliki tugas aktif.");
        }

        // Kamar harus vacant_dirty atau expected_departure untuk bisa ditugaskan
        if (!in_array($room->status, ['vacant_dirty', 'expected_departure'])) {
            return back()->with('error', "Kamar {$room->room_number} tidak perlu dibersihkan (status: {$room->statusLabel}).");
        }

        // Ambil time limit dari timer settings
        $timeLimit = TimerSetting::getDuration($room->status);

        // Buat task
        $task = Task::create([
            'room_id'     => $room->id,
            'assigned_to' => $ra->id,
            'assigned_by' => auth()->id(),
            'status'      => 'pending',
            'time_limit'  => $timeLimit,
        ]);

        // Buat checklist preparation (18 item)
        $prepItems = [
            ['item_name' => 'Bed Sheet (2 set)',    'order' => 1],
            ['item_name' => 'Pillow Case (4 pcs)',  'order' => 2],
            ['item_name' => 'Duvet Cover',          'order' => 3],
            ['item_name' => 'Bath Towel (2)',        'order' => 4],
            ['item_name' => 'Hand Towel (2)',        'order' => 5],
            ['item_name' => 'Face Towel (2)',        'order' => 6],
            ['item_name' => 'Mop & Bucket',         'order' => 7],
            ['item_name' => 'Vacuum Cleaner',       'order' => 8],
            ['item_name' => 'Spray Bottle',         'order' => 9],
            ['item_name' => 'Lap Microfiber',       'order' => 10],
            ['item_name' => 'Sikat Toilet',         'order' => 11],
            ['item_name' => 'Sarung Tangan',        'order' => 12],
            ['item_name' => 'Sabun Mandi',          'order' => 13],
            ['item_name' => 'Shampoo',              'order' => 14],
            ['item_name' => 'Conditioner',          'order' => 15],
            ['item_name' => 'Body Lotion',          'order' => 16],
            ['item_name' => 'Dental Kit',           'order' => 17],
            ['item_name' => 'Coffee & Tea Set',     'order' => 18],
        ];

        // Buat checklist cleaning (18 item)
        $cleanItems = [
            ['item_name' => 'Ganti sprei & sarung bantal',  'order' => 1,  'estimated_minutes' => 10],
            ['item_name' => 'Bersihkan headboard',          'order' => 2,  'estimated_minutes' => 3],
            ['item_name' => 'Lap meja & nakas',             'order' => 3,  'estimated_minutes' => 3],
            ['item_name' => 'Vacuum karpet',                'order' => 4,  'estimated_minutes' => 5],
            ['item_name' => 'Rapikan bantal',               'order' => 5,  'estimated_minutes' => 2],
            ['item_name' => 'Bersihkan cermin',             'order' => 6,  'estimated_minutes' => 2],
            ['item_name' => 'Lap sofa & meja',              'order' => 7,  'estimated_minutes' => 3],
            ['item_name' => 'Bersihkan TV & remote',        'order' => 8,  'estimated_minutes' => 2],
            ['item_name' => 'Lap jendela & kaca',           'order' => 9,  'estimated_minutes' => 3],
            ['item_name' => 'Vacuum sofa',                  'order' => 10, 'estimated_minutes' => 3],
            ['item_name' => 'Rapikan dekorasi',             'order' => 11, 'estimated_minutes' => 2],
            ['item_name' => 'Cek minibar',                  'order' => 12, 'estimated_minutes' => 2],
            ['item_name' => 'Sikat toilet & wastafel',      'order' => 13, 'estimated_minutes' => 5],
            ['item_name' => 'Bersihkan shower/bathtub',     'order' => 14, 'estimated_minutes' => 5],
            ['item_name' => 'Lap cermin kamar mandi',       'order' => 15, 'estimated_minutes' => 2],
            ['item_name' => 'Ganti handuk & amenities',     'order' => 16, 'estimated_minutes' => 3],
            ['item_name' => 'Bersihkan lantai',             'order' => 17, 'estimated_minutes' => 5],
            ['item_name' => 'Cek exhaust fan',              'order' => 18, 'estimated_minutes' => 1],
        ];

        foreach ($prepItems as $item) {
            TaskChecklist::create([
                'task_id'   => $task->id,
                'type'      => 'preparation',
                'item_name' => $item['item_name'],
                'order'     => $item['order'],
            ]);
        }

        foreach ($cleanItems as $item) {
            TaskChecklist::create([
                'task_id'            => $task->id,
                'type'               => 'cleaning',
                'item_name'          => $item['item_name'],
                'order'              => $item['order'],
                'estimated_minutes'  => $item['estimated_minutes'],
            ]);
        }

        // Assign kamar ke RA
        $room->update(['assigned_to' => $ra->id]);

        return back()->with('success',
            "Tugas kamar {$room->room_number} berhasil diberikan ke {$ra->name}."
        );
    }

    /**
     * Batalkan / cabut tugas dari RA
     * DELETE /admin/tasks/{task}/cancel
     */
    public function cancel(Task $task)
    {
        // Hanya bisa cancel jika masih pending atau in_progress
        if (!in_array($task->status, ['pending', 'in_progress'])) {
            return back()->with('error', 'Tugas tidak bisa dibatalkan karena sudah disubmit.');
        }

        $roomNumber = $task->room->room_number;
        $raName     = $task->assignedUser->name;

        // Lepas assignment kamar
        $task->room->update(['assigned_to' => null]);

        // Hapus semua checklist lalu hapus task
        $task->checklists()->delete();
        $task->delete();

        return back()->with('success',
            "Tugas kamar {$roomNumber} dari {$raName} berhasil dibatalkan."
        );
    }
}
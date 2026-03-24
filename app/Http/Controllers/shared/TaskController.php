<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomStatusLog;
use App\Models\TimerSetting;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Map status kamar → CSS class dan label teks.
    // Didefinisikan sebagai konstanta agar mudah diubah
    // tanpa harus menyentuh blade.
    private const STATUS_CLASS = [
        'vacant_dirty'       => 'vd',
        'vacant_clean'       => 'vc',
        'vacant_ready'       => 'vr',
        'occupied'           => 'oc',
        'expected_departure' => 'ed',
    ];

    private const STATUS_LABEL = [
        'vacant_dirty'       => 'Vacant Dirty',
        'vacant_clean'       => 'Vacant Clean',
        'vacant_ready'       => 'Vacant Ready',
        'occupied'           => 'Occupied',
        'expected_departure' => 'Exp. Departure',
    ];

    // ── Admin / Supervisor / Manager: semua kamar ──────────────────
    public function index()
    {
        $rooms = Room::with(['assignedUser', 'activeTask'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        // Daftar RA aktif + hitung kamar aktif masing-masing
        $raList = User::where('role', 'ra')
            ->where('is_active', true)
            ->with(['assignedRooms'])
            ->orderBy('name')
            ->get();

        // Timer settings untuk auto-fill durasi saat assign
        $timerSettings = TimerSetting::orderBy('key')->get();

        return view('admin.rooms', compact('rooms', 'raList', 'timerSettings') + [
            'clsMap'      => self::STATUS_CLASS,
            'lblMap'      => self::STATUS_LABEL,
            'floorGroups' => $rooms->groupBy('floor'),
        ]);
    }

    // ── RA: hanya kamar yang di-assign ke RA ini ───────────────────
    public function raIndex()
    {
        $user = auth()->user();

        $rooms = Room::where('assigned_to', $user->id)
            ->with(['activeTask'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        return view('ra.task', compact('rooms'));
    }

    // ── RA: detail satu kamar → redirect ke task ───────────────────
    public function raShow(Room $room)
    {
        abort_if($room->assigned_to !== auth()->id(), 403);

        $task = $room->tasks()
            ->where('assigned_to', auth()->id())
            ->whereNotIn('status', ['completed'])
            ->with(['preparationChecklists', 'cleaningChecklists', 'assignedByUser'])
            ->latest()
            ->first();

        if (!$task) {
            return redirect()->route('ra.rooms.index')
                ->with('error', 'Tidak ada tugas aktif untuk kamar ini.');
        }

        return redirect()->route('ra.tasks.show', $task);
    }

    // ── Admin / Supervisor: update status kamar ────────────────────
    public function updateStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(self::STATUS_CLASS)),
            'reason' => 'nullable|string|max:255',
        ]);

        $room->changeStatus(
            $request->status,
            auth()->user(),
            $request->reason ?? 'Manual update oleh ' . auth()->user()->name
        );

        return back()->with('success',
            "Status kamar {$room->room_number} diubah ke {$room->statusLabel}."
        );
    }

    // ── Log perubahan status ───────────────────────────────────────
    public function logs()
    {
        $logs = RoomStatusLog::with(['room', 'changedByUser'])
            ->latest()
            ->paginate(30);

        return view('shared.room-logs', compact('logs'));
    }
}
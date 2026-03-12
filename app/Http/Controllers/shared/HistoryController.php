<?php
namespace App\Http\Controllers\Shared;
use App\Http\Controllers\Controller;
use App\Models\{Room, RoomStatusLog};
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $role  = auth()->user()->role;
        $rooms = Room::with(['assignedUser','activeTask'])->orderBy('room_number')->get();
        $logs  = RoomStatusLog::with(['room','changedByUser'])->latest()->take(20)->get();
        return view("{$role}.rooms", compact('rooms', 'logs'));
    }

    public function raIndex()
    {
        $rooms = Room::where('assigned_to', auth()->id())->with(['activeTask'])->orderBy('room_number')->get();
        return view('ra.rooms', compact('rooms'));
    }

    public function raShow(Room $room)
    {
        abort_if($room->assigned_to !== auth()->id(), 403);
        return view('ra.room-detail', ['room' => $room, 'task' => $room->activeTask]);
    }

    public function updateStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:vacant_dirty,vacant_clean,vacant_ready,occupied,expected_departure',
            'reason' => 'nullable|string|max:200',
        ]);
        $room->changeStatus($request->status, auth()->user(), $request->reason ?? 'Manual change');
        return back()->with('success', "Status kamar {$room->room_number} diubah ke {$room->status_label}.");
    }

    public function logs()
    {
        return view('admin.room-logs', ['logs' => RoomStatusLog::with(['room','changedByUser','task'])->latest()->paginate(30)]);
    }
}

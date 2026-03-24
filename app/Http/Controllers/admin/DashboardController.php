<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Room, Task, Attendance, RoomStatusLog};

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalRooms'      => Room::count(),
            'vacantDirty'     => Room::where('status', 'vacant_dirty')->count(),
            'inProgress'      => Task::where('status', 'in_progress')->count(),
            'todayAttendance' => Attendance::whereDate('date', today())->count(),
            'totalStaff'      => User::where('role', '!=', 'admin')->where('is_active', true)->count(),
            'recentLogs'      => RoomStatusLog::with(['room', 'changedByUser'])->latest()->take(8)->get(),
            'staffList'       => User::where('role', '!=', 'admin')
                                    ->with(['todayAttendance'])
                                    ->orderBy('role')->orderBy('name')->get(),
            'badgeMap'        => [
                'hadir' => ['label' => 'Hadir', 'class' => 'badge-green'],
                'izin'  => ['label' => 'Izin',  'class' => 'badge-yellow'],
                'sakit' => ['label' => 'Sakit', 'class' => 'badge-blue'],
                'alfa'  => ['label' => 'Alfa',  'class' => 'badge-gray'],
            ],
        ]);
    }
}
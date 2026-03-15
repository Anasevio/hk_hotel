<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\{Room, Task};

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ambil semua kamar (karena supervisor memantau semua, bukan assigned)
        $myRooms = Room::all();

        // Bagian yang membedakan: Tugas yang butuh verifikasi supervisor
        $activeTask = Task::where('status', 'pending_supervisor')
            ->with(['room', 'assignedTo']) // 'assignedTo' adalah RA yang mengerjakan
            ->latest()
            ->first();

        // Jumlah tugas yang sudah di-approve oleh supervisor hari ini
        $completedToday = Task::where('status', 'completed')
            ->whereDate('supervisor_approved_at', today())
            ->count();

        $todayAtt = $user->todayAttendance;

        return view('supervisor.dashboard', compact('myRooms', 'activeTask', 'completedToday', 'todayAtt'));
    }
}
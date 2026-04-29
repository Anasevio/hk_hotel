<?php

namespace App\Http\Controllers\Ra;

use App\Http\Controllers\Controller;
use App\Models\{Room, Task};

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $myRooms = Room::where('assigned_to', $user->id)->get();

        $activeTask = Task::where('assigned_to', $user->id)
    ->whereIn('status', [
        'pending',
        'in_progress',
        'returned_to_ra'
    ])
    ->with('room')
    ->latest()
    ->first();

        $completedToday = Task::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        $todayAtt = $user->todayAttendance;

        return view('ra.dashboard', compact('myRooms', 'activeTask', 'completedToday', 'todayAtt'));
    }
}

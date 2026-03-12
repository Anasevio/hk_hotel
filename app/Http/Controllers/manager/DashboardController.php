<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Models\{Room, Task};

class DashboardController extends Controller
{
    public function index()
    {
        return view('manager.dashboard', [
            'vacantReady'    => Room::where('status', 'vacant_ready')->count(),
            'occupied'       => Room::where('status', 'occupied')->count(),
            'vacantDirty'    => Room::where('status', 'vacant_dirty')->count(),
            'pendingApprove' => Task::where('status', 'pending_manager')->count(),
            'roomSummary'    => Room::selectRaw('status, count(*) as total')->groupBy('status')->get(),
            'pendingTasks'   => Task::where('status', 'pending_manager')
                                   ->with(['room', 'assignedUser', 'assignedByUser'])->get(),
        ]);
    }
}

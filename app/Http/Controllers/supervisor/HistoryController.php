<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;

class HistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $history = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with('room')
            ->orderBy('updated_at', 'desc')
            ->take(30)
            ->get();

        $stats = [
            'approved' => Task::where('assigned_to', $user->id)->where('status', 'completed')->count(),
            'returned' => Task::where('assigned_to', $user->id)->where('status', 'cancelled')->count(),
            'total'    => Task::where('assigned_to', $user->id)->whereIn('status', ['completed', 'cancelled'])->count(),
        ];

        return view('supervisor.history', compact('history', 'stats'));
    }
}
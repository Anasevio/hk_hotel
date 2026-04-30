<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with([
            'room',
            'assignedUser',
            'supervisor',
            'manager'
        ])
        ->where('supervisor_id', auth()->id())
        ->where('updated_at', '>=', now()->subDays(30));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('updated_at', $request->date);
        }

        if ($request->filled('search')) {
            $query->whereHas('room', function ($q) use ($request) {
                $q->where('room_number', 'like', '%' . $request->search . '%');
            });
        }

        $history = $query->latest()->paginate(10)->withQueryString();

        return view('supervisor.history', compact('history'));
    }
}
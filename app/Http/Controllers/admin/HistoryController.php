<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['room', 'assignedUser'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->where('updated_at', '>=', now()->subDays(30));

        // 🔍 Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('updated_at', $request->date);
        }

        // 🔍 Filter murid (RA)
        if ($request->filled('user')) {
            $query->where('assigned_to', $request->user);
        }

        // 🔍 Filter tugas
        if ($request->filled('task')) {
            $query->whereHas('room', function ($q) use ($request) {
                $q->where('room_number', 'like', '%' . $request->task . '%');
            });
        }

        $history = $query->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        // ambil semua user (untuk dropdown filter)
        $users = User::where('role', 'ra')->get();

        return view('admin.history', compact('history', 'users'));
    }
}
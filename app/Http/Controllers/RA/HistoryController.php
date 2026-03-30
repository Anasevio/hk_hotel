<?php

namespace App\Http\Controllers\Ra;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('room')
            ->where('assigned_to', auth()->id())
            ->where('status', 'completed') // 🔥 fokus ke selesai
            ->where('updated_at', '>=', now()->subDays(30));

        // filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('updated_at', $request->date);
        }

        // search kamar
        if ($request->filled('search')) {
            $query->whereHas('room', function ($q) use ($request) {
                $q->where('room_number', 'like', '%' . $request->search . '%');
            });
        }

        $history = $query->latest()->paginate(5)->withQueryString();

        return view('ra.history', compact('history'));
    }
}
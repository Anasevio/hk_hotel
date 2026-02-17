<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            $totalAccounts = User::count();
            $hadirBulanIni = Activity::whereMonth('created_at', now()->month)->count();
            $activities = Activity::latest()->take(10)->get();
            $users = User::orderBy('name')->get();
            $dbAvailable = true;
        } catch (\Throwable $e) {
            // Fallback ketika database belum tersedia: tampilkan data contoh agar view tetap render
            $totalAccounts = 0;
            $hadirBulanIni = 0;
            $activities = collect([
                (object)[
                    'created_at' => now(),
                    'user' => (object)['name' => 'Contoh User'],
                    'room' => 'Kamar 01',
                    'description' => 'Contoh aktivitas (DB belum di-setup)',
                    'status' => 'Selesai',
                ],
            ]);
            $users = collect([
                (object)['id' => 1, 'name' => 'Ilyas Noor', 'email' => 'ilyas@example.com'],
                (object)['id' => 2, 'name' => 'Aulia', 'email' => 'aulia@example.com'],
            ]);
            $dbAvailable = false;
        }

        return view('admin.dashboard_admin', compact('totalAccounts', 'hadirBulanIni', 'activities', 'users', 'dbAvailable'));
    }

    public function assignTask(Request $request, User $user)
    {
        Activity::create([
            'user_id' => $user->id,
            'description' => 'Task assigned to ' . $user->name,
            'room' => null,
            'status' => 'Assigned',
        ]);

        return redirect()->route('admin.dashboard')->with('status', 'Task assigned to ' . $user->name);
    }

    /**
     * Store tugas submitted from tugas_admin form and redirect back to dashboard
     */
    public function storeTugas(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'room' => 'required|string|max:255',
            'time' => 'nullable|string|max:10',
            'message' => 'required|string|max:1000',
        ]);

        Activity::create([
            'user_id' => $data['user_id'] ?? null,
            'description' => $data['message'],
            'room' => $data['room'],
            'status' => 'Assigned',
        ]);

        return redirect()->route('admin.dashboard')->with('status', 'Tugas berhasil dikirim.');
    }

    /**
     * Render admin user management page
     */
    public function users()
    {
        try {
            $users = User::orderBy('name')->get();
            $dbAvailable = true;
        } catch (\Throwable $e) {
            $users = collect([
                (object)['id' => 1, 'name' => 'Ilyas Noor', 'email' => 'ilyas@example.com', 'role' => 'ra'],
            ]);
            $dbAvailable = false;
        }

        return view('admin.user_admin', compact('users', 'dbAvailable'));
    }

    /**
     * Update a user's role (supervisor / room attendant / manager)
     */
    public function updateUserRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'string', function($attr, $value, $fail){
                // store enum keys used in DB: 'ra' = room attendant
                $allowed = ['supervisor','ra','manager'];
                if (!in_array($value, $allowed)) $fail('Invalid role selected.');
            }]
        ]);

        $user->role = $data['role'];
        $user->save();

        return redirect()->route('admin.users')->with('status', 'Role updated for ' . $user->name);
    }
}


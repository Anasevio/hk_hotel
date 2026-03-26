<?php

namespace App\Http\Controllers\shared;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role; // supervisor, manager, ra

        $todayAtt = $user->todayAttendance;

        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        // Pakai satu query aggregate biar lebih efisien
        $rawStats = Attendance::where('user_id', $user->id)
            ->selectRaw("
                SUM(status = 'hadir')     as hadir,
                SUM(status = 'terlambat') as terlambat,
                SUM(status = 'izin')      as izin,
                SUM(status = 'sakit')     as sakit,
                SUM(status = 'alfa')      as alfa
            ")
            ->first();

        $stats = [
            'hadir'     => (int) $rawStats->hadir,
            'terlambat' => (int) $rawStats->terlambat,
            'izin'      => (int) $rawStats->izin,
            'sakit'     => (int) $rawStats->sakit,
            'alfa'      => (int) $rawStats->alfa,
        ];

        return view($role . '.attendance', compact('todayAtt', 'history', 'stats'));
    }

    public function checkIn()
    {
        $user = auth()->user();

        if ($user->todayAttendance) {
            return back()->with('error', 'Kamu sudah absen hari ini.');
        }

        $now      = Carbon::now();
        $jamMasuk = Carbon::parse('07:00');

        $isLate      = $now->gt($jamMasuk);
        $lateMinutes = $isLate ? $now->diffInMinutes($jamMasuk) : 0;

        Attendance::create([
            'user_id'      => $user->id,
            'date'         => today(),
            'check_in'     => $now,
            'status'       => $isLate ? 'terlambat' : 'hadir',
            'late_minutes' => $isLate ? $lateMinutes : null,
            'notes'        => $isLate ? "Terlambat {$lateMinutes} menit" : null,
        ]);

        return back()->with('success', 'Absen masuk berhasil.');
    }

    public function izin(Request $request)
    {
        $user = auth()->user();

        if ($user->todayAttendance) {
            return back()->with('error', 'Sudah ada absensi hari ini.');
        }

        if (Carbon::today()->isSaturday() || Carbon::today()->isSunday()) {
        return back()->with('error', 'Hari ini libur, tidak perlu absen.');
}

        $request->validate([
            'status' => 'required|in:izin,sakit',
            'notes'  => 'required|min:5|max:255',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'date'    => today(),
            'status'  => $request->status,
            'notes'   => $request->notes,
        ]);

        return back()->with('success', 'Berhasil mengajukan ' . $request->status . '.');
    }

    public function checkOut()
    {
        $user = auth()->user();
        $att  = $user->todayAttendance;

        if (!$att) {
            return back()->with('error', 'Belum absen masuk.');
        }

        if (!in_array($att->status, ['hadir', 'terlambat'])) {
            return back()->with('error', 'Absen keluar hanya untuk yang hadir.');
        }

        if ($att->check_out) {
            return back()->with('error', 'Sudah absen keluar.');
        }

        $att->update([
            'check_out' => now(),
        ]);

        return back()->with('success', 'Absen keluar berhasil.');
    }
}
<?php

namespace App\Http\Controllers\Ra;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $todayAtt = $user->todayAttendance;

        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        $stats = [
            'hadir' => Attendance::where('user_id', $user->id)->where('status', 'hadir')->count(),
            'izin'  => Attendance::where('user_id', $user->id)->where('status', 'izin')->count(),
            'sakit' => Attendance::where('user_id', $user->id)->where('status', 'sakit')->count(),
            'alfa'  => Attendance::where('user_id', $user->id)->where('status', 'alfa')->count(),
        ];

        return view('ra.attendance', compact('todayAtt', 'history', 'stats'));
    }

    public function checkIn(Request $request)
    {
        $user = auth()->user();

        if ($user->todayAttendance) {
            return back()->with('error', 'Kamu sudah absen hari ini.');
        }

        $request->validate([
            'status' => 'required|in:hadir,izin,sakit',
        ], [
            'status.required' => 'Pilih status kehadiran terlebih dahulu.',
        ]);

        $data = [
            'user_id' => $user->id,
            'date'    => today(),
            'status'  => $request->status,
            'notes'   => $request->notes,
        ];

        // Hanya hadir yang punya check_in
        if ($request->status === 'hadir') {
            $data['check_in'] = now()->format('H:i:s');
        }

        Attendance::create($data);

        $label = [
            'hadir' => 'Hadir — jangan lupa absen keluar ya!',
            'izin'  => 'Izin tercatat.',
            'sakit' => 'Sakit tercatat.',
        ][$request->status];

        return back()->with('success', "Absensi berhasil! $label");
    }

    public function checkOut()
    {
        $att = auth()->user()->todayAttendance;

        if (!$att) return back()->with('error', 'Belum absen masuk.');
        if ($att->status !== 'hadir') return back()->with('error', 'Absen keluar hanya untuk status Hadir.');
        if ($att->check_out) return back()->with('error', 'Sudah absen keluar.');

        $att->update(['check_out' => now()->format('H:i:s')]);

        return back()->with('success', 'Absen keluar berhasil!');
    }
}
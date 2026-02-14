<?php
namespace App\Http\Controllers\RA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // Halaman absensi + data riwayat & rekap
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $riwayat = Absensi::with('user')->orderBy('tanggal', 'desc')->get();
            $rekap = Absensi::selectRaw('status, COUNT(*) as total')
                        ->groupBy('status')
                        ->pluck('total', 'status');
        } else {
            $riwayat = Absensi::where('user_id', $user->id)
                        ->orderBy('tanggal', 'desc')->get();
            $rekap = Absensi::selectRaw('status, COUNT(*) as total')
                        ->where('user_id', $user->id)
                        ->groupBy('status')
                        ->pluck('total', 'status');
        }

        return view('RA.absensi_ra', compact('riwayat', 'rekap'));
    }

    // Simpan absensi
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        $sudahAbsen = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Sudah absen hari ini.');
        }

        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'jam_masuk' => $request->status === 'hadir' ? now()->format('H:i:s') : null,
        ]);

        return back()->with('success', 'Absensi berhasil.');
    }
}

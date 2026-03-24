<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Attendance, User};
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = $this->parseMonth($month);

        $staff = User::where('role', '!=', 'admin')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        // Satu query, dua kegunaan: $records untuk tabel detail,
        // $grouped untuk kalkulasi summary per user.
        $records = Attendance::with('user')
            ->whereYear('date', $year)
            ->whereMonth('date', $mon)
            ->orderBy('date', 'desc')
            ->get();

        $grouped = $records->groupBy('user_id');

        $summary = $staff->map(fn($u) => [
            'user'  => $u,
            'hadir' => $grouped->get($u->id, collect())->where('status', 'hadir')->count(),
            'izin'  => $grouped->get($u->id, collect())->where('status', 'izin')->count(),
            'sakit' => $grouped->get($u->id, collect())->where('status', 'sakit')->count(),
            'alfa'  => $grouped->get($u->id, collect())->where('status', 'alfa')->count(),
        ]);

        return view('admin.attendance', compact('summary', 'records', 'month', 'staff'));
    }

    public function export(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = $this->parseMonth($month);

        $records = Attendance::with('user')
            ->whereYear('date', $year)
            ->whereMonth('date', $mon)
            ->orderBy('date')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=rekap_{$month}.csv",
        ];

        return response()->stream(function () use ($records) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Tanggal', 'Nama', 'Role', 'Jam Masuk', 'Jam Keluar', 'Durasi', 'Status']);
            foreach ($records as $r) {
                fputcsv($h, [
                    $r->date->format('d/m/Y'),
                    $r->user->name,
                    ucfirst($r->user->role),
                    $r->check_in    ?? '-',
                    $r->check_out   ?? '-',
                    $r->work_duration ?? '-',
                    ucfirst($r->status),
                ]);
            }
            fclose($h);
        }, 200, $headers);
    }

    // ── Helpers ────────────────────────────────────────────────

    /**
     * Parse string 'Y-m' menjadi [$year, $month].
     * Dipakai di index() dan export() agar tidak duplikat.
     */
    private function parseMonth(string $month): array
    {
        return explode('-', $month);
    }
}
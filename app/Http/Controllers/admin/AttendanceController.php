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
        [$year, $mon] = explode('-', $month);
        $staff = User::where('role', '!=', 'admin')->orderBy('role')->orderBy('name')->get();
        $grouped = Attendance::with('user')->whereYear('date', $year)->whereMonth('date', $mon)
            ->orderBy('date', 'desc')->get()->groupBy('user_id');
        $summary = $staff->map(fn($u) => [
            'user'  => $u,
            'hadir' => $grouped->get($u->id, collect())->where('status', 'hadir')->count(),
            'izin'  => $grouped->get($u->id, collect())->where('status', 'izin')->count(),
            'sakit' => $grouped->get($u->id, collect())->where('status', 'sakit')->count(),
            'alfa'  => $grouped->get($u->id, collect())->where('status', 'alfa')->count(),
        ]);
        $records = Attendance::with('user')->whereYear('date', $year)->whereMonth('date', $mon)
            ->orderBy('date', 'desc')->get();
        return view('admin.attendance', compact('summary', 'records', 'month', 'staff'));
    }

    public function export(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);
        $records = Attendance::with('user')->whereYear('date', $year)->whereMonth('date', $mon)
            ->orderBy('date')->get();
        return response()->stream(function () use ($records) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Tanggal', 'Nama', 'Role', 'Shift', 'Jam Masuk', 'Jam Keluar', 'Durasi', 'Status']);
            foreach ($records as $r) {
                fputcsv($h, [
                    $r->date->format('d/m/Y'), $r->user->name, ucfirst($r->user->role),
                    ucfirst($r->user->shift), $r->check_in ?? '-', $r->check_out ?? '-',
                    $r->work_duration ?? '-', ucfirst($r->status),
                ]);
            }
            fclose($h);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=rekap_{$month}.csv"]);
    }
}

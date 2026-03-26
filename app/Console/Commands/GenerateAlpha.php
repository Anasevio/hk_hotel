<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class GenerateAlpha extends Command
{
    protected $signature   = 'attendance:alpa';
    protected $description = 'Generate alpha attendance for users who did not check in';

    public function handle()
    {
        $today = Carbon::today();

        // Skip Sabtu & Minggu — tidak ada kegiatan
        if ($today->isSaturday() || $today->isSunday()) {
            $this->info('Hari libur (' . $today->translatedFormat('l') . '), skip generate alfa.');
            return;
        }

        $users = User::all();

        foreach ($users as $user) {
            $already = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->exists();

            if (!$already) {
                Attendance::create([
                    'user_id' => $user->id,
                    'date'    => $today,
                    'status'  => 'alfa',
                    'notes'   => 'Tidak hadir tanpa keterangan',
                ]);
            }
        }

        $this->info('Auto alfa berhasil dijalankan.');
    }
}
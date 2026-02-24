<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAlphaAbsensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-alpha-absensi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $users = User::where('role', 'ra')->get();

    foreach ($users as $user) {

        $sudahAbsen = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if (!$sudahAbsen) {
            Absensi::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'status' => 'alpha',
                'jam_masuk' => null
            ]);
        }
    }
    }
}

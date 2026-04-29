<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Attendance::create([
                'user_id' => $user->id,
                'date' => now()->toDateString(),
                'check_in' => now()->format('H:i:s'),
                'check_out' => null,
                'status' => 'hadir',
                'late_minutes' => 0,
                'notes' => null,
            ]);

        }
    }
}   
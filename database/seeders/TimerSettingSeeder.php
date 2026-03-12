<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TimerSetting;

class TimerSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Timer berbeda-beda tergantung jenis status kamar
        // Admin bisa mengubah ini dari halaman pengaturan
        $settings = [
            ['key' => 'default',            'label' => 'Default',            'duration_minutes' => 45],
            ['key' => 'vacant_dirty',       'label' => 'Vacant Dirty',       'duration_minutes' => 45],
            ['key' => 'vacant_clean',       'label' => 'Vacant Clean',       'duration_minutes' => 30],
            ['key' => 'expected_departure', 'label' => 'Expected Departure', 'duration_minutes' => 40],
        ];

        foreach ($settings as $setting) {
            TimerSetting::create($setting);
        }
    }
}

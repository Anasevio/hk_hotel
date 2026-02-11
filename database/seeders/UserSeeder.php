<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'RA',
            'username' => 'ra',
            'password' => Hash::make('ra'),
            'role' => 'ra'
        ]);

        User::create([
            'name' => 'Supervisor',
            'username' => 'supervisor',
            'password' => Hash::make('supervisor'),
            'role' => 'supervisor'
        ]);

        User::create([
            'name' => 'Manager',
            'username' => 'manager',
            'password' => Hash::make('manager'),
            'role' => 'manager'
        ]);
    }
}

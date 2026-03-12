<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\User;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil RA untuk pre-assign beberapa kamar
        $ra1 = User::where('username', 'ra1')->first();
        $ra2 = User::where('username', 'ra2')->first();

        $rooms = [
            ['room_number' => '101', 'floor' => 1, 'room_type' => 'standard',   'status' => 'vacant_dirty',       'assigned_to' => $ra1?->id],
            ['room_number' => '102', 'floor' => 1, 'room_type' => 'standard',   'status' => 'vacant_clean',       'assigned_to' => null],
            ['room_number' => '103', 'floor' => 1, 'room_type' => 'deluxe',     'status' => 'vacant_ready',       'assigned_to' => null],
            ['room_number' => '104', 'floor' => 1, 'room_type' => 'standard',   'status' => 'occupied',           'assigned_to' => null],
            ['room_number' => '105', 'floor' => 1, 'room_type' => 'standard',   'status' => 'expected_departure', 'assigned_to' => null],
            ['room_number' => '201', 'floor' => 2, 'room_type' => 'deluxe',     'status' => 'vacant_dirty',       'assigned_to' => $ra2?->id],
            ['room_number' => '202', 'floor' => 2, 'room_type' => 'standard',   'status' => 'vacant_clean',       'assigned_to' => null],
            ['room_number' => '203', 'floor' => 2, 'room_type' => 'suite',      'status' => 'vacant_ready',       'assigned_to' => null],
            ['room_number' => '204', 'floor' => 2, 'room_type' => 'standard',   'status' => 'occupied',           'assigned_to' => null],
            ['room_number' => '205', 'floor' => 2, 'room_type' => 'deluxe',     'status' => 'vacant_dirty',       'assigned_to' => null],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}

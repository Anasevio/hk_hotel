<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();

            // Nomor kamar, contoh: 101, 102, dst
            $table->string('room_number')->unique();

            // Tipe kamar untuk info tambahan
            $table->enum('room_type', ['standard', 'deluxe', 'suite'])->default('standard');

            // Status kamar sesuai standar housekeeping:
            // vacant_dirty  = kosong & kotor, perlu dibersihkan
            // vacant_clean  = kosong & sudah bersih (belum diinspeksi)
            // vacant_ready  = kosong, bersih, sudah diinspeksi, siap ditempati
            // occupied      = sedang ditempati tamu
            // expected_departure = tamu akan check-out hari ini
            $table->enum('status', [
                'vacant_dirty',
                'vacant_clean',
                'vacant_ready',
                'occupied',
                'expected_departure'
            ])->default('vacant_dirty');

            // Siapa RA yang sedang ditugaskan ke kamar ini (nullable = belum ada)
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // Lantai kamar (untuk info display)
            $table->integer('floor')->default(1);

            // Catatan khusus kamar (misal: renovasi, AC rusak, dll)
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_status_logs', function (Blueprint $table) {
            $table->id();

            // Kamar mana yang berubah statusnya
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            // Siapa yang mengubah status (bisa RA, supervisor, atau manager)
            $table->foreignId('changed_by')->constrained('users');

            // Status sebelum diubah
            $table->enum('from_status', [
                'vacant_dirty', 'vacant_clean', 'vacant_ready',
                'occupied', 'expected_departure'
            ]);

            // Status setelah diubah
            $table->enum('to_status', [
                'vacant_dirty', 'vacant_clean', 'vacant_ready',
                'occupied', 'expected_departure'
            ]);

            // Alasan/konteks perubahan (misal: "Tamu check-out", "Inspeksi selesai")
            $table->string('reason')->nullable();

            // Relasi ke task jika perubahan ini berasal dari alur tugas
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_status_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Siapa yang absen
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Tanggal absensi
            $table->date('date');

            // Jam masuk
            $table->time('check_in')->nullable();

            // Jam keluar
            $table->time('check_out')->nullable();

            // Status: hadir / izin / sakit / alfa
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->default('hadir');

            // Catatan (misal alasan izin)
            $table->text('notes')->nullable();

            // Satu user hanya bisa punya 1 record per hari
            $table->unique(['user_id', 'date']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

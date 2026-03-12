<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_cases', function (Blueprint $table) {
            $table->id();

            // Kamar yang terkena special case
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            // Supervisor yang membuat special case
            $table->foreignId('created_by')->constrained('users');

            // RA yang ditugaskan menangani (boleh null)
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // Jenis kasus
            $table->enum('type', [
                'vip_room',          // Kamar VIP, standar ekstra
                'do_not_disturb',    // DND terlalu lama
                'guest_sick',        // Tamu sakit
                'damage_report',     // Ada kerusakan
                'lost_found',        // Barang hilang/ditemukan
                'other'              // Lainnya
            ]);

            // Deskripsi detail kasus
            $table->text('description');

            // Prioritas penanganan
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');

            // Status penanganan
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');

            // Catatan penyelesaian
            $table->text('resolution_notes')->nullable();

            // Kapan diselesaikan
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_cases');
    }
};

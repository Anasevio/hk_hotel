<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Kamar mana yang dikerjakan
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            // RA yang mengerjakan
            $table->foreignId('assigned_to')->constrained('users');

            // Supervisor yang memberikan tugas
            $table->foreignId('assigned_by')->constrained('users');

            // Status alur kerja tugas:
            // pending           = tugas dibuat, belum mulai
            // in_progress       = RA sedang mengerjakan
            // pending_supervisor = RA selesai, menunggu cek supervisor
            // returned_to_ra    = supervisor kembalikan ke RA karena ada masalah
            // pending_manager   = supervisor approve, menunggu cek manager
            // returned_to_supervisor = manager kembalikan ke supervisor
            // completed         = manager approve, selesai
            $table->enum('status', [
                'pending',
                'in_progress',
                'pending_supervisor',
                'returned_to_ra',
                'pending_manager',
                'returned_to_supervisor',
                'completed'
            ])->default('pending');

            // Kapan RA mulai mengerjakan (untuk hitung durasi)
            $table->timestamp('started_at')->nullable();

            // Kapan RA submit (selesai mengerjakan)
            $table->timestamp('submitted_at')->nullable();

            // Kapan supervisor approve
            $table->timestamp('supervisor_approved_at')->nullable();

            // Kapan manager approve (tugas benar-benar selesai)
            $table->timestamp('completed_at')->nullable();

            // Batas waktu pengerjaan dalam menit (diambil dari timer_settings)
            $table->integer('time_limit')->default(45);

            // Catatan dari supervisor ke RA saat dikembalikan
            $table->text('supervisor_note')->nullable();

            // Catatan dari manager ke supervisor saat dikembalikan
            $table->text('manager_note')->nullable();

            // Progress checklist 1 (persiapan alat) dalam persen
            $table->integer('checklist1_progress')->default(0);

            // Progress checklist 2 (pembersihan) dalam persen
            $table->integer('checklist2_progress')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

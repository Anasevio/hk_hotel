<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_checklists', function (Blueprint $table) {
            $table->id();

            // Milik tugas mana
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();

            // Tipe checklist:
            // preparation = checklist 1 (barang yang dibawa)
            // cleaning    = checklist 2 (langkah pembersihan)
            $table->enum('type', ['preparation', 'cleaning']);

            // Nama item checklist (misal: "Vacuum cleaner", "Sapu lantai", dll)
            $table->string('item_name');

            // Urutan tampil
            $table->integer('order')->default(0);

            // Sudah dicentang atau belum
            $table->boolean('is_checked')->default(false);

            // Kapan dicentang
            $table->timestamp('checked_at')->nullable();

            // Estimasi waktu untuk item ini (menit) — hanya untuk type cleaning
            $table->integer('estimated_minutes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_checklists');
    }
};

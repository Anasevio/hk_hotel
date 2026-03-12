<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timer_settings', function (Blueprint $table) {
            $table->id();

            // Nama setting (misal: "vacant_dirty", "vacant_clean", "default")
            $table->string('key')->unique();

            // Label tampil di UI
            $table->string('label');

            // Durasi dalam menit
            $table->integer('duration_minutes')->default(45);

            // Siapa yang terakhir ubah
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timer_settings');
    }
};

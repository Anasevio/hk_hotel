<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom ke tabel tasks
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            // 🧑‍💼 Supervisor yang approve
            $table->foreignId('supervisor_id')
                ->nullable()
                ->after('assigned_by')
                ->constrained('users')
                ->nullOnDelete();

            // 🧑‍💻 Manager yang approve final
            $table->foreignId('manager_id')
                ->nullable()
                ->after('supervisor_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Rollback (hapus kolom)
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            // hapus foreign key dulu
            $table->dropForeign(['supervisor_id']);
            $table->dropForeign(['manager_id']);

            // baru hapus kolom
            $table->dropColumn(['supervisor_id', 'manager_id']);
        });
    }
};
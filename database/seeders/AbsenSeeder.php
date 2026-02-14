
Schema::create('absensis', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->date('tanggal');
    $table->enum('status', ['hadir', 'izin', 'sakit']);
    $table->text('catatan')->nullable();
    $table->time('jam_masuk')->nullable();
    $table->timestamps();

    $table->unique(['user_id', 'tanggal']); // 1 user hanya boleh 1 absensi per hari
});

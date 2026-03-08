<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aduans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_kos')->nullable()->constrained('kos')->onDelete('set null');
            $table->string('judul');
            $table->text('pesan');
            $table->enum('kategori', ['fasilitas', 'lainnya'])->default('fasilitas');
            $table->enum('status', ['belum_dibaca', 'sudah_dibaca'])->default('belum_dibaca');
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduans');
    }
};

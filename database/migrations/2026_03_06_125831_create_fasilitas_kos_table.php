<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('fasilitas_kos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kos');
            $table->string('nama_fasilitas');
            $table->bigInteger('harga_tambahan')->default(0);
            $table->timestamps();

            $table->foreign('id_kos')->references('id')->on('kos')->onDelete('cascade');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas_kos');
    }
};

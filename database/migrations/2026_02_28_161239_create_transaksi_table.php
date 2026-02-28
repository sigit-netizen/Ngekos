<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_pembayaran')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_kamar')->constrained('kamar')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

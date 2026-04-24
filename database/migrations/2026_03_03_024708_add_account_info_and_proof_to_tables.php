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
        Schema::table('kos', function (Blueprint $table) {
            $table->string('no_rekening')->nullable()->after('alamat');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('batas_bayar');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('kos', function (Blueprint $table) {
            $table->dropColumn('no_rekening');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });
    }
};

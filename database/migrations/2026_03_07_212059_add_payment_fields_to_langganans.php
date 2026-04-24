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
        Schema::table('langganans', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('status');
            $table->string('metode_pembayaran')->nullable()->after('bukti_pembayaran');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('langganans', function (Blueprint $table) {
            $table->dropColumn(['bukti_pembayaran', 'metode_pembayaran']);
        });
    }
};

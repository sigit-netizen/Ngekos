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
            $table->date('jatuh_tempo')->nullable()->after('tanggal_pembayaran');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('langganans', function (Blueprint $table) {
            $table->dropColumn('jatuh_tempo');
        });
    }
};

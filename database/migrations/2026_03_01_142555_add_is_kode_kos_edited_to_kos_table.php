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
            $table->boolean('is_kode_kos_edited')->default(false)->after('kode_kos');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('kos', function (Blueprint $table) {
            $table->dropColumn('is_kode_kos_edited');
        });
    }
};

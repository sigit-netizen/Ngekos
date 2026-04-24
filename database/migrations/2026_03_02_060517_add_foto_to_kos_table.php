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
            $table->string('foto')->nullable()->after('kategori');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('kos', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};

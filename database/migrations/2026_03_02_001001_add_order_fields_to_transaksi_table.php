<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->bigInteger('kode_kos')->nullable()->after('id_kamar');
            $table->text('catatan')->nullable()->after('kode_kos');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['kode_kos', 'catatan']);
        });
    }
};

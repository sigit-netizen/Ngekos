<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->integer('durasi_sewa')->default(1)->after('status');
            $table->string('tipe_durasi')->default('bulan')->after('durasi_sewa'); // hari, minggu, bulan
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['durasi_sewa', 'tipe_durasi']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->bigInteger('kode_kos')->nullable()->after('jumlah_kamar');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kos')->nullable()->after('id_plans');
            $table->foreign('id_kos')->references('id')->on('kos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_kos']);
            $table->dropColumn('id_kos');
        });

        Schema::table('pending_users', function (Blueprint $table) {
            $table->dropColumn('kode_kos');
        });
    }
};

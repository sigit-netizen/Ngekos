<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->string('nik')->nullable()->change();
            $table->string('nomor_wa')->nullable()->change();
            $table->date('tanggal_lahir')->nullable()->change();
            $table->text('alamat')->nullable()->change();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->string('nik')->nullable(false)->change();
            $table->string('nomor_wa')->nullable(false)->change();
            $table->date('tanggal_lahir')->nullable(false)->change();
            $table->text('alamat')->nullable(false)->change();
        });
    }
};

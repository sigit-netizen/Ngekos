<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nomor_banks', function (Blueprint $table) {
            if (!Schema::hasColumn('nomor_banks', 'nama_pemilik')) {
                $table->string('nama_pemilik')->after('nomor_rekening')->nullable();
            }
            if (!Schema::hasColumn('nomor_banks', 'nama_bank_2')) {
                $table->string('nama_bank_2')->nullable();
            }
            if (!Schema::hasColumn('nomor_banks', 'nomor_rekening_2')) {
                $table->string('nomor_rekening_2')->nullable();
            }
            if (!Schema::hasColumn('nomor_banks', 'nama_pemilik_2')) {
                $table->string('nama_pemilik_2')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nomor_banks', function (Blueprint $table) {
            $table->dropColumn(['nama_pemilik', 'nama_bank_2', 'nomor_rekening_2', 'nama_pemilik_2']);
        });
    }
};

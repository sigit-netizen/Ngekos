<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Indexing Kamar table
        Schema::table('kamar', function (Blueprint $table) {
            $table->index('status');
            $table->index('id_kos');
        });

        // Indexing Transaksi table
        Schema::table('transaksi', function (Blueprint $table) {
            $table->index('status');
            $table->index('kode_kos');
            $table->index('id_user');
            $table->index('id_kamar');
        });

        // Indexing Pending Users table
        Schema::table('pending_users', function (Blueprint $table) {
            $table->index('status');
            $table->index('kode_kos');
        });
    }

    public function down(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['kode_kos']);
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['kode_kos']);
            $table->dropIndex(['id_user']);
            $table->dropIndex(['id_kamar']);
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['id_kos']);
        });
    }
};

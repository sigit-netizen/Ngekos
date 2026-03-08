<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aduans', function (Blueprint $table) {
            $table->index('id_user', 'aduans_user_id_idx');
            $table->index('kategori', 'aduans_kategori_idx');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->index('id_user', 'transaksi_user_id_idx');
            $table->index('status', 'transaksi_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aduans', function (Blueprint $table) {
            $table->dropIndex('aduans_user_id_idx');
            $table->dropIndex('aduans_kategori_idx');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropIndex('transaksi_user_id_idx');
            $table->dropIndex('transaksi_status_idx');
        });
    }
};

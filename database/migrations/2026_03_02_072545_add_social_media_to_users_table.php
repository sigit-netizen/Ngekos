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
        Schema::table('users', function (Blueprint $table) {
            $table->string('instagram')->nullable()->after('alamat');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('twitter');
            $table->string('tiktok')->nullable()->after('youtube');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['instagram', 'twitter', 'youtube', 'tiktok']);
        });
    }
};

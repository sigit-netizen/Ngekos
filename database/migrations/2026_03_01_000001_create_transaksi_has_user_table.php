<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi has user', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->bigInteger('id_user')->nullable();
            $table->bigInteger('id_transaksi')->nullable();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_transaksi')->references('id')->on('transaksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi has user');
    }
};

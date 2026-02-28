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
        Schema::create('pending_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nik');
            $table->string('nomor_wa');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->integer('id_plans'); // requested role (1: Anak Kos, 2: Pemilik Kos)
            $table->string('plan_type')->nullable(); // pro, premium, etc.
            $table->string('package_type')->nullable();
            $table->integer('jumlah_kamar')->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favorits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_kos')->constrained('kos')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['id_user', 'id_kos']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorits');
    }
};

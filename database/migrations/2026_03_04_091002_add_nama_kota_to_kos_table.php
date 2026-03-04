<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kos', function (Blueprint $table) {
            if (!Schema::hasColumn('kos', 'nama_kota')) {
                $table->string('nama_kota')->nullable()->index();
            }
        });

        // Populate nama_kota from existing kota column
        \DB::table('kos')->whereNull('nama_kota')->whereNotNull('kota')->update([
            'nama_kota' => \DB::raw('kota')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kos', function (Blueprint $table) {
            $table->dropColumn('nama_kota');
        });
    }
};

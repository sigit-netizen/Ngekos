<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // PostgreSQL enum creates a check constraint. When we switched to VARCHAR,
        // the constraint might still exist and only allow 'fasilitas' or 'lainnya'.
        // We need to drop it to allow 'tambah'.

        // This is for PostgreSQL
        DB::statement('ALTER TABLE aduans DROP CONSTRAINT IF EXISTS aduans_kategori_check');
    }

    public function down(): void
    {
        // No easy way to restore the exact check constraint without knowing the previous state,
        // but we can leave it as varchar for now.
    }
};

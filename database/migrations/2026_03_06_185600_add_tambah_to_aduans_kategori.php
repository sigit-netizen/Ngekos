<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Simple PostgreSQL-compatible way: convert to varchar
        DB::statement('ALTER TABLE aduans ALTER COLUMN kategori TYPE VARCHAR(255)');
    }

    public function down(): void
    {
        // Revert to original size if needed
        DB::statement('ALTER TABLE aduans ALTER COLUMN kategori TYPE VARCHAR(50)');
    }
};

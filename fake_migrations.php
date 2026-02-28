<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$toFake = [
    '2026_02_28_161237_create_cabang_kos_table',
    '2026_02_28_161237_create_plans_table',
    '2026_02_28_161238_create_fasilitas_table',
    '2026_02_28_161238_create_kamar_table',
    '2026_02_28_161238_create_kos_table',
    '2026_02_28_161239_create_transaksi_table',
    '2026_03_01_000001_create_transaksi_has_user_table',
];

$batch = DB::table('migrations')->max('batch') + 1;

foreach ($toFake as $m) {
    if (!DB::table('migrations')->where('migration', $m)->exists()) {
        DB::table('migrations')->insert([
            'migration' => $m,
            'batch' => $batch
        ]);
        echo "Faked migration: $m\n";
    }
}
echo "Migration synchronization completed.\n";

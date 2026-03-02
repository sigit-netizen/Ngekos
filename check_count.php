<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kos = App\Models\Kos::where('nama_kos', 'like', '%sigit%')->first();
if ($kos) {
    echo "Kos: " . $kos->nama_kos . " (ID: " . $kos->id . ")\n";
    echo "Kamar Count: " . $kos->kamars()->count() . "\n";
    foreach ($kos->kamars as $k) {
        echo "- Kamar: " . $k->nomor_kamar . " (ID: " . $k->id . ")\n";
    }
} else {
    echo "Kos not found\n";
}

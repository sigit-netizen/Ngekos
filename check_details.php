<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kos = App\Models\Kos::where('nama_kos', 'like', '%sigit%')->first();
if ($kos) {
    echo "Kos: " . $kos->nama_kos . " (ID: " . $kos->id . ")\n";
    $kamars = $kos->kamars;
    echo "Total Kamar: " . $kamars->count() . "\n";
    foreach ($kamars as $k) {
        echo "- ID: " . $k->id . " | No: " . $k->nomor_kamar . " | Status: '" . $k->status . "' | Harga: " . $k->harga . "\n";
    }
}

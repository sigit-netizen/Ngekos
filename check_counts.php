<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kos;
use App\Models\User;
use App\Models\Kamar;

$user = User::where('name', 'like', '%sigit%')->first();
if (!$user) {
    echo "User sigit not found\n";
    exit;
}

$kos = $user->kos()->first();
if (!$kos) {
    echo "Kos for sigit not found\n";
    exit;
}

$totalKamar = Kamar::where('id_kos', $kos->id)->count();
$totalPenyewa = User::where('id_kos', $kos->id)->where('status', 'active')->count();

echo "User: " . $user->name . "\n";
echo "Property: " . $kos->nama_kos . "\n";
echo "Total Kamar (Kapasitas): " . $totalKamar . "\n";
echo "Total Penyewa (Aktif): " . $totalPenyewa . "\n";
echo "Total Kos System: " . Kos::count() . "\n";
echo "Total Active Tenants System: " . User::where('status', 'active')->whereNotNull('id_kos')->count() . "\n";

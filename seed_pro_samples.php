<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Kos;
use App\Models\Kamar;

$userNames = ['Andi Owner Pro', 'Joko Owner Per Kamar Pro', 'petani', 'sigit'];

foreach ($userNames as $name) {
    $user = User::where('name', 'like', "%$name%")->first();
    if ($user) {
        if ($user->kos->count() == 0) {
            // Use user ID + some random digits to make it 5+ digits to avoid collisions
            $numericCode = (int) ($user->id . rand(100, 999));

            $kos = Kos::create([
                'nama_kos' => 'Sample Kos ' . $user->name,
                'alamat' => 'Lokasi Contoh ' . $user->name,
                'kode_kos' => $numericCode,
                'id_user' => $user->id
            ]);
            Kamar::create([
                'nomor_kamar' => '101',
                'status' => 'available',
                'harga' => 1500000,
                'id_kos' => $kos->id
            ]);
            Kamar::create([
                'nomor_kamar' => '102',
                'status' => 'available',
                'harga' => 2000000,
                'id_kos' => $kos->id
            ]);
            echo "Created sample for $name with code $numericCode\n";
        } else {
            echo "User $name already has " . $user->kos->count() . " kos\n";
        }
    } else {
        echo "User $name not found\n";
    }
}

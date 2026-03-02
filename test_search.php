<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\User\UserOrderController;
use Illuminate\Http\Request;

$controller = new UserOrderController();
$request = new Request(); // Empty request

$response = $controller->searchKos($request);
$data = json_decode($response->getContent(), true);

if ($data['success']) {
    foreach ($data['data'] as $kos) {
        if (strpos($kos['nama_kos'], 'sigit') !== false) {
            echo "Kos: " . $kos['nama_kos'] . " (ID: " . $kos['id'] . ")\n";
            echo "Room count in JSON: " . count($kos['kamars']) . "\n";
            echo "Badge Count (kamar_count): " . $kos['kamar_count'] . "\n";
            foreach ($kos['kamars'] as $k) {
                echo "- Kamar: " . $k['nomor_kamar'] . " (ID: " . $k['id'] . ")\n";
            }
        }
    }
} else {
    echo "Search failed: " . $data['message'] . "\n";
}

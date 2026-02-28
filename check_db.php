<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Langganan;

echo "--- User Status ---\n";
$users = User::select('id', 'name', 'status', 'id_plans')->get();
foreach ($users as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Status: {$u->status}, Plan: {$u->id_plans}\n";
}

echo "\n--- Langganan Status ---\n";
$packets = Langganan::with('user')->get();
foreach ($packets as $p) {
    $userName = $p->user ? $p->user->name : 'N/A';
    echo "ID: {$p->id}, User: {$userName}, Status: {$p->status}, Tgl: {$p->tanggal_pembayaran}\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'user17_1772327440@dummy.com';
$u = \App\Models\User::where('email', $email)->first();

if (!$u) {
    echo "User not found\n";
    exit;
}

echo "User ID: " . $u->id . "\n";
echo "Name: " . $u->name . "\n";
echo "ID Kamar: " . ($u->id_kamar ?? 'NULL') . "\n";
echo "ID Kos: " . ($u->id_kos ?? 'NULL') . "\n";
echo "Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
echo "Direct Permissions: " . implode(', ', $u->permissions->pluck('name')->toArray()) . "\n";
echo "All Permissions: " . implode(', ', $u->getAllPermissions()->pluck('name')->toArray()) . "\n";

$txs = \App\Models\Transaksi::where('id_user', $u->id)->get();
echo "Transactions:\n";
foreach ($txs as $t) {
    echo "- ID: {$t->id}, Status: {$t->status}, Tipe: {$t->tipe}, Kamar: {$t->id_kamar}\n";
}

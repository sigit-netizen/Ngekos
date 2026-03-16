<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::where('name', 'like', '%Dummy User 20%')->first();
if (!$u) {
    echo "User not found\n";
    exit;
}

echo "User: " . $u->name . "\n";
echo "Account Created: " . $u->created_at->toDateTimeString() . "\n";
echo "Room ID: " . ($u->id_kamar ?? 'NULL') . "\n";

$txs = \App\Models\Transaksi::where('id_user', $u->id)->orderBy('tanggal_pembayaran', 'desc')->get();
echo "Transactions (latest first):\n";
foreach ($txs as $t) {
    echo "- ID: {$t->id}, Status: {$t->status}, Date: " . ($t->tanggal_pembayaran ? $t->tanggal_pembayaran->toDateTimeString() : 'NULL') . ", Tipe: {$t->tipe}, Kamar: {$t->id_kamar}\n";
}

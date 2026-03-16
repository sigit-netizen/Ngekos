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

echo "Attempting robust eviction for user: {$u->email}\n";
if ($u->evict()) {
    echo "SUCCESS: User has been cleaned up.\n";
    
    // Refresh user to see changes
    $u->refresh();
    echo "New Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
    echo "New ID Kamar: " . ($u->id_kamar ?? 'NULL') . "\n";
    
    $txs = \App\Models\Transaksi::where('id_user', $u->id)->whereIn('status', ['pending', 'verified', 'paid'])->count();
    echo "Active Transactions Remaining: {$txs}\n";
} else {
    echo "FAILED: Eviction failed. Check logs.\n";
}

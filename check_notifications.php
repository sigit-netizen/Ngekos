<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \DB::table('notifications')->count();
echo "Number of recorded notifications: " . $count . "\n";

$notifs = \DB::table('notifications')->latest()->limit(5)->get();
foreach ($notifs as $n) {
    echo "ID: " . $n->id . " - Type: " . $n->type . " - Created: " . $n->created_at . "\n";
}

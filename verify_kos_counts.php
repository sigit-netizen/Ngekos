<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$userNames = ['Andi Owner Pro', 'Joko Owner Per Kamar Pro', 'petani', 'sigit'];

foreach ($userNames as $name) {
    $user = User::where('name', 'like', "%$name%")->first();
    if ($user) {
        echo $user->name . ": " . $user->kos->count() . " kos\n";
    } else {
        echo "User $name not found\n";
    }
}

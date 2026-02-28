<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Langganan;
use Illuminate\Support\Facades\DB;

try {
    DB::transaction(function () {
        // Delete langganan records for dummy users
        $dummyUserIds = User::where('email', 'like', '%@mail.com')->pluck('id');

        if ($dummyUserIds->isNotEmpty()) {
            $deletedLangganan = Langganan::whereIn('id_user', $dummyUserIds)->delete();
            echo "Deleted $deletedLangganan dummy langganan records.\n";

            $deletedUsers = User::whereIn('id', $dummyUserIds)->delete();
            echo "Deleted $deletedUsers dummy user records.\n";
        } else {
            echo "No dummy users (@mail.com) found.\n";
        }

        // Also cleanup other potentially dummy names if they exist
        $specificDummies = ['Budi Santoso'];
        foreach ($specificDummies as $name) {
            $user = User::where('name', $name)->first();
            if ($user) {
                $user->langganans()->delete();
                $user->delete();
                echo "Deleted dummy user: $name\n";
            }
        }
    });
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "Cleanup completed.\n";

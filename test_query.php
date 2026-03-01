<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;

$rolesQuery = Role::with('permissions')
    ->where('name', '!=', 'superadmin')
    ->where('name', '!=', 'admin');

$viewGroup = 'admin'; // Testing for the admin tab

if ($viewGroup === 'user') {
    $rolesQuery->whereIn('name', ['user', 'users']);
} else {
    $rolesQuery->whereIn('name', ['pro', 'premium', 'per_kamar_pro', 'per_kamar_premium', 'nonaktif']);
}

$roles = $rolesQuery->get();
echo "Roles found for '" . $viewGroup . "':\n";
foreach ($roles as $role) {
    echo "- " . $role->name . " (ID: " . $role->id . ")\n";
}
echo "Total: " . $roles->count() . "\n";

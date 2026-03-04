<?php
// Fix for Dummy Users and any users currently registered incorrectly.
$users = \App\Models\User::where('id_plans', 1)->get();
foreach ($users as $u) {
    if (is_null($u->id_kos)) {
        // Just a generic user finding kos
        $u->syncRoles(['user']);
        echo "Set {$u->name} to 'user' (User Umum)\n";
    } else {
        // Someone checked into a Kos
        $u->syncRoles(['users']);
        echo "Set {$u->name} to 'users' (Penyewa)\n";
    }
}
echo "Migration complete.\n";

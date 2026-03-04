<?php
$user = \App\Models\User::where('name', 'Dummy User 7')->first();
if ($user) {
    echo "User found: {$user->name} ({$user->email})\n";
    echo "Roles: \n";
    foreach ($user->roles as $role) {
        echo "- {$role->name}\n";
    }
} else {
    echo "User not found.\n";
}

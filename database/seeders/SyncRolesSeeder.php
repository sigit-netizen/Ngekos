<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SyncRolesSeeder extends Seeder
{
    /**
     * Run the database seeds to sync id_plans with Spatie Roles for existing users.
     */
    public function run(): void
    {
        $users = User::whereNotNull('id_plans')->get();

        $planMap = [
            2 => 'pro',
            3 => 'premium',
            4 => 'per_kamar_premium',
            5 => 'per_kamar_pro',
            6 => 'superadmin',
        ];

        foreach ($users as $user) {
            if (isset($planMap[$user->id_plans])) {
                $roleName = $planMap[$user->id_plans];

                // Ensure role exists before assigning
                if (Role::where('name', $roleName)->exists()) {
                    if (!$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                        $this->command->info("Assigned role '{$roleName}' to user: {$user->email}");
                    }
                }
            }
        }
    }
}

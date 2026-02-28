<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear ALL caches to prevent Spatie permission stale table reference after migrate:fresh
        Artisan::call('cache:clear');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call([
            JenisLanggananSeeder::class,
            PlansSeeder::class,
            DynamicPermissionSeeder::class,
            SuperAdminSeeder::class,
            SyncRolesSeeder::class,
            PendingUserSeeder::class,
        ]);
    }
}

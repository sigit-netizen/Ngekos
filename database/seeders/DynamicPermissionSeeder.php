<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DynamicPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Scan view directories (member and user factor)
        $directories = [
            resource_path('views/member'),
            resource_path('views/user')
        ];

        $permissions = [];

        foreach ($directories as $dirPath) {
            if (File::exists($dirPath)) {
                $files = File::files($dirPath);
                foreach ($files as $file) {
                    $filename = str_replace('.blade.php', '', $file->getFilename());
                    $permissionName = 'menu.' . $filename;

                    if (!in_array($permissionName, $permissions)) {
                        $permissions[] = $permissionName;
                        // Generate permission if not exist
                        Permission::firstOrCreate(['name' => $permissionName]);
                    }
                }
            }
        }

        // 1.5 Add Feature Permissions
        $featurePermissions = [
            'fitur.edit_profile'
        ];

        foreach ($featurePermissions as $fp) {
            if (!in_array($fp, $permissions)) {
                $permissions[] = $fp;
                Permission::firstOrCreate(['name' => $fp]);
            }
        }

        // 2. Ambil kategori roles dari tabel `plans` (jika ada isinya, buat role darisitu)
        // Kita juga pastikan role 'superadmin' ada mutlak
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $superAdminRole->syncPermissions(Permission::all());

        if (DB::getSchemaBuilder()->hasTable('plans')) {
            $plans = DB::table('plans')->get();

            foreach ($plans as $plan) {
                // Konversi nama plan ke format slug atau lowercase untuk nama Role
                $roleName = strtolower(str_replace(' ', '_', $plan->nama_plans));

                if ($roleName !== 'superadmin') {
                    $role = Role::firstOrCreate(['name' => $roleName]);
                    // Secara default berikan semua permission dulu agar bisa diedit di UI Superadmin
                    // Nantinya superadmin yang akan mengatur ulang/revoking di menu "Manage Permission"
                    $role->syncPermissions($permissions);
                }
            }
        } else {
            // Fallback jika tabel plans belum ada/kosong
            $memberRole = Role::firstOrCreate(['name' => 'member']);
            $memberRole->syncPermissions($permissions);

            $userRole = Role::firstOrCreate(['name' => 'user']);
            $userRole->syncPermissions($permissions);
        }

        // Always create 'users' and 'admin' roles (referenced by controllers)
        Role::firstOrCreate(['name' => 'users']);
        Role::firstOrCreate(['name' => 'admin']);
    }
}

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
        // Reset cache peran (roles) dan izin (permissions)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Pindai direktori view (pembeda member dan user)
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
                        // Buat izin (permission) jika belum ada
                        Permission::firstOrCreate(['name' => $permissionName]);
                    }
                }
            }
        }

        // 1.5 Tambahkan Izin Fitur (Feature Permissions)
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
        // Kita juga pastikan peran 'superadmin' ada secara mutlak
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $superAdminRole->syncPermissions(Permission::all());

        if (DB::getSchemaBuilder()->hasTable('plans')) {
            $plans = DB::table('plans')->get();

            foreach ($plans as $plan) {
                $roleName = strtolower(str_replace(' ', '_', $plan->nama_plans));

                if ($roleName !== 'superadmin') {
                    $role = Role::firstOrCreate(['name' => $roleName]);
                    // Hanya BERIKAN (GIVE) izin, jangan SYNC (karena akan menghapus yang sudah ada)
                    // Ini memastikan bahwa izin yang ditentukan pengguna di UI tetap terjaga
                    foreach ($permissions as $p) {
                        if (!$role->hasPermissionTo($p)) {
                            $role->givePermissionTo($p);
                        }
                    }
                }
            }
        } else {
            // Cadangan (Fallback)
            $memberRole = Role::firstOrCreate(['name' => 'member']);
            foreach ($permissions as $p) {
                if (!$memberRole->hasPermissionTo($p)) {
                    $memberRole->givePermissionTo($p);
                }
            }

            $userRole = Role::firstOrCreate(['name' => 'user']);
            foreach ($permissions as $p) {
                if (!$userRole->hasPermissionTo($p)) {
                    $userRole->givePermissionTo($p);
                }
            }
        }

        // Selalu buat peran 'users' dan 'admin' (direferensikan oleh controller)
        Role::firstOrCreate(['name' => 'users']);
        Role::firstOrCreate(['name' => 'admin']);
    }
}

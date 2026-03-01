<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class PermissionManagementController extends Controller
{
    public function index(Request $request)
    {
        // View Group: 'admin' (menampilkan Pro & Premium), 'user' (menampilkan User)
        $viewGroup = $request->query('view_group', 'admin');

        // Base Query: Pengecualian mutlak untuk superadmin, dan member/admin default
        $rolesQuery = Role::with('permissions')
            ->where('name', '!=', 'superadmin')
            ->where('name', '!=', 'admin');

        // Deteksi Permissions dari Direktori menggunakan Facade File
        $memberPerms = [];
        $userPerms = [];

        $memberPath = resource_path('views/member');
        if (\Illuminate\Support\Facades\File::exists($memberPath)) {
            foreach (\Illuminate\Support\Facades\File::files($memberPath) as $file) {
                $memberPerms[] = 'menu.' . str_replace('.blade.php', '', $file->getFilename());
            }
        }

        $userPath = resource_path('views/user');
        if (\Illuminate\Support\Facades\File::exists($userPath)) {
            foreach (\Illuminate\Support\Facades\File::files($userPath) as $file) {
                $userPerms[] = 'menu.' . str_replace('.blade.php', '', $file->getFilename());
            }
        }

        // Tampilkan Profile di kedua grup
        if (!in_array('menu.profil', $memberPerms)) {
            $memberPerms[] = 'menu.profil';
        }
        if (!in_array('menu.profil', $userPerms)) {
            $userPerms[] = 'menu.profil';
        }

        $allFilePerms = array_merge($memberPerms, $userPerms);

        if ($viewGroup === 'user') {
            // Tampilkan untuk tab user (termasuk role 'users' maupun 'user')
            $rolesQuery->whereIn('name', ['user', 'users']);
            // Filter hanya tabel menu.nama_file_user
            $permissions = Permission::whereIn('name', $userPerms)
                ->where('name', 'not like', 'fitur.%')
                ->orderBy('name', 'asc')->get();
        } else {
            // Tampilkan HANYA Plan ini di Tab Admin + role nonaktif
            $rolesQuery->whereIn('name', ['pro', 'premium', 'per_kamar_pro', 'per_kamar_premium', 'nonaktif']);

            // Filter tabel : menu.nama_file_member + permission custom manual
            $permissions = Permission::where(function ($query) use ($memberPerms, $allFilePerms) {
                $query->whereIn('name', $memberPerms)
                    ->orWhereNotIn('name', $allFilePerms);
            })
                ->where('name', 'not like', 'fitur.%')
                ->orderBy('name', 'asc')
                ->get();
        }

        // Force 'nonaktif' to be the first column for visibility, then other plans
        $roles = $rolesQuery->orderByRaw("CASE WHEN name = 'nonaktif' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->get();

        // Ambil semua permission terkait 'fitur'
        $featurePermissions = Permission::where('name', 'like', 'fitur.%')->orderBy('name', 'asc')->get();

        return view('superadmin.permission', compact('roles', 'permissions', 'featurePermissions', 'viewGroup'), ['role' => 'superadmin', 'title' => 'Manage Permission']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'role_permissions' => 'array',
        ]);

        DB::transaction(function () use ($request) {
            $roles = Role::where('name', '!=', 'superadmin')->get();

            foreach ($roles as $role) {
                $permissions = $request->role_permissions[$role->id] ?? [];
                $role->syncPermissions($permissions);
            }
        });

        // Hapus cache Spatie agar efek langsung dirasakan
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $activeFilter = $request->input('active_filter', 'admin');

        // Redirect dengan alert sukses
        return redirect()->route('superadmin.permission', ['view_group' => $activeFilter])->with('success', 'Hak akses berhasil diperbarui!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
            'kategori' => 'required|in:admin,user'
        ]);

        $permissionName = strtolower(str_replace(' ', '_', $request->name));
        $fullPermissionName = 'menu.' . $permissionName; // Sesuaikan standar penamaan seeder

        // 1. Buat permission baru jika belum ada
        if (!Permission::where('name', $fullPermissionName)->exists()) {
            Permission::create(['name' => $fullPermissionName]);
        }

        // 2. Buat 4 Permission CRUD Otomatis di Database
        $crudActions = ['create', 'read', 'update', 'delete'];
        foreach ($crudActions as $action) {
            $featureName = "fitur.{$action}_{$permissionName}";
            if (!Permission::where('name', $featureName)->exists()) {
                $createdFeature = Permission::create(['name' => $featureName]);

                // Assign create/read/update/delete to Superadmin otomatis
                $superadmin = Role::where('name', 'superadmin')->first();
                if ($superadmin) {
                    $superadmin->givePermissionTo($createdFeature->name);
                }
            }
        }

        // Coba assign otomatis menu base ke superadmin jika ada
        $superadmin = Role::where('name', 'superadmin')->first();
        if ($superadmin) {
            $superadmin->givePermissionTo($fullPermissionName);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Menu {$request->name} beserta 4 Hak Akses CRUD-nya berhasil ditambahkan ke database!");
    }

    public function destroy(Permission $permission)
    {
        $permissionName = str_replace('menu.', '', $permission->name);

        // Hapus juga 4 permission CRUD fiturnya
        $crudActions = ['create', 'read', 'update', 'delete'];
        foreach ($crudActions as $action) {
            $featureName = "fitur.{$action}_{$permissionName}";
            $featurePerm = Permission::where('name', $featureName)->first();
            if ($featurePerm) {
                $featurePerm->delete();
            }
        }

        // Hapus dari database (menu base)
        $permission->delete();

        // Bersihkan cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Akses menu '{$permissionName}' beserta file halamannya berhasil dihapus!");
    }
}

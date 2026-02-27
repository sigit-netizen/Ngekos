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

        $allFilePerms = array_merge($memberPerms, $userPerms);

        if ($viewGroup === 'user') {
            // Tampilkan untuk tab user (termasuk role 'users' maupun 'user')
            $rolesQuery->whereIn('name', ['user', 'users']);
            // Filter hanya tabel menu.nama_file_user
            $permissions = Permission::whereIn('name', $userPerms)->orderBy('name', 'asc')->get();
        } else {
            // Tampilkan HANYA 4 Plan ini di Tab Admin (Pilihan Pasti)
            $rolesQuery->whereIn('name', ['pro', 'premium', 'per_kamar_pro', 'per_kamar_premium']);

            // Filter tabel : menu.nama_file_member + permission custom manual
            $permissions = Permission::whereIn('name', $memberPerms)
                ->orWhereNotIn('name', $allFilePerms)
                ->orderBy('name', 'asc')
                ->get();
        }

        $roles = $rolesQuery->get();

        return view('superadmin.permission', compact('roles', 'permissions', 'viewGroup'), ['role' => 'superadmin', 'title' => 'Manage Permission']);
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

        // 2. Buat File Blade Kosong
        $kategori = $request->kategori;
        $folderPath = $kategori === 'admin' ? resource_path('views/member') : resource_path('views/user');

        // Pastikan folder exist, jika belum buat otomatis
        if (!\Illuminate\Support\Facades\File::isDirectory($folderPath)) {
            \Illuminate\Support\Facades\File::makeDirectory($folderPath, 0755, true, true);
        }

        $filePath = $folderPath . '/' . $permissionName . '.blade.php';

        // Check if file not exists, then generate boilerplate
        if (!\Illuminate\Support\Facades\File::exists($filePath)) {
            $uiTitle = ucwords(str_replace('_', ' ', $permissionName));
            $layoutPath = $kategori === 'admin' ? 'layouts.dashboard' : 'layouts.dashboard-user'; // Sesuaikan layout name

            $boilerplate = <<<HTML
@extends('{$layoutPath}')

@section('dashboard-content')
<div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Halaman {$uiTitle}</h1>
    <p class="text-gray-500">File view untuk halaman ini berada di {$kategori}/{$permissionName}.blade.php</p>
</div>

<!-- Mulai Isi Konten Anda Di Sini -->
<div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm" data-aos="fade-up" data-aos-delay="100">
    <p class="text-gray-600">Konten kosong. Silakan edit file blade ini.</p>
</div>
@endsection
HTML;
            \Illuminate\Support\Facades\File::put($filePath, $boilerplate);
        }

        // Coba assign otomatis ke superadmin jika ada
        $superadmin = Role::where('name', 'superadmin')->first();
        if ($superadmin) {
            $superadmin->givePermissionTo($fullPermissionName);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "File {$permissionName}.blade.php berhasil dibuat di dalam folder {$kategori} & Akses telah ditambahkan!");
    }

    public function destroy(Permission $permission)
    {
        $permissionName = str_replace('menu.', '', $permission->name);
        $memberPath = resource_path("views/member/{$permissionName}.blade.php");
        $userPath = resource_path("views/user/{$permissionName}.blade.php");

        // Hapus file blade jika ada
        if (\Illuminate\Support\Facades\File::exists($memberPath)) {
            \Illuminate\Support\Facades\File::delete($memberPath);
        }
        if (\Illuminate\Support\Facades\File::exists($userPath)) {
            \Illuminate\Support\Facades\File::delete($userPath);
        }

        // Hapus dari database
        $permission->delete();

        // Bersihkan cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', "Akses menu '{$permissionName}' beserta file halamannya berhasil dihapus!");
    }
}

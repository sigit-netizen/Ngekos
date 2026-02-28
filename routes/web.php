<?php

use Illuminate\Support\Facades\Route;
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Landing Page
Route::get('/', [\App\Http\Controllers\LandingPageController::class, 'index'])->name('home');

// Pending Verification Page
Route::get('/pending', function () {
    if (auth()->user()->status === 'active') {
        return redirect()->route(auth()->user()->hasRole('superadmin') ? 'superadmin.dashboard' : 'admin.dashboard');
    }
    return view('pending.dashboard');
})->middleware('auth')->name('pending.dashboard');

// Protected Admin Dashboard
Route::middleware(['auth', 'role:admin', 'check.subscription'])->group(function () {
    Route::get('/admin', function () {
        return view('member.dashboard', ['role' => 'admin']);
    })->name('admin.dashboard');

    Route::get('/admin/kamar', function () {
        return view('member.kamar', ['title' => 'Kamar', 'role' => 'admin']);
    })->name('admin.kamar');

    Route::get('/admin/data-penyewa', function () {
        return view('member.data_penyewa', ['title' => 'Data Penyewa', 'role' => 'admin']);
    })->name('admin.data_penyewa');

    Route::get('/admin/cabang-kos', function () {
        return view('member.cabang_kos', ['title' => 'Cabang Kos', 'role' => 'admin']);
    })->name('admin.cabang_kos');

    Route::get('/admin/pesan-aduan', function () {
        return view('member.pesan_aduan', ['title' => 'Pesan Aduan', 'role' => 'admin']);
    })->name('admin.pesan_aduan');

    Route::get('/admin/laporan-pembayaran', function () {
        return view('member.laporan_pembayaran', ['title' => 'Laporan Pembayaran', 'role' => 'admin']);
    })->name('admin.laporan_pembayaran');

    Route::get('/admin/tagihan-sistem', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'index'])->name('admin.tagihan_sistem');
    Route::put('/admin/tagihan-sistem', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'update'])->name('admin.subscription.update');

    Route::get('/admin/order', function () {
        return view('member.order', ['title' => 'Order', 'role' => 'admin']);
    })->name('admin.order');

    // Dynamic Route for automatically generated admin menus
    Route::get('/admin/{page}', function ($page) {
        if (view()->exists('member.' . $page)) {
            $title = ucwords(str_replace(['_', '-'], ' ', $page));
            return view('member.' . $page, ['title' => $title, 'role' => 'admin']);
        }
        abort(404);
    })->name('admin.dynamic');
});

// Protected User Dashboards (Anak Kos)
Route::middleware(['auth', 'role:users', 'check.subscription'])->group(function () {
    Route::get('/user', function () {
        return view('user.dashboard', ['role' => 'user']);
    })->name('user.dashboard');

    Route::get('/user/dashboard', function () {
        return view('user.dashboard', ['title' => 'Dashboard User', 'role' => 'user']);
    })->name('user.dashboard.detail');

    Route::get('/user/pesan', function () {
        return view('user.pesan', ['title' => 'Pesan', 'role' => 'user']);
    })->name('user.pesan');

    Route::get('/user/order', function () {
        return view('user.order', ['title' => 'Order', 'role' => 'user']);
    })->name('user.order');

    Route::get('/user/jatuh-tempo', function () {
        return view('user.jatuh_tempo', ['title' => 'Jatuh Tempo', 'role' => 'user']);
    })->name('user.jatuh_tempo');

    Route::get('/user/aduan', function () {
        return view('user.aduan', ['title' => 'Aduan Fasilitas', 'role' => 'user']);
    })->name('user.aduan');

    // Dynamic Route for automatically generated user menus
    Route::get('/user/{page}', function ($page) {
        if (view()->exists('user.' . $page)) {
            $title = ucwords(str_replace(['_', '-'], ' ', $page));
            return view('user.' . $page, ['title' => $title, 'role' => 'user']);
        }
        abort(404);
    })->name('user.dynamic');
});

// Other specific dashboard roles... (assuming these exists from existing code)
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/member', function () {
        return view('member.dashboard', ['role' => 'member']);
    })->name('member.dashboard');
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', [\App\Http\Controllers\Superadmin\DashboardController::class, 'index'])->name('superadmin.dashboard');

    Route::get('/superadmin/data-member', [\App\Http\Controllers\Superadmin\MemberManagementController::class, 'index'])->name('superadmin.data_member');
    Route::post('/superadmin/data-member', [\App\Http\Controllers\Superadmin\MemberManagementController::class, 'store'])->name('superadmin.data_member.store');
    Route::put('/superadmin/data-member/{user}', [\App\Http\Controllers\Superadmin\MemberManagementController::class, 'update'])->name('superadmin.data_member.update');
    Route::delete('/superadmin/data-member/{user}', [\App\Http\Controllers\Superadmin\MemberManagementController::class, 'destroy'])->name('superadmin.data_member.destroy');

    Route::get('/superadmin/data-user', [\App\Http\Controllers\Superadmin\UserManagementController::class, 'index'])->name('superadmin.data_user');
    Route::post('/superadmin/data-user', [\App\Http\Controllers\Superadmin\UserManagementController::class, 'store'])->name('superadmin.data_user.store');
    Route::put('/superadmin/data-user/{user}', [\App\Http\Controllers\Superadmin\UserManagementController::class, 'update'])->name('superadmin.data_user.update');
    Route::delete('/superadmin/data-user/{user}', [\App\Http\Controllers\Superadmin\UserManagementController::class, 'destroy'])->name('superadmin.data_user.destroy');

    Route::get('/superadmin/laporan-pembayaran', [\App\Http\Controllers\Superadmin\LaporanPembayaranController::class, 'index'])->name('superadmin.laporan_pembayaran');

    Route::get('/superadmin/order', [\App\Http\Controllers\Superadmin\OrderManagementController::class, 'index'])->name('superadmin.order');
    Route::post('/superadmin/order/user/{pendingUser}/verify', [\App\Http\Controllers\Superadmin\OrderManagementController::class, 'verifyUser'])->name('superadmin.order.user.verify');
    Route::post('/superadmin/order/user/{pendingUser}/reject', [\App\Http\Controllers\Superadmin\OrderManagementController::class, 'rejectUser'])->name('superadmin.order.user.reject');
    Route::post('/superadmin/order/packet/{subscription}/verify', [\App\Http\Controllers\Superadmin\OrderManagementController::class, 'verifyPacket'])->name('superadmin.order.verify');
    Route::post('/superadmin/order/packet/{subscription}/reject', [\App\Http\Controllers\Superadmin\OrderManagementController::class, 'rejectPacket'])->name('superadmin.order.reject');

    Route::get('/superadmin/permission', [\App\Http\Controllers\Superadmin\PermissionManagementController::class, 'index'])->name('superadmin.permission');
    Route::post('/superadmin/permission', [\App\Http\Controllers\Superadmin\PermissionManagementController::class, 'store'])->name('superadmin.permission.store');
    Route::put('/superadmin/permission', [\App\Http\Controllers\Superadmin\PermissionManagementController::class, 'update'])->name('superadmin.permission.update');
    Route::delete('/superadmin/permission/{permission}', [\App\Http\Controllers\Superadmin\PermissionManagementController::class, 'destroy'])->name('superadmin.permission.destroy');

    // Aduan Routes
    Route::get('/superadmin/aduan/member', function () {
        return view('superadmin.aduanMemeber', ['role' => 'superadmin', 'title' => 'Aduan Member']);
    })->name('superadmin.aduan.member');

    Route::get('/superadmin/aduan/user', function () {
        return view('superadmin.aduanUser', ['role' => 'superadmin', 'title' => 'Aduan User']);
    })->name('superadmin.aduan.user');

    Route::get('/superadmin/aduan/publik', function () {
        return view('superadmin.aduanPublik', ['role' => 'superadmin', 'title' => 'Aduan Publik']);
    })->name('superadmin.aduan.publik');
});

// Registration Pending Status Page (public, no auth required)
Route::get('/registration/pending', function (\Illuminate\Http\Request $request) {
    $pendingUser = \App\Models\PendingUser::where('email', $request->email)
        ->where('status', 'pending')
        ->first();

    if (!$pendingUser) {
        return redirect()->route('login');
    }

    return view('pending.dashboardPanding', ['pendingUser' => $pendingUser]);
})->name('registration.pending');

// Registration Rejected Status Page (public, no auth required)
Route::get('/registration/rejected', function (\Illuminate\Http\Request $request) {
    $pendingUser = \App\Models\PendingUser::where('email', $request->email)
        ->where('status', 'rejected')
        ->first();

    if (!$pendingUser) {
        return redirect()->route('login')->with('error', 'Data tidak ditemukan.');
    }

    return view('pending.dashboardDitolak', ['pendingUser' => $pendingUser]);
})->name('registration.rejected');

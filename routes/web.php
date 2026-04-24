<?php

use Illuminate\Support\Facades\Route;
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Rute Web (Web Routes)
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute-rute
| ini dimuat oleh RouteServiceProvider dalam grup yang berisi grup
| middleware "web". Sekarang, buat sesuatu yang luar biasa!
|
*/
// Halaman Utama (Landing Page)
Route::get('/', [\App\Http\Controllers\LandingPageController::class, 'index'])->name('home');

// Manajemen Kos (Kos Management)
Route::put('/admin/kos/{kos}', [\App\Http\Controllers\KosController::class, 'update'])->middleware('auth')->name('admin.kos.update');
Route::post('/search-kos', [\App\Http\Controllers\User\UserOrderController::class, 'searchKos'])->name('kos.search');

// Rute Profil (Profile Routes)
Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
Route::post('/profile/verify-password', [\App\Http\Controllers\ProfileController::class, 'verifyPassword'])->middleware('auth')->name('profile.verify-password');

// Halaman Verifikasi Tertunda (Pending Verification Page)
Route::get('/pending', function () {
    if (auth()->user()->status === 'active') {
        return redirect()->route(auth()->user()->hasRole('superadmin') ? 'superadmin.dashboard' : 'admin.dashboard');
    }
    return view('pending.dashboard');
})->middleware('auth')->name('pending.dashboard');

Route::post('/push-subscription', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])
    ->middleware('auth')
    ->name('push-subscription');

// Dashboard Admin Terproteksi (Protected Admin Dashboard)
Route::middleware(['auth', 'role:admin|nonaktif', 'check.subscription'])->group(function () {
    Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Manajemen Kamar (Kamar Management)
    Route::get('/admin/kamar', [\App\Http\Controllers\Admin\KamarController::class, 'index'])->name('admin.kamar');
    Route::post('/admin/kamar', [\App\Http\Controllers\Admin\KamarController::class, 'store'])->name('admin.kamar.store');
    Route::put('/admin/kamar/{kamar}', [\App\Http\Controllers\Admin\KamarController::class, 'update'])->name('admin.kamar.update');
    Route::put('/admin/kamar/{kamar}/fasilitas', [\App\Http\Controllers\Admin\KamarController::class, 'updateFasilitas'])->name('admin.kamar.updateFasilitas');
    Route::delete('/admin/kamar/{kamar}', [\App\Http\Controllers\Admin\KamarController::class, 'destroy'])->name('admin.kamar.destroy');

    Route::get('/admin/data-penyewa', [\App\Http\Controllers\Admin\PenyewaController::class, 'index'])->name('admin.data_penyewa');
    Route::post('/admin/penyewa/{user}/evict', [\App\Http\Controllers\Admin\PenyewaController::class, 'evict'])->name('admin.penyewa.evict');

    Route::get('/admin/cabang-kos', function () {
        return view('member.cabang_kos', ['title' => 'Cabang Kos', 'role' => 'admin']);
    })->name('admin.cabang_kos');

    // Manajemen Aduan (Aduan Management)
    Route::get('/admin/pesan-aduan', [\App\Http\Controllers\Admin\AduanFasilitasController::class, 'index'])->name('admin.pesan_aduan');
    Route::post('/admin/aduan/{aduan}/read', [\App\Http\Controllers\Admin\AduanFasilitasController::class, 'markRead'])->name('admin.aduan.read');
    Route::post('/admin/aduan/{aduan}/unread', [\App\Http\Controllers\Admin\AduanFasilitasController::class, 'markUnread'])->name('admin.aduan.unread');
    Route::delete('/admin/aduan/{aduan}', [\App\Http\Controllers\Admin\AduanFasilitasController::class, 'destroy'])->name('admin.aduan.destroy');

    Route::get('/admin/fasilitas', [\App\Http\Controllers\Admin\FasilitasKosController::class, 'index'])->name('admin.fasilitas');
    Route::post('/admin/fasilitas', [\App\Http\Controllers\Admin\FasilitasKosController::class, 'store'])->name('admin.fasilitas.store');
    Route::put('/admin/fasilitas/{fasilitas}', [\App\Http\Controllers\Admin\FasilitasKosController::class, 'update'])->name('admin.fasilitas.update');
    Route::delete('/admin/fasilitas/{fasilitas}', [\App\Http\Controllers\Admin\FasilitasKosController::class, 'destroy'])->name('admin.fasilitas.destroy');

    Route::get('/admin/aduan', fn() => redirect()->route('admin.pesan_aduan'));
    Route::get('/admin/pesan_aduan', fn() => redirect()->route('admin.pesan_aduan'));
    Route::get('/admin/laporan-pembayaran', [\App\Http\Controllers\Admin\LaporanPembayaranController::class, 'index'])->name('admin.laporan_pembayaran');

    Route::get('/admin/tagihan-sistem', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'index'])->name('admin.tagihan_sistem');
    Route::put('/admin/tagihan-sistem', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'update'])->name('admin.subscription.update');
    Route::post('/admin/tagihan-sistem/upload-proof', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'uploadProof'])->name('admin.subscription.upload-proof');

    Route::get('/admin/order', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.order');
    Route::post('/admin/order/{id}/verify', [\App\Http\Controllers\Admin\OrderController::class, 'verifyOrder'])->name('admin.order.verify');
    Route::post('/admin/order/{id}/confirm', [\App\Http\Controllers\Admin\OrderController::class, 'confirmPayment'])->name('admin.order.confirm');
    Route::post('/admin/order/{id}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'rejectOrder'])->name('admin.order.reject');

    // Verifikasi Penyewa (PendingUser Verification)
    Route::post('/admin/penyewa/{pendingUser}/verify', [\App\Http\Controllers\Admin\OrderController::class, 'verifyPenyewa'])->name('admin.penyewa.verify');
    Route::post('/admin/penyewa/{pendingUser}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'rejectPenyewa'])->name('admin.penyewa.reject');


    // Rute Dinamis untuk menu admin yang dibuat secara otomatis
    Route::get('/admin/{page}', function ($page) {
        // Cegah halaman yang memerlukan data controller agar tidak diakses tanpa data tersebut
        if (in_array($page, ['pesan_aduan'])) {
            abort(404);
        }
        if (view()->exists('member.' . $page)) {
            $title = ucwords(str_replace(['_', '-'], ' ', $page));
            return view('member.' . $page, ['title' => $title, 'role' => 'admin']);
        }
        abort(404);
    })->name('admin.dynamic');
});

// Dashboard Pengguna Terproteksi / Anak Kos (Protected User Dashboards)
Route::middleware(['auth', 'role:users|user', 'check.subscription'])->group(function () {
    Route::get('/user', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('user.dashboard');

    Route::get('/user/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('user.dashboard.detail');

    Route::get('/user/order', [\App\Http\Controllers\User\UserOrderController::class, 'index'])->name('user.order');
    Route::post('/user/order/search', [\App\Http\Controllers\User\UserOrderController::class, 'searchKos'])->name('user.order.search');
    Route::post('/user/order', [\App\Http\Controllers\User\UserOrderController::class, 'store'])->name('user.order.store');
    Route::post('/user/order/{transaksi}/cancel', [\App\Http\Controllers\User\UserOrderController::class, 'cancelOrder'])->name('user.order.cancel');
    Route::post('/user/order/{transaksi}/upload-proof', [\App\Http\Controllers\User\UserOrderController::class, 'uploadProof'])->name('user.order.upload-proof');
    Route::post('/user/kos/{id}/toggle-favorit', [\App\Http\Controllers\User\UserOrderController::class, 'toggleFavorit'])->name('user.kos.toggle-favorit');

    Route::get('/user/jatuh-tempo', [\App\Http\Controllers\User\JatuhTempoController::class, 'index'])->name('user.jatuh_tempo');
    Route::post('/user/jatuh-tempo', [\App\Http\Controllers\User\JatuhTempoController::class, 'store'])->name('user.jatuh_tempo.store');

    // Rute Fasilitas (Fasilitas routes)
    Route::get('/user/fasilitas', [\App\Http\Controllers\User\FasilitasController::class, 'index'])->name('user.fasilitas');
    Route::post('/user/fasilitas/aduan', [\App\Http\Controllers\User\FasilitasController::class, 'storeAduan'])->name('user.fasilitas.aduan');
    Route::post('/user/fasilitas/tambah', [\App\Http\Controllers\User\FasilitasController::class, 'storeTambah'])->name('user.fasilitas.tambah');

    // Rute Dinamis untuk menu pengguna yang dibuat secara otomatis
    Route::get('/user/{page}', function ($page) {
        if (view()->exists('user.' . $page)) {
            $title = ucwords(str_replace(['_', '-'], ' ', $page));
            return view('user.' . $page, ['title' => $title, 'role' => 'user']);
        }
        abort(404);
    })->name('user.dynamic');
});

// Peran dashboard spesifik lainnya... (berdasarkan kode yang sudah ada)
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
    Route::post('/superadmin/data-member/{user}/toggle', [\App\Http\Controllers\Superadmin\MemberManagementController::class, 'toggleStatus'])->name('superadmin.data_member.toggle');
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
    Route::post('/superadmin/order/member/{pendingUser}/confirm', [\App\Http\Controllers\Superadmin\OrderManagementController::class, 'confirmMemberPayment'])->name('superadmin.order.member.confirm');

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

    Route::post('/superadmin/user/{user}/deactivate', [\App\Http\Controllers\Superadmin\LaporanPembayaranController::class, 'deactivateUser'])->name('superadmin.user.deactivate');
});

// Halaman Status Pendaftaran Tertunda (Aman melalui Sesi)
Route::get('/registration/pending', function (\Illuminate\Http\Request $request) {
    $pendingUserId = session('pending_user_id');
    
    if (!$pendingUserId) {
        return redirect()->route('login');
    }

    $pendingUser = \App\Models\PendingUser::find($pendingUserId);

    if (!$pendingUser) {
        session()->forget('pending_user_id');
        return redirect()->route('login')->with('success', 'Pendaftaran Anda telah disetujui! Silakan masuk ke akun Anda.');
    }

    $plans = \Illuminate\Support\Facades\DB::table('plans')
        ->whereNotIn('nama_plans', ['Member', 'Superadmin'])
        ->get();

    // Ambil akun bank Superadmin untuk instruksi pembayaran
    $superadminBanks = \App\Models\User::role('superadmin')
        ->with('nomorBank')
        ->get()
        ->filter(fn($u) => $u->nomorBank)
        ->map(fn($u) => $u->nomorBank);

    // Hitung total pembayaran
    $totalPembayaran = 0;
    if ($pendingUser->plan_type) {
        $planName = trim($pendingUser->plan_type);
        $searchKey = $planName;
        
        // Petakan nama sederhana ke nama database jika diperlukan
        if (strtolower($planName) === 'pro') $searchKey = 'MEMBER PRO';
        if (strtolower($planName) === 'premium') $searchKey = 'MEMBER PREMIUM';

        $langganan = \Illuminate\Support\Facades\DB::table('jenis_langganans')
            ->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($searchKey) . '%'])
            ->first();

        if ($langganan) {
            $totalPembayaran = (float) $langganan->harga;
            if (str_contains(strtolower($planName), 'kamar')) {
                $totalPembayaran *= ($pendingUser->jumlah_kamar ?: 1);
            }
        }
    }

    if (!$pendingUser || !in_array($pendingUser->status, ['pending', 'verified', 'konfirmasi'])) {
        return redirect()->route('login');
    }

    return view('pending.dashboardPanding', [
        'pendingUser' => $pendingUser,
        'plans' => $plans,
        'totalPembayaran' => $totalPembayaran,
        'superadminBanks' => $superadminBanks
    ]);
})->name('registration.pending');

Route::post('/registration/upload-proof', [\App\Http\Controllers\Auth\PendingUserController::class, 'uploadProof'])->name('registration.upload-proof');

Route::post('/registration/cancel', function (\Illuminate\Http\Request $request) {
    $pendingUserId = session('pending_user_id');

    if (!$pendingUserId) {
        return redirect()->route('login');
    }

    $pendingUser = \App\Models\PendingUser::find($pendingUserId);

    if ($pendingUser && $pendingUser->status === 'pending') {
        $pendingUser->delete();
        session()->forget('pending_user_id');
        return redirect('/')->with('success', 'Pendaftaran Anda telah dibatalkan.');
    }
    return redirect()->route('login');
})->name('registration.cancel');

Route::post('/registration/step-one', [\App\Http\Controllers\Auth\PendingUserDashboardController::class, 'stepOne'])->name('registration.step-one');
Route::post('/registration/send-otp', [\App\Http\Controllers\Auth\PendingUserDashboardController::class, 'sendOtp'])->name('registration.send-otp');
Route::post('/registration/verify-otp', [\App\Http\Controllers\Auth\PendingUserDashboardController::class, 'verifyOtp'])->name('registration.verify-otp');

// Halaman Status Pendaftaran Ditolak (Aman melalui Sesi)
Route::get('/registration/rejected', function (\Illuminate\Http\Request $request) {
    $pendingUserId = session('pending_user_id');

    if (!$pendingUserId) {
        return redirect()->route('login');
    }

    $pendingUser = \App\Models\PendingUser::find($pendingUserId);

    if (!$pendingUser || $pendingUser->status !== 'rejected') {
        return redirect()->route('login');
    }

    return view('pending.dashboardDitolak', ['pendingUser' => $pendingUser]);
})->name('registration.rejected');

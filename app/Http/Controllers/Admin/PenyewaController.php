<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::user();
        $kos = Kos::where('id_user', $admin->id)->first();
        $status = $request->get('status', 'active');

        // Picu pengeluaran otomatis (auto-evict) di sini juga jika melihat penyewa aktif
        if ($kos && $status === 'active') {
            \App\Models\Transaksi::checkDeadAccounts($kos->kode_kos);
        }

        if (!$kos) {
            return view('member.data_penyewa', [
                'title' => 'Data Penyewa',
                'role' => 'admin',
                'penyewas' => collect(),
                'status' => $status
            ]);
        }

        if ($status === 'rejected') {
            $penyewas = \App\Models\PendingUser::where('kode_kos', $kos->kode_kos)
                ->where('status', 'rejected')
                ->latest()
                ->paginate(10);
        } else {
            $penyewas = User::where('id_kos', $kos->id)
                ->where('status', 'active')
                ->whereHas('transaksis', function($q) use ($kos) {
                    $q->where('kode_kos', $kos->kode_kos)
                      ->where('status', 'paid');
                })
                ->with('kamar')
                ->latest()
                ->paginate(10);
        }

        return view('member.data_penyewa', [
            'title' => 'Data Penyewa',
            'role' => 'admin',
            'penyewas' => $penyewas,
            'kos' => $kos,
            'status' => $status
        ]);
    }

    /**
     * Keluarkan penyewa secara manual.
     */
    public function evict(User $user)
    {
        $admin = Auth::user();
        $kos = Kos::where('id_user', $admin->id)->first();

        // Pemeriksaan keamanan (Security check)
        if (!$kos || $user->id_kos !== $kos->id) {
            return back()->with('error', 'Akses ditolak: Penyewa tidak terdaftar di kos Anda.');
        }

        if ($user->evict()) {
            return back()->with('success', "Penyewa {$user->name} berhasil dikeluarkan. Kamar telah dikosongkan.");
        }

        return back()->with('error', 'Gagal mengeluarkan penyewa.');
    }
}

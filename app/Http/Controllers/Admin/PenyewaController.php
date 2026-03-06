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
}

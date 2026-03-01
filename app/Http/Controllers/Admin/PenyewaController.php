<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        $kos = Kos::where('id_user', $admin->id)->first();

        if (!$kos) {
            return view('member.data_penyewa', [
                'title' => 'Data Penyewa',
                'role' => 'admin',
                'penyewas' => collect()
            ]);
        }

        $penyewas = User::where('id_kos', $kos->id)
            ->with('kamar')
            ->latest()
            ->paginate(10);

        return view('member.data_penyewa', [
            'title' => 'Data Penyewa',
            'role' => 'admin',
            'penyewas' => $penyewas,
            'kos' => $kos
        ]);
    }
}

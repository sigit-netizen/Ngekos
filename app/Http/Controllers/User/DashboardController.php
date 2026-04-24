<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Kos;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard pengguna (user dashboard).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isPenyewa = $user->isPenyewa();

        $data = [
            'role' => 'user',
            'user' => $user,
            'isPenyewa' => $isPenyewa,
            'title' => $request->routeIs('user.dashboard.detail') ? 'Dashboard User' : 'Dashboard',
        ];

        if ($isPenyewa) {
            $data['kosData'] = $user->kosAnak;
            $data['kamarData'] = $user->kamar;
        } else {
            // Logika untuk pengguna yang belum menyewa (rented)
            $latestOrder = Transaksi::where('id_user', $user->id)
                ->latest()
                ->with(['kamar.kos'])
                ->first();

            if (!$latestOrder) {
                $orderStatus = 'belum_order';
            } elseif ($latestOrder->status === 'pending') {
                $orderStatus = 'pending';
            } elseif ($latestOrder->status === 'verified') {
                $orderStatus = 'verified';
            } else {
                $orderStatus = 'belum_order';
            }

            $popularCities = Kos::whereNotNull('kota')
                ->where('kota', '!=', '')
                ->distinct()
                ->pluck('kota')
                ->toArray();

            if (empty($popularCities)) {
                $popularCities = ['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Malang', 'Semarang'];
            }

            $data['latestOrder'] = $latestOrder;
            $data['orderStatus'] = $orderStatus;
            $data['popularCities'] = $popularCities;
        }

        // Ambil kode_kos dari query string jika user datang dari landing page
        $data['intendedKos'] = $request->input('kode_kos');

        return view('user.dashboard', $data);
    }
}

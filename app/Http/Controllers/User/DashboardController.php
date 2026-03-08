<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Kos;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
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
            // Logic for users who haven't rented yet
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

        return view('user.dashboard', $data);
    }
}

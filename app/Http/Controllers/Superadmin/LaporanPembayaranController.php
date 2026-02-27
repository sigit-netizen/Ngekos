<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Langganan;
use Carbon\Carbon;

class LaporanPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $planFilter = $request->get('plan');
        $yearFilter = $request->get('year', date('Y'));
        $monthFilter = $request->get('month');
        $search = $request->get('search');

        // Fetch query with filters
        $query = Langganan::with(['jenis_langganan', 'user'])
            ->whereHas('user', function ($q) use ($search) {
                $q->role('admin');
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })
            ->whereNotNull('tanggal_pembayaran')
            ->whereYear('tanggal_pembayaran', $yearFilter);

        if ($monthFilter) {
            $query->whereMonth('tanggal_pembayaran', $monthFilter);
        }

        if ($planFilter) {
            $query->where('id_langganan', $planFilter);
        }

        $subscriptions = $query->orderBy('tanggal_pembayaran', 'desc')
            ->get()
            ->map(function ($sub) {
                $purchaseDate = Carbon::parse($sub->tanggal_pembayaran);
                $expiryDate = $purchaseDate->copy()->addDays(30);
                $daysRemaining = max(0, now()->diffInDays($expiryDate, false));

                $sub->expiry_date = $expiryDate;
                $sub->days_remaining = $daysRemaining;
                return $sub;
            });

        // Metrics
        $totalMember = \App\Models\User::role('admin')->count();
        $totalActive = $subscriptions->where('days_remaining', '>', 0)->count();

        $availablePlans = \App\Models\JenisLangganan::all();

        return view('superadmin.laporan_pembayaran', [
            'title' => 'Laporan Pembayaran Global',
            'subscriptions' => $subscriptions,
            'totalMember' => $totalMember,
            'totalActive' => $totalActive,
            'availablePlans' => $availablePlans,
            'selectedPlan' => $planFilter,
            'selectedYear' => $yearFilter,
            'selectedMonth' => $monthFilter,
            'search' => $search,
            'role' => 'superadmin'
        ]);
    }
}

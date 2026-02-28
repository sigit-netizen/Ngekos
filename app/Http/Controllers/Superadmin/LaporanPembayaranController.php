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
        $statusFilter = $request->get('status');
        $search = $request->get('search');

        // Fetch query with filters
        $query = Langganan::with(['jenis_langganan', 'user'])
            ->whereHas('user', function ($q) use ($search) {
                // In this context, 'admin' refers to the property owners (members)
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
                // Use the persistent jatuh_tempo column or fallback if NULL (for old data)
                // We use addMonth(1) for more natural expiry (e.g., 28 Jan -> 28 Feb)
                $expiryDate = $sub->jatuh_tempo ? Carbon::parse($sub->jatuh_tempo) : Carbon::parse($sub->tanggal_pembayaran)->addMonth(1);

                // Get raw difference to detect grace period (negative values)
                $diffDays = (int) now()->diffInDays($expiryDate, false);

                $sub->expiry_date = $expiryDate;
                $sub->days_remaining = $diffDays;

                // Determine precise status
                if ($diffDays > 0) {
                    $sub->computed_status = 'active';
                } elseif ($diffDays >= -3) {
                    $sub->computed_status = 'grace';
                } else {
                    $sub->computed_status = 'inactive';
                }

                return $sub;
            });

        // Apply status filtering on collection if selected
        if ($statusFilter) {
            $subscriptions = $subscriptions->where('computed_status', $statusFilter);
        }

        // Metrics
        $totalMember = \App\Models\User::role('admin')->count();
        $totalActive = $subscriptions->where('computed_status', 'active')->count();
        $totalGrace = $subscriptions->where('computed_status', 'grace')->count();

        $availablePlans = \App\Models\JenisLangganan::all();

        return view('superadmin.laporan_pembayaran', [
            'title' => 'Laporan Pembayaran Global',
            'subscriptions' => $subscriptions,
            'totalMember' => $totalMember,
            'totalActive' => $totalActive,
            'totalGrace' => $totalGrace,
            'availablePlans' => $availablePlans,
            'selectedPlan' => $planFilter,
            'selectedStatus' => $statusFilter,
            'selectedYear' => $yearFilter,
            'selectedMonth' => $monthFilter,
            'search' => $search,
            'role' => 'superadmin'
        ]);
    }
}

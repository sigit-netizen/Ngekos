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
                $q->role(['admin', 'nonaktif'])->with('statusUser');
                if ($search) {
                    $q->where('name', 'ilike', '%' . $search . '%');
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

        $subscriptions = $query->orderBy('tanggal_pembayaran', 'desc')->get()
            ->map(function ($sub) {
                // Eager loading should have fetched user.statusUser
                $userOfficialStatus = $sub->user->statusUser ? $sub->user->statusUser->status : 'aktif';

                // Use the persistent jatuh_tempo column or fallback if NULL (for old data)
                $expiryDate = $sub->jatuh_tempo ? Carbon::parse($sub->jatuh_tempo) : Carbon::parse($sub->tanggal_pembayaran)->addDays(28);

                // WIB Reset: Use Asia/Jakarta and compare pure dates (startOfDay)
                $nowWib = now('Asia/Jakarta')->startOfDay();
                $expiryWib = $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay();

                // Get raw difference in days
                $diffDays = (int) $nowWib->diffInDays($expiryWib, false);

                $sub->expiry_date = $expiryDate;
                $sub->days_remaining = $diffDays;

                // Determine precise status
                if ($diffDays >= 0) {
                    $sub->computed_status = 'active';
                } elseif ($diffDays >= -3) {
                    $sub->computed_status = 'grace';
                    $sub->grace_days_remaining = 3 - abs($diffDays) + 1;
                } else {
                    // It's technically expired (Mati)
                    // If the account is already officially deactivated, it should no longer show in the "Mati" list
                    if ($userOfficialStatus === 'inactive') {
                        $sub->computed_status = 'deactivated';
                    } else {
                        $sub->computed_status = 'inactive';
                        $sub->inactive_days_count = abs($diffDays) - 3;
                    }
                }

                return $sub;
            });

        // Metrics (Before status filtering for global accuracy)
        $totalMember = \App\Models\User::role(['admin', 'nonaktif'])->count();
        $totalActive = $subscriptions->where('computed_status', 'active')->count();
        $totalGrace = $subscriptions->where('computed_status', 'grace')->count();
        $totalInactive = $subscriptions->where('computed_status', 'inactive')->count();
        $totalDeactivated = $subscriptions->where('computed_status', 'deactivated')->count();

        // Apply status filtering on collection if selected for the table view
        if ($statusFilter) {
            $subscriptions = $subscriptions->where('computed_status', $statusFilter);
        }

        // Calculate filtered metrics BEFORE pagination
        $totalOrderCount = $subscriptions->count();
        $totalOmzetAmount = $subscriptions->sum(fn($s) => $s->jenis_langganan->harga);

        // Manual Pagination for the collection
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;

        // Auto-redirect if page is out of bounds (optional but helpful)
        $maxPage = max(1, ceil($totalOrderCount / $perPage));
        if ($currentPage > $maxPage) {
            return redirect()->route('superadmin.laporan_pembayaran', array_merge($request->except('page'), ['page' => 1]));
        }

        $items = $subscriptions->forPage($currentPage, $perPage)->values();
        $subscriptions = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $totalOrderCount,
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $availablePlans = \App\Models\JenisLangganan::all();

        return view('superadmin.laporan_pembayaran', [
            'title' => 'Laporan Pembayaran Global',
            'subscriptions' => $subscriptions,
            'totalMember' => $totalMember,
            'totalActive' => $totalActive,
            'totalGrace' => $totalGrace,
            'totalInactive' => $totalInactive,
            'totalDeactivated' => $totalDeactivated,
            'totalOrderCount' => $totalOrderCount,
            'totalOmzetAmount' => $totalOmzetAmount,
            'availablePlans' => $availablePlans,
            'selectedPlan' => $planFilter,
            'selectedStatus' => $statusFilter,
            'selectedYear' => $yearFilter,
            'selectedMonth' => $monthFilter,
            'search' => $search,
            'role' => 'superadmin'
        ]);
    }

    public function deactivateUser(\App\Models\User $user)
    {
        $user->deactivateStatus();

        return back()->with('success', "Member {$user->name} berhasil dinonaktifkan!");
    }
}

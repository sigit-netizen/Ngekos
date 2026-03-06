<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::user();
        $kos = Kos::where('id_user', $admin->id)->first();

        if (!$kos) {
            return view('member.laporan_pembayaran', [
                'title' => 'Laporan Pembayaran',
                'role' => 'admin',
                'tenants' => collect(),
                'metrics' => $this->emptyMetrics()
            ]);
        }

        $yearFilter = $request->get('year', date('Y'));
        $monthFilter = $request->get('month');
        $statusFilter = $request->get('status');
        $search = $request->get('search');

        // Fetch Users who have at least one paid transaction for this kos
        $query = \App\Models\User::where('id_kos', $kos->id)
            ->where('status', 'active')
            ->whereNotNull('id_kamar')
            ->whereHas('transaksis', function ($q) use ($kos) {
                $q->where('kode_kos', $kos->kode_kos)
                    ->where('status', 'paid');
            })
            ->with(['kamar', 'kosAnak']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('email', 'ilike', '%' . $search . '%');
            });
        }

        $allTenants = $query->get()->map(function ($user) use ($yearFilter, $monthFilter, $kos) {
            // Get the GLOBAL latest paid transaction for this user in this kos
            // If they have ANY successful transaction, they are a "Real Tenant"
            $latestTrx = Transaksi::where('id_user', $user->id)
                ->where('kode_kos', $kos->kode_kos)
                ->where('status', 'paid')
                ->orderBy('tanggal_pembayaran', 'desc')
                ->first();

            // Logic to calculate expiry based on duration in the transaction
            $paymentDate = $latestTrx ? Carbon::parse($latestTrx->tanggal_pembayaran) : null;
            $expiryDate = null;

            if ($latestTrx) {
                $duration = $latestTrx->durasi_sewa ?? 1;
                $unit = $latestTrx->tipe_durasi ?? 'bulan';

                if ($unit === 'bulan') {
                    $expiryDate = $paymentDate->copy()->addMonths($duration);
                } else {
                    $expiryDate = $paymentDate->copy()->addDays($duration);
                }
            }

            $nowWib = now('Asia/Jakarta')->startOfDay();
            $expiryWib = $expiryDate ? $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay() : null;

            $diffDays = $expiryWib ? (int) $nowWib->diffInDays($expiryWib, false) : -999;

            $user->latest_trx = $latestTrx;
            $user->expiry_date = $expiryDate;
            $user->days_remaining = $diffDays;

            // Determine status
            if ($diffDays >= 0) {
                $user->computed_status = 'active';
            } elseif ($diffDays >= -3) {
                $user->computed_status = 'grace';
                $user->grace_days_remaining = 3 - abs($diffDays) + 1;
            } else {
                $user->computed_status = 'expired';
                $user->inactive_days_count = abs($diffDays) - 3;
            }

            return $user;
        });

        // We show all active tenants found in the step above

        $metrics = [
            'total_penyewa' => $allTenants->count(),
            'penyewa_aktif' => $allTenants->where('computed_status', 'active')->count(),
            'masa_tenggang' => $allTenants->where('computed_status', 'grace')->count(),
            'sewa_habis' => $allTenants->where('computed_status', 'expired')->count(),
            'total_omzet' => Transaksi::where('kode_kos', $kos->kode_kos)
                ->where('status', 'paid')
                ->whereYear('created_at', $yearFilter)
                ->when($monthFilter, fn($q) => $q->whereMonth('created_at', $monthFilter))
                ->sum('jumlah_bayar'),
            'paid_count' => Transaksi::where('kode_kos', $kos->kode_kos)
                ->where('status', 'paid')
                ->whereYear('created_at', $yearFilter)
                ->when($monthFilter, fn($q) => $q->whereMonth('created_at', $monthFilter))
                ->count(),
            'pending_count' => Transaksi::where('kode_kos', $kos->kode_kos)
                ->where('status', 'pending')
                ->whereYear('created_at', $yearFilter)
                ->when($monthFilter, fn($q) => $q->whereMonth('created_at', $monthFilter))
                ->count(),
            'failed_count' => Transaksi::where('kode_kos', $kos->kode_kos)
                ->whereIn('status', ['failed', 'rejected'])
                ->whereYear('created_at', $yearFilter)
                ->when($monthFilter, fn($q) => $q->whereMonth('created_at', $monthFilter))
                ->count(),
        ];

        if ($statusFilter) {
            $allTenants = $allTenants->where('computed_status', $statusFilter);
        }

        // Manual Pagination
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $items = $allTenants->forPage($currentPage, $perPage)->values();
        $tenants = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allTenants->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('member.laporan_pembayaran', [
            'title' => 'Laporan Sewa Penyewa',
            'role' => 'admin',
            'tenants' => $tenants,
            'metrics' => $metrics,
            'selectedYear' => $yearFilter,
            'selectedMonth' => $monthFilter,
            'selectedStatus' => $statusFilter,
            'search' => $search,
            'kos' => $kos
        ]);
    }

    private function emptyMetrics()
    {
        return [
            'total_penyewa' => 0,
            'penyewa_aktif' => 0,
            'kamar_terisi' => 0,
            'total_omzet' => 0,
            'total_transactions' => 0,
            'paid_count' => 0,
            'pending_count' => 0,
            'failed_count' => 0,
        ];
    }
}

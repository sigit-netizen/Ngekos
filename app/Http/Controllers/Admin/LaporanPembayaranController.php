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
                'tenants' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'metrics' => $this->emptyMetrics(),
                'selectedYear' => $request->get('year', date('Y')),
                'selectedMonth' => $request->get('month'),
                'selectedStatus' => $request->get('status'),
                'selectedDurationType' => $request->get('duration_type'),
                'search' => $request->get('search'),
                'kos' => null
            ]);
        }

        $yearFilter = $request->get('year', date('Y'));
        $monthFilter = $request->get('month');
        $statusFilter = $request->get('status');
        $search = $request->get('search');
        $durationTypeFilter = $request->get('duration_type');

        // Ambil pengguna yang memiliki setidaknya satu transaksi berbayar untuk kos ini
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
            // Dapatkan transaksi berbayar terbaru secara GLOBAL untuk pengguna ini di kos ini
            // Jika mereka memiliki transaksi yang berhasil (ANY successful transaction), mereka adalah "Penyewa Asli"
            $latestTrx = Transaksi::where('id_user', $user->id)
                ->where('kode_kos', $kos->kode_kos)
                ->where('status', 'paid')
                ->orderBy('tanggal_pembayaran', 'desc')
                ->first();

            // Logika untuk menghitung masa kedaluwarsa (expiry) berdasarkan durasi dalam transaksi
            $paymentDate = $latestTrx ? Carbon::parse($latestTrx->tanggal_pembayaran) : null;
            $expiryDate = null;

            if ($latestTrx) {
                if ($latestTrx->jatuh_tempo) {
                    $expiryDate = Carbon::parse($latestTrx->jatuh_tempo);
                } else {
                    $duration = $latestTrx->durasi_sewa ?? 1;
                    $type = $latestTrx->tipe_durasi ?? 'bulan';

                    if ($type === 'hari') {
                        $expiryDate = $paymentDate->copy()->addDays($duration);
                    } elseif ($type === 'minggu') {
                        $expiryDate = $paymentDate->copy()->addWeeks($duration);
                    } else { // Default ke 'bulan' atau jenis durasi lain yang tidak dikenali
                        $expiryDate = $paymentDate->copy()->addDays($duration * 30);
                    }
                }
            }

            $nowWib = now('Asia/Jakarta')->startOfDay();
            $expiryWib = $expiryDate ? $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay() : null;

            $diffDays = $expiryWib ? (int) $nowWib->diffInDays($expiryWib, false) : -999;

            $user->latest_trx = $latestTrx;
            $user->expiry_date = $expiryDate;
            $user->days_remaining = $diffDays;

            // Tentukan status
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

        // Kami menampilkan semua penyewa aktif yang ditemukan pada langkah di atas

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
        ];

        if ($statusFilter) {
            $allTenants = $allTenants->where('computed_status', $statusFilter);
        }

        if ($durationTypeFilter) {
            $allTenants = $allTenants->filter(function ($user) use ($durationTypeFilter) {
                return $user->latest_trx && $user->latest_trx->tipe_durasi === $durationTypeFilter;
            });
        }

        // Penomoran Halaman Manual (Manual Pagination)
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
            'selectedDurationType' => $durationTypeFilter,
            'search' => $search,
            'kos' => $kos
        ]);
    }

    private function emptyMetrics()
    {
        return [
            'total_penyewa' => 0,
            'penyewa_aktif' => 0,
            'masa_tenggang' => 0,
            'sewa_habis' => 0,
            'total_omzet' => 0,
        ];
    }
}

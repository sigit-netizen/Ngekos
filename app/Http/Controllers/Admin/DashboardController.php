<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kos;
use App\Models\Transaksi;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $kos = $user->kos()->with('kamars')->first();

        $stats = [
            'minHarga' => 0,
            'maxHarga' => 0,
            'totalKamar' => 0,
            'totalPenyewa' => 0,
            'sisaKuota' => 0,
            'limitKamar' => 0,
            'isPerKamar' => in_array($user->id_plans, [4, 5]),
        ];

        if ($kos) {
            $stats['minHarga'] = $kos->kamars->min('harga') ?? 0;
            $stats['maxHarga'] = $kos->kamars->max('harga') ?? 0;
            $stats['totalKamar'] = $kos->kamars->count();

            $stats['totalPenyewa'] = User::where('id_kos', $kos->id)
                ->where('status', 'active')
                ->whereHas('transaksis', function ($q) use ($kos) {
                    $q->where('kode_kos', $kos->kode_kos)
                        ->where('status', 'paid');
                })
                ->count();

            $activeSubscription = $user->langganans()->where('status', 'active')->latest()->first();
            if ($stats['isPerKamar'] && $activeSubscription) {
                $stats['limitKamar'] = $activeSubscription->jumlah_kamar;
                $stats['sisaKuota'] = $stats['limitKamar'] - $stats['totalKamar'];
            }

            // Count for 'Konfirmasi' (Sudah upload bukti tapi belum dikonfirmasi admin) - Initial Booking
            $stats['orderKonfirmasiCount'] = Transaksi::where('kode_kos', $kos->kode_kos)
                ->where('status', 'verified')
                ->where('tipe', Transaksi::TYPE_BOOKING)
                ->whereNotNull('bukti_pembayaran')
                ->count();

            // Count for 'Konfirmasi' - Recurring Rent
            $stats['rentKonfirmasiCount'] = Transaksi::where('kode_kos', $kos->kode_kos)
                ->where('status', 'pending')
                ->where('tipe', Transaksi::TYPE_SEWA)
                ->whereNotNull('bukti_pembayaran')
                ->count();

            // Count pending penyewa from pending_users table who registered with this kos's kode_kos
            $stats['pendingCount'] = PendingUser::where('kode_kos', $kos->kode_kos)
                ->where('status', 'pending')
                ->count();
        }

        // Available years for filter (2026 - 2035)
        $years = range(2026, 2035);

        // Data for Tenant Chart
        $chartData = $this->getChartData($kos, $request);

        return view('member.dashboard', [
            'role' => 'admin',
            'kos' => $kos,
            'stats' => $stats,
            'chart' => $chartData,
            'years' => $years,
            'selectedYear' => $request->get('year', date('Y')),
            'selectedMonth' => $request->get('month'),
            'maxCapacity' => $stats['totalKamar'] > 0 ? $stats['totalKamar'] : 10
        ]);
    }

    private function getChartData($kos, Request $request)
    {
        $labels = [];
        $data = [];

        if (!$kos) {
            return ['labels' => [], 'data' => []];
        }

        $year = (int) $request->get('year', date('Y'));
        $month = $request->get('month') ? (int) $request->get('month') : null;
        $now = Carbon::now();

        if ($month) {
            // Data per day for selected month
            $targetMonth = Carbon::create($year, $month);
            $daysInMonth = $targetMonth->daysInMonth;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = Carbon::create($year, $month, $i);
                $labels[] = $i;

                // If date is in the future, return null to break the line
                if ($date->gt($now->endOfDay())) {
                    $data[] = null;
                    continue;
                }

                $count = User::where('id_kos', $kos->id)
                    ->where('status', 'active')
                    ->whereHas('transaksis', function ($q) use ($kos) {
                        $q->where('kode_kos', $kos->kode_kos)
                            ->where('status', 'paid');
                    })
                    ->where('created_at', '<=', $date->endOfDay())
                    ->count();
                $data[] = $count;
            }
        } else {
            // Data per month for selected year
            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::create($year, $i)->endOfMonth();
                $labels[] = Carbon::create($year, $i)->translatedFormat('M');

                // If the start of this month is in the future, return null
                if (Carbon::create($year, $i, 1)->gt($now)) {
                    $data[] = null;
                    continue;
                }

                $count = User::where('id_kos', $kos->id)
                    ->where('status', 'active')
                    ->whereHas('transaksis', function ($q) use ($kos) {
                        $q->where('kode_kos', $kos->kode_kos)
                            ->where('status', 'paid');
                    })
                    ->where('created_at', '<=', $date)
                    ->count();
                $data[] = $count;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}

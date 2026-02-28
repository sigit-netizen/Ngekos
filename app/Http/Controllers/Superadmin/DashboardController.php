<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Langganan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('year', date('Y'));
        $selectedMonth = $request->get('month', '');   // empty = show all months
        $statusFilter = $request->get('status_member', 'all');

        // ── Statistics ────────────────────────────────────────────────
        $totalUsers = User::role('users')->count();
        $totalMembers = User::role('admin')->count();

        // Active members = admin users who have at least one active subscription
        $activeMemberIds = Langganan::where('status', 'active')->distinct()->pluck('id_user');
        $activeMembersCount = User::role('admin')->whereIn('id', $activeMemberIds)->count();
        $totalOverall = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'superadmin'))->count();

        // ── Chart Mode ────────────────────────────────────────────────
        // month selected → daily breakdown; otherwise → monthly breakdown
        if ($selectedMonth) {
            // Daily breakdown within the selected month
            $chartMode = 'daily';

            $usersGrowth = User::role('users')
                ->select(
                    DB::raw('count(id) as total'),
                    DB::raw("TO_CHAR(created_at, 'DD') as day"),
                    DB::raw('max(created_at) as date')
                )
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->groupBy('day')
                ->orderBy('date', 'ASC')
                ->get()
                ->pluck('total', 'day');

            $membersGrowth = User::role('admin')
                ->select(
                    DB::raw('count(id) as total'),
                    DB::raw("TO_CHAR(created_at, 'DD') as day"),
                    DB::raw('max(created_at) as date')
                )
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->groupBy('day')
                ->orderBy('date', 'ASC')
                ->get()
                ->pluck('total', 'day');

            // Build full day range for the month
            $daysInMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->daysInMonth;
            $labels = [];
            $userGrowthData = [];
            $memberGrowthData = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $key = str_pad($d, 2, '0', STR_PAD_LEFT);
                $labels[] = $d;
                $userGrowthData[] = (int) ($usersGrowth[$key] ?? 0);
                $memberGrowthData[] = (int) ($membersGrowth[$key] ?? 0);
            }
        } else {
            // Monthly breakdown across the year
            $chartMode = 'monthly';

            $usersGrowth = User::role('users')
                ->select(
                    DB::raw('count(id) as total'),
                    DB::raw("TO_CHAR(created_at, 'FMMonth') as month"),
                    DB::raw("EXTRACT(MONTH FROM created_at) as month_num"),
                    DB::raw('max(created_at) as date')
                )
                ->whereYear('created_at', $selectedYear)
                ->groupBy('month', 'month_num')
                ->orderBy('month_num', 'ASC')
                ->get()
                ->pluck('total', 'month');

            $membersGrowth = User::role('admin')
                ->select(
                    DB::raw('count(id) as total'),
                    DB::raw("TO_CHAR(created_at, 'FMMonth') as month"),
                    DB::raw("EXTRACT(MONTH FROM created_at) as month_num"),
                    DB::raw('max(created_at) as date')
                )
                ->whereYear('created_at', $selectedYear)
                ->groupBy('month', 'month_num')
                ->orderBy('month_num', 'ASC')
                ->get()
                ->pluck('total', 'month');

            // Build full 12-month labels
            $labels = [];
            $userGrowthData = [];
            $memberGrowthData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthName = Carbon::createFromDate($selectedYear, $m, 1)->format('F');
                $labels[] = $monthName;
                $userGrowthData[] = (int) ($usersGrowth[$monthName] ?? 0);
                $memberGrowthData[] = (int) ($membersGrowth[$monthName] ?? 0);
            }
        }

        return view('superadmin.dashboard', [
            'title' => 'Dashboard Superadmin',
            'role' => 'superadmin',
            'stats' => [
                'total_users' => $totalUsers,
                'total_members' => $totalMembers,
                'active_members' => $activeMembersCount,
                'total_overall' => $totalOverall,
            ],
            'chart' => [
                'labels' => $labels,
                'user_growth' => $userGrowthData,
                'member_growth' => $memberGrowthData,
                'mode' => $chartMode,
            ],
            'filters' => [
                'month' => $selectedMonth,
                'year' => $selectedYear,
                'status_member' => $statusFilter,
            ],
        ]);
    }
}

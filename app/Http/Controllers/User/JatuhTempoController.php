<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Kamar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JatuhTempoController extends Controller
{
    /**
     * Display the jatuh tempo page with premium UI and grace period logic.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Fetch latest active/paid rental transaction
        $lastPaidRent = Transaksi::where('id_user', $user->id)
            ->where('status', 'paid')
            ->whereIn('tipe', [Transaksi::TYPE_BOOKING, Transaksi::TYPE_SEWA])
            ->latest()
            ->first();

        // 2. Calculate Expiry Date
        $expiryDate = null;
        if ($lastPaidRent) {
            // Prioritize the persistent column for manual testing/overrides
            if ($lastPaidRent->jatuh_tempo) {
                $expiryDate = Carbon::parse($lastPaidRent->jatuh_tempo);
            } else {
                // Fallback to dynamic calculation
                $paymentDate = Carbon::parse($lastPaidRent->tanggal_pembayaran);
                $duration = $lastPaidRent->durasi_sewa ?? 1;
                $type = $lastPaidRent->tipe_durasi ?? 'bulan';

                if ($type === 'hari') {
                    $expiryDate = $paymentDate->copy()->addDays($duration);
                } elseif ($type === 'minggu') {
                    $expiryDate = $paymentDate->copy()->addWeeks($duration);
                } else {
                    // Fixed 30 days as requested
                    $expiryDate = $paymentDate->copy()->addDays($duration * 30);
                }
            }
        }

        // 3. Status Calculation (Synchronized with System Subscription logic)
        $nowWib = now('Asia/Jakarta')->startOfDay();
        $expiryWib = $expiryDate ? $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay() : null;

        $daysRemaining = $expiryWib ? (int) $nowWib->diffInDays($expiryWib, false) : 0;

        $computedStatus = ''; // Initialize, will be set below
        $graceDaysRemaining = 0;
        $matiDaysCount = 0;

        // If user is a tenant but hasn't paid or has no rent history, we might want to handle it
        if ($expiryWib) {
            if ($daysRemaining > 0) {
                // Still active
                $computedStatus = 'active';
            } elseif ($daysRemaining >= -2) {
                // Grace period: Day 0 (due date), Day -1, Day -2 -> Total 3 days
                $computedStatus = 'grace';
                // 0 -> 3 days remaining, -1 -> 2 days, -2 -> 1 day
                $graceDaysRemaining = $daysRemaining + 3;
            } else {
                // Account becomes inactive starting from Day -3
                $computedStatus = 'inactive';
                // -3 -> 1 day dead, -4 -> 2 days dead, etc.
                $matiDaysCount = abs($daysRemaining) - 2;
            }
        } else {
            // No payment history found
            $computedStatus = 'none';
        }

        // 4. Fetch purchase history (all paid rent/booking transactions)
        $history = Transaksi::where('id_user', $user->id)
            ->whereNotNull('tanggal_pembayaran')
            ->whereIn('tipe', [Transaksi::TYPE_BOOKING, Transaksi::TYPE_SEWA])
            ->orderBy('tanggal_pembayaran', 'desc')
            ->paginate(10);

        return view('user.jatuh_tempo', [
            'title' => 'Jatuh Tempo Sewa',
            'role' => 'user',
            'user' => $user,
            'lastRent' => $lastPaidRent,
            'expiryDate' => $expiryDate,
            'daysRemaining' => $daysRemaining,
            'computedStatus' => $computedStatus,
            'graceDaysRemaining' => $graceDaysRemaining,
            'matiDaysCount' => $matiDaysCount,
            'history' => $history,
        ]);
    }

    /**
     * Store a new rent payment order from user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kamar' => 'required|exists:kamar,id',
            'kode_kos' => 'required|numeric',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:manual,pymen',
            'batas_bayar' => 'required_if:metode_pembayaran,manual|nullable|date_format:Y-m-d\TH:i',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Security check: must be a tenant of this kamar
        if ($user->id_kamar != $request->id_kamar) {
            return back()->with('error', 'Akses ditolak: Kamar tidak sesuai dengan data sewa Anda.');
        }

        // Check if user already has an active order for this kamar
        $existingOrder = Transaksi::where('id_user', $user->id)
            ->where('id_kamar', $request->id_kamar)
            ->where('status', 'pending')
            ->first();

        if ($existingOrder) {
            return back()->with('error', 'Anda sudah memiliki order yang menunggu verifikasi untuk kamar ini.');
        }

        $kamar = Kamar::findOrFail($request->id_kamar);

        // Expiry logic
        $batasBayar = $request->batas_bayar ? Carbon::parse($request->batas_bayar) : null;
        if ($request->metode_pembayaran === 'pymen') {
            $batasBayar = now()->addDays(3);
        }

        $duration = $kamar->durasi_sewa ?? 1;
        $type = $kamar->tipe_durasi ?? 'bulan';

        // Calculate next jatuh tempo based on current one if exists, else from now
        $lastPaidRent = Transaksi::where('id_user', $user->id)
            ->where('status', 'paid')
            ->whereIn('tipe', [Transaksi::TYPE_BOOKING, Transaksi::TYPE_SEWA])
            ->latest()
            ->first();

        $baseDate = $lastPaidRent && $lastPaidRent->jatuh_tempo ? Carbon::parse($lastPaidRent->jatuh_tempo) : now('Asia/Jakarta');
        $nextJatuhTempo = $baseDate->copy();

        if ($type === 'hari') {
            $nextJatuhTempo->addDays($duration);
        } elseif ($type === 'minggu') {
            $nextJatuhTempo->addWeeks($duration);
        } else {
            $nextJatuhTempo->addDays($duration * 30);
        }

        Transaksi::create([
            'jumlah_bayar' => $request->jumlah_bayar,
            'tanggal_pembayaran' => $request->metode_pembayaran === 'manual' ? $request->batas_bayar : null,
            'status' => 'verified', // Rent payments bypass initial pending verification
            'tipe' => Transaksi::TYPE_SEWA,
            'durasi_sewa' => $duration,
            'tipe_durasi' => $type,
            'id_user' => $user->id,
            'id_kamar' => $request->id_kamar,
            'kode_kos' => $kamar->kos->kode_kos,
            'catatan' => $request->catatan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'batas_bayar' => $batasBayar,
            'bukti_pembayaran' => null,
            'jatuh_tempo' => $nextJatuhTempo,
        ]);

        return redirect()->route('user.order')->with('success', 'Pembayaran sewa berhasil dikirim! Segera unggah bukti pembayaran di menu Order.');
    }
}

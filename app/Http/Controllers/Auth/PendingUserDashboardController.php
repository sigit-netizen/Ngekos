<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Models\JenisLangganan;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class PendingUserDashboardController extends Controller
{
    /**
     * Step 1: Update basic profile (NIK, DOB, Alamat).
     */
    public function stepOne(Request $request)
    {
        $pendingUserId = session('pending_user_id');
        if (!$pendingUserId) return redirect()->route('login');

        $pendingUser = PendingUser::findOrFail($pendingUserId);

        $request->validate([
            'nik' => ['required', 'numeric', 'digits:16', 'unique:users,nik', 'unique:pending_users,nik,' . $pendingUser->id],
            'tanggal_lahir' => ['required', 'date', 'before:-10 years'],
            'provinsi_nama' => ['required', 'string'],
            'kabupaten_nama' => ['required', 'string'],
            'kecamatan_nama' => ['required', 'string'],
            'desa_nama' => ['required', 'string'],
            'alamat_detail' => ['required', 'string', 'min:5'],
        ], [
            'tanggal_lahir.before' => 'Usia minimal adalah 10 tahun.',
        ]);

        $alamatLengkap = sprintf(
            "%s, Desa/Kel: %s, Kec: %s, %s, %s",
            $request->alamat_detail,
            $request->desa_nama,
            $request->kecamatan_nama,
            $request->kabupaten_nama,
            $request->provinsi_nama
        );

        $pendingUser->update([
            'nik' => $request->nik,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $alamatLengkap,
        ]);

        return back()->with('success', 'Data profil berhasil disimpan. Silakan lanjut ke verifikasi WhatsApp.');
    }

    /**
     * Step 2: Send OTP via WhatsApp and save package (Owners only).
     */
    public function sendOtp(Request $request)
    {
        $pendingUserId = session('pending_user_id');
        if (!$pendingUserId) return redirect()->route('login');

        $pendingUser = PendingUser::findOrFail($pendingUserId);

        $rules = [
            'nomor_wa' => ['required', 'numeric', 'min_digits:10', 'unique:users,nomor_wa', 'unique:pending_users,nomor_wa,' . $pendingUser->id],
        ];

        // Only Owners (id_plans == 2) need to choose a package
        if ($pendingUser->id_plans == 2) {
            $rules['plan_type'] = ['required', 'string'];
            // If plan contains 'Kamar', jumlah_kamar is required and min 1
            if (str_contains(strtolower($request->plan_type), 'kamar')) {
                $rules['jumlah_kamar'] = ['required', 'integer', 'min:1'];
            }
        }

        $request->validate($rules);

        // Find id_plans from plans table
        $idPlans = $pendingUser->id_plans; // default
        if ($request->has('plan_type')) {
            $plan = \Illuminate\Support\Facades\DB::table('plans')
                ->where('nama_plans', $request->plan_type)
                ->first();
            if ($plan) {
                $idPlans = $plan->id;
            }
        }

        // Rate limit OTP sending...
        $throttleKey = 'pending-otp-' . $pendingUserId;
        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            return back()->with('error', 'Harap tunggu 1 menit sebelum mengirim ulang kode.');
        }
        RateLimiter::hit($throttleKey, 60);

        // Generate OTP
        $otp = random_int(100000, 999999);
        
        $data = [
            'nomor_wa' => $request->nomor_wa,
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(1),
            'id_plans' => $idPlans,
        ];

        if ($request->has('plan_type')) {
            $data['plan_type'] = $request->plan_type;
        }

        if ($request->has('jumlah_kamar')) {
            $data['jumlah_kamar'] = $request->jumlah_kamar;
        }

        $pendingUser->update($data);

        // Send OTP via Fonnte
        $fonnteService = app(FonnteService::class);
        $message = "Kode OTP pendaftaran Ngekos Anda adalah: *{$otp}*\n\nBerlaku selama 1 menit. Jangan berikan kode ini kepada siapapun.";
        $fonnteService->sendMessage($request->nomor_wa, $message);

        return back()->with('otp_sent', true)->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }

    /**
     * Step 3: Verify OTP and finalize.
     */
    public function verifyOtp(Request $request)
    {
        $pendingUserId = session('pending_user_id');
        if (!$pendingUserId) return redirect()->route('login');

        $pendingUser = PendingUser::findOrFail($pendingUserId);

        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        if (now()->gt($pendingUser->otp_expires_at)) {
            return back()->with('error', 'Kode OTP sudah kadaluarsa. Silakan kirim ulang.');
        }

        if ($pendingUser->otp !== $request->otp) {
            return back()->with('error', 'Kode OTP salah.');
        }

        // Finalize: Set status to 'menunggu_verifikasi' or keep 'pending' but mark as complete
        // We will keep 'pending' for now but the dashboard UI will know it's complete because NIK and WA are filled & verified
        $pendingUser->update([
            'otp' => null, // Clear OTP after success
            'otp_expires_at' => null,
            'status' => 'pending', // You might want a specific status like 'verified_data'
        ]);

        return back()->with('success', 'WhatsApp berhasil diverifikasi! Data Anda telah dikirim ke Super Admin untuk ditinjau.');
    }
}

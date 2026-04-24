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
     * Langkah 1: Perbarui profil dasar (NIK, Tanggal Lahir, Alamat).
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
     * Langkah 2: Kirim OTP melalui WhatsApp dan simpan paket (Khusus Pemilik/Owner).
     */
    public function sendOtp(Request $request)
    {
        $pendingUserId = session('pending_user_id');
        if (!$pendingUserId) return redirect()->route('login');

        $pendingUser = PendingUser::findOrFail($pendingUserId);

        $rules = [
            'nomor_wa' => ['required', 'numeric', 'min_digits:10', 'unique:users,nomor_wa', 'unique:pending_users,nomor_wa,' . $pendingUser->id],
        ];

        // Hanya Pemilik (id_plans == 2) yang perlu memilih paket
        if ($pendingUser->id_plans == 2) {
            $rules['plan_type'] = ['required', 'string'];
            // Jika paket berisi 'Kamar', jumlah_kamar wajib diisi dan minimal 1
            if (str_contains(strtolower($request->plan_type), 'kamar')) {
                $rules['jumlah_kamar'] = ['required', 'integer', 'min:1'];
            }
        }

        $request->validate($rules);

        // Cari id_plans dari tabel plans
        $idPlans = $pendingUser->id_plans; // Standar (Default)
        if ($request->has('plan_type')) {
            $plan = \Illuminate\Support\Facades\DB::table('plans')
                ->where('nama_plans', $request->plan_type)
                ->first();
            if ($plan) {
                $idPlans = $plan->id;
            }
        }

        // Pembatasan rate pengiriman OTP...
        $throttleKey = 'pending-otp-' . $pendingUserId;
        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            return back()->with('error', 'Harap tunggu 1 menit sebelum mengirim ulang kode.');
        }
        RateLimiter::hit($throttleKey, 60);

        // Hasilkan OTP
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

        // Kirim OTP melalui Fonnte
        $fonnteService = app(FonnteService::class);
        $message = "Kode OTP pendaftaran Ngekos Anda adalah: *{$otp}*\n\nBerlaku selama 1 menit. Jangan berikan kode ini kepada siapapun.";
        $fonnteService->sendMessage($request->nomor_wa, $message);

        return back()->with('otp_sent', true)->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }

    /**
     * Langkah 3: Verifikasi OTP dan selesaikan.
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

        // Selesaikan: Setel status ke 'menunggu_verifikasi' atau biarkan 'pending' tetapi tandai sebagai selesai
        // Kami akan membiarkan 'pending' untuk saat ini tetapi UI dashboard akan tahu itu sudah selesai karena NIK dan WA sudah diisi & diverifikasi
        $pendingUser->update([
            'otp' => null, // Bersihkan OTP setelah berhasil
            'otp_expires_at' => null,
            'status' => 'pending', // Anda mungkin menginginkan status spesifik seperti 'verified_data'
        ]);

        return back()->with('success', 'WhatsApp berhasil diverifikasi! Data Anda telah dikirim ke Super Admin untuk ditinjau.');
    }
}

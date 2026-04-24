<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class DeviceOtpController extends Controller
{
    /**
     * Tampilkan halaman pemilihan saluran (channel) OTP.
     */
    public function showSelectChannel()
    {
        if (!session('pending_otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('pending_otp_user_id'));
        if (!$user)
            return redirect()->route('login');

        return view('pages.auth.otp-selection', [
            'title' => 'Pilih Metode Verifikasi',
            'user' => $user
        ]);
    }

    /**
     * Hasilkan dan kirim OTP melalui saluran yang dipilih.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:whatsapp,email'
        ]);

        $userId = session('pending_otp_user_id');
        if (!$userId)
            return redirect()->route('login');

        $user = User::find($userId);
        if (!$user)
            return redirect()->route('login');

        // Hasilkan OTP 6 digit
        $otp = random_int(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(1) // Kedaluwarsa 1 menit seperti yang diminta
        ]);

        // Simpan saluran ke sesi untuk ditampilkan
        session(['otp_channel' => $request->channel]);

        // Kirim OTP
        if ($request->channel === 'whatsapp' && !$user->nomor_wa) {
            Log::warning('OTP WhatsApp: nomor_wa kosong untuk user ID: ' . $user->id);
            return back()->with('error', 'Nomor WhatsApp Anda belum diisi di profil. Silakan pilih metode Email atau hubungi admin.');
        }

        if ($request->channel === 'whatsapp' && $user->nomor_wa) {
            $fonnteService = app(\App\Services\FonnteService::class);
            $message = "Kode OTP Anda adalah: *{$otp}*\n\nBerlaku selama 1 menit. Mohon tidak memberikan kode ini kepada siapapun.";
            Log::info('OTP: Mencoba kirim WhatsApp ke nomor: ' . $user->nomor_wa . ' untuk user ID: ' . $user->id);
            $response = $fonnteService->sendMessage($user->nomor_wa, $message);
            
            if (!$response || (isset($response['status']) && $response['status'] == false)) {
                Log::error('OTP WhatsApp gagal terkirim', ['user_id' => $user->id, 'nomor_wa' => $user->nomor_wa, 'response' => $response]);
                return back()->with('error', 'Gagal mengirim OTP ke WhatsApp. ' . ($response['message'] ?? 'Silakan coba lagi.'));
            }
            Log::info('OTP WhatsApp berhasil dikirim ke user ID: ' . $user->id);
        } elseif ($request->channel === 'email') {
            try {
                \Illuminate\Support\Facades\Mail::raw("Kode OTP Anda adalah: {$otp}\n\nBerlaku selama 1 menit. Mohon tidak memberikan kode ini kepada siapapun.", function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Kode Verifikasi OTP - Ngekos');
                });
            } catch (\Exception $e) {
                \Log::error('OTP Email Error: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengirim OTP ke Email. Silakan coba metode lain.');
            }
        }

        return redirect()->route('otp.verify')->with('success', 'Kode OTP telah dikirim!');
    }

    /**
     * Tampilkan halaman verifikasi OTP.
     */
    public function show()
    {
        if (!session('pending_otp_user_id')) {
            return redirect()->route('login');
        }

        return view('pages.auth.verify-otp', ['title' => 'Verifikasi OTP']);
    }

    /**
     * Tangani pengiriman OTP.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        $otpInput = trim($request->otp);
        $userId = session('pending_otp_user_id');
        if (!$userId)
            return redirect()->route('login');

        $user = User::find($userId);
        if (!$user)
            return redirect()->route('login');

        // Periksa apakah sudah kedaluwarsa
        if (now()->gt($user->otp_expires_at)) {
            return back()->with('error', 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.');
        }

        $throttleKey = 'otp-verify-' . $userId;
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $hours = ceil($seconds / 3600);
            return back()->with('error', "Terlalu banyak percobaan salah. Verifikasi ditangguhkan selama 24 jam. Silakan coba lagi dalam $hours jam.");
        }

        // Periksa apakah benar (Perbandingan longgar untuk menghindari ketidakcocokan tipe string/int)
        if ($user->otp != $otpInput) {
            RateLimiter::hit($throttleKey, 86400); // Kunci selama 24 jam setelah 3 kali percobaan salah
            
            $remaining = RateLimiter::remaining($throttleKey, 3);
            $message = 'Kode OTP salah.';
            
            if ($remaining === 1) {
                $message .= ' Hati-hati, sisa 1 kesempatan lagi sebelum akun ditangguhkan 24 jam!';
            }

            return back()->with('error', $message);
        }

        // OTP benar! Bersihkan kode OTP dan sesi verifikasi
        RateLimiter::clear($throttleKey);
        $user->update(['otp' => null, 'otp_expires_at' => null]);

        DB::table('sessions')->where('user_id', $user->id)->delete();

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget(['pending_otp_user_id', 'otp_channel']);

        // Logika pengalihan (Redirect)
        if ($user->hasRole('superadmin')) {
            $route = 'superadmin.dashboard';
        } elseif ($user->hasRole('admin') || $user->hasRole('nonaktif')) {
            $route = 'admin.dashboard';
        } elseif ($user->hasRole(['users', 'user'])) {
            $route = 'user.dashboard';
        } elseif ($user->hasRole('member')) {
            $route = 'member.dashboard';
        } else {
            $route = 'home';
        }

        return redirect()->route($route)->with('success_login', 'Perangkat berhasil diverifikasi!');
    }

    /**
     * Kirim ulang OTP.
     */
    public function resend()
    {
        $userId = session('pending_otp_user_id');
        if (!$userId)
            return response()->json(['success' => false], 403);

        $user = User::find($userId);
        $throttleKey = 'otp-resend-' . $userId;
        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            return back()->with('error', 'Harap tunggu 1 menit sebelum mengirim ulang kode.');
        }
        RateLimiter::hit($throttleKey, 60);

        $otp = random_int(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(1)
        ]);

        if (session('otp_channel') === 'whatsapp' && $user->nomor_wa) {
            $fonnteService = app(\App\Services\FonnteService::class);
            $message = "Kode OTP Anda adalah: *{$otp}*\n\nBerlaku selama 1 menit. Mohon tidak memberikan kode ini kepada siapapun.";
            $response = $fonnteService->sendMessage($user->nomor_wa, $message);

            if (!$response || (isset($response['status']) && $response['status'] == false)) {
                return back()->with('error', 'Gagal mengirim ulang OTP ke WhatsApp. ' . ($response['message'] ?? 'Silakan coba lagi.'));
            }
        } elseif (session('otp_channel') === 'email') {
            try {
                \Illuminate\Support\Facades\Mail::raw("Kode OTP Anda adalah: {$otp}\n\nBerlaku selama 1 menit. Mohon tidak memberikan kode ini kepada siapapun.", function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Kode Verifikasi OTP - Ngekos');
                });
            } catch (\Exception $e) {
                \Log::error('OTP Email Resend Error: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengirim ulang OTP ke Email. Silakan coba metode lain.');
            }
        }

        return redirect()->route('otp.verify')->with('success', 'Kode OTP baru telah dikirim!');
    }
}

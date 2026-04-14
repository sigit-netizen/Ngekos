<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class DeviceOtpController extends Controller
{
    /**
     * Show the OTP channel selection view.
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
     * Generate and send OTP via selected channel.
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

        // Generate 6-digit OTP
        $otp = random_int(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(1) // 1 minute expiry as requested
        ]);

        // Save channel to session for display
        session(['otp_channel' => $request->channel]);

        // Send OTP
        if ($request->channel === 'whatsapp' && $user->nomor_wa) {
            $fonnteService = app(\App\Services\FonnteService::class);
            $message = "Kode OTP Anda adalah: *{$otp}*\n\nBerlaku selama 1 menit. Mohon tidak memberikan kode ini kepada siapapun.";
            $response = $fonnteService->sendMessage($user->nomor_wa, $message);
            
            if (!$response || (isset($response['status']) && $response['status'] == false)) {
                return back()->with('error', 'Gagal mengirim OTP ke WhatsApp. ' . ($response['message'] ?? 'Silakan coba lagi.'));
            }
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
     * Show the OTP verification view.
     */
    public function show()
    {
        if (!session('pending_otp_user_id')) {
            return redirect()->route('login');
        }

        return view('pages.auth.verify-otp', ['title' => 'Verifikasi OTP']);
    }

    /**
     * Handle the OTP submission.
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

        // Check if expired
        if (now()->gt($user->otp_expires_at)) {
            return back()->with('error', 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.');
        }

        $throttleKey = 'otp-verify-' . $userId;
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $hours = ceil($seconds / 3600);
            return back()->with('error', "Terlalu banyak percobaan salah. Verifikasi ditangguhkan selama 24 jam. Silakan coba lagi dalam $hours jam.");
        }

        // Check if correct (Relaxed comparison to avoid string/int type mismatch)
        if ($user->otp != $otpInput) {
            RateLimiter::hit($throttleKey, 86400); // Lock for 24 hours after 3 hits
            
            $remaining = RateLimiter::remaining($throttleKey, 3);
            $message = 'Kode OTP salah.';
            
            if ($remaining === 1) {
                $message .= ' Hati-hati, sisa 1 kesempatan lagi sebelum akun ditangguhkan 24 jam!';
            }

            return back()->with('error', $message);
        }

        // OTP is correct! Clear it and sessions
        RateLimiter::clear($throttleKey);
        $user->update(['otp' => null, 'otp_expires_at' => null]);

        DB::table('sessions')->where('user_id', $user->id)->delete();

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget(['pending_otp_user_id', 'otp_channel']);

        // Redirect logic
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
     * Resend OTP.
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

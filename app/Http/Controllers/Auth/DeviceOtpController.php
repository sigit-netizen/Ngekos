<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(1) // 1 minute expiry as requested
        ]);

        // Save channel to session for display
        session(['otp_channel' => $request->channel]);

        // FLASH dummy_otp so it shows in the view (test mode)
        return redirect()->route('otp.verify')->with('dummy_otp', $otp);
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

        // Check if correct (Relaxed comparison to avoid string/int type mismatch)
        if ($user->otp != $otpInput) {
            return back()->with('error', 'Kode OTP salah.');
        }

        // OTP is correct! Clear it and sessions
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
        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(1)
        ]);

        return redirect()->route('otp.verify')->with('dummy_otp', $otp)->with('success', 'Kode OTP baru telah dikirim!');
    }
}

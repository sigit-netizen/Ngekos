<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PendingUserController extends Controller
{
    public function uploadProof(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pending_users,email',
            'bukti_pembayaran' => 'required|image|max:512000', // 500MB seperti yang diminta sebelumnya
            'metode_pembayaran' => 'required|string',
        ]);

        $pendingUser = PendingUser::where('email', $request->email)->first();

        if (!$pendingUser || $pendingUser->status !== 'verified') {
            return back()->with('error', 'Permintaan tidak valid.');
        }

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('pembayaran_pemilik_kos', 'public');

            $pendingUser->update([
                'bukti_pembayaran' => $path,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => 'konfirmasi',
            ]);

            // Beri tahu Superadmin
            $superadmins = \App\Models\User::role('superadmin')->get();
            foreach ($superadmins as $admin) {
                if ($admin->nomor_wa) {
                    $admin->notify(new \App\Notifications\BuktiPembayaranNotification([
                        'type' => 'owner_reg',
                        'name' => $pendingUser->name,
                        'plan_name' => 'Owner Member',
                    ]));
                }
            }

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Mohon tunggu verifikasi akhir dari admin.');
        }

        return back()->with('error', 'Gagal mengunggah gambar.');
    }
}

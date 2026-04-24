<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Perbarui informasi profil pengguna.
     */
    public function update(Request $request)
    {
        if (!Auth::user()->can('fitur.edit_profile')) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki permission untuk mengedit profil.');
        }

        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nik' => 'nullable|string|max:20',
            'nomor_wa' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'nomor_rekening' => 'nullable|string|max:255',
            'nama_pemilik' => 'nullable|string|max:255',
            'nama_bank_2' => 'nullable|string|max:255',
            'nomor_rekening_2' => 'nullable|string|max:255',
            'nama_pemilik_2' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->nik = $request->nik;
        $user->nomor_wa = $request->nomor_wa;
        $user->alamat = $request->alamat;
        $user->instagram = $request->instagram;
        $user->twitter = $request->twitter;
        $user->youtube = $request->youtube;
        $user->tiktok = $request->tiktok;

        // Perbarui/buat informasi akun bank
        $user->nomorBank()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama_bank' => $request->nama_bank,
                'nomor_rekening' => $request->nomor_rekening,
                'nama_pemilik' => $request->nama_pemilik,
                'nama_bank_2' => $request->nama_bank_2,
                'nomor_rekening_2' => $request->nomor_rekening_2,
                'nama_pemilik_2' => $request->nama_pemilik_2,
            ]
        );

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
    /**
     * Verifikasi kata sandi pengguna sebelum membuka modal profil.
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (Hash::check($request->password, Auth::user()->password)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kata sandi salah. Silakan coba lagi.'], 422);
    }
}

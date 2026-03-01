<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KamarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kos = $user->kos()->first();

        // Eager load fasilitas and transaksis
        $kamars = $kos ? $kos->kamars()->with(['fasilitas', 'transaksis'])->latest()->get() : collect();

        $activeSubscription = $user->langganans()->where('status', 'active')->latest()->first();
        $isPerKamar = in_array($user->id_plans, [4, 5]);
        $limitKamar = ($isPerKamar && $activeSubscription) ? $activeSubscription->jumlah_kamar : 0;

        return view('member.kamar', [
            'title' => 'Manajemen Kamar',
            'role' => 'admin',
            'kamars' => $kamars,
            'kos' => $kos,
            'limitKamar' => $limitKamar,
            'isPerKamar' => $isPerKamar
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $kos = $user->kos()->first();

        if (!$kos) {
            return back()->with('error', 'Silakan buat data kos terlebih dahulu.');
        }

        // Clean price input from formatting (dots)
        if ($request->has('harga')) {
            $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        }

        $request->validate([
            'nomor_kamar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kamar')->where(fn($query) => $query->where('id_kos', $kos->id))
            ],
            'harga' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'nullable|string',
        ]);

        // Quota check
        $activeSubscription = $user->langganans()->where('status', 'active')->latest()->first();
        $isPerKamar = in_array($user->id_plans, [4, 5]);
        if ($isPerKamar && $activeSubscription) {
            $currentRooms = $kos->kamars()->count();
            if ($currentRooms >= $activeSubscription->jumlah_kamar) {
                return back()->with('error', 'Kuota kamar Anda sudah penuh. Silakan upgrade paket Anda.');
            }
        }

        $kamar = Kamar::create([
            'nomor_kamar' => $request->nomor_kamar,
            'harga' => $request->harga,
            'status' => 'tersedia', // Default, auto in view
            'id_kos' => $kos->id,
        ]);

        // Add facilities
        if ($request->fasilitas) {
            foreach ($request->fasilitas as $name) {
                $name = trim($name);
                if ($name) {
                    Fasilitas::create([
                        'nama_fasilitas' => $name,
                        'id_kamar' => $kamar->id,
                    ]);
                }
            }
        }

        return back()->with('success', 'Kamar berhasil ditambahkan!');
    }

    public function update(Request $request, Kamar $kamar)
    {
        // Clean price input
        if ($request->has('harga')) {
            $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        }

        $user = Auth::user();
        $kos = $user->kos()->first();

        $request->validate([
            'nomor_kamar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kamar')->where(fn($query) => $query->where('id_kos', $kos->id))->ignore($kamar->id)
            ],
            'harga' => 'required|numeric|min:0',
        ]);

        $kamar->update($request->only(['nomor_kamar', 'harga']));

        return back()->with('success', 'Data kamar berhasil diperbarui!');
    }

    public function updateFasilitas(Request $request, Kamar $kamar)
    {
        $request->validate([
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'nullable|string',
        ]);

        // Remove old facilities
        $kamar->fasilitas()->delete();

        // Add new facilities
        if ($request->fasilitas) {
            foreach ($request->fasilitas as $name) {
                $name = trim($name);
                if ($name) {
                    Fasilitas::create([
                        'nama_fasilitas' => $name,
                        'id_kamar' => $kamar->id,
                    ]);
                }
            }
        }

        return back()->with('success', 'Fasilitas kamar berhasil diperbarui!');
    }

    public function destroy(Kamar $kamar)
    {
        $kamar->delete();
        return back()->with('success', 'Kamar berhasil dihapus!');
    }
}

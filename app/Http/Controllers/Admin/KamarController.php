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

        // Muat fasilitas dan transaksi secara eager (Eager load)
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

        // Bersihkan input harga dari format (titik)
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
            'durasi_sewa' => 'required|integer|min:1',
            'tipe_durasi' => 'required|in:hari,minggu,bulan',
            'foto' => 'nullable|image|max:512000',
            'foto_camera' => 'nullable|image|max:512000',
            'foto_gallery' => 'nullable|image|max:512000',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'nullable|string',
        ]);

        // Path sementara untuk pemrosesan latar belakang (background processing)
        $tempPath = null;
        $file = $request->file('foto_camera') ?? $request->file('foto_gallery') ?? $request->file('foto');
        if ($file) {
            $tempPath = $file->store('temp', 'public');
        }

        // Pemeriksaan kuota
        $activeSubscription = $user->langganans()->where('status', 'active')->latest()->first();
        $isPerKamar = in_array($user->id_plans, [4, 5]);
        if ($isPerKamar && $activeSubscription) {
            $currentRooms = $kos->kamars()->count();
            if ($currentRooms >= $activeSubscription->jumlah_kamar) {
                return back()->with('error', 'Kuota kamar Anda sudah penuh. Silakan upgrade paket Anda.');
            }
        }

        // Pemeriksaan durasi maksimal (1 tahun)
        if ($request->tipe_durasi === 'bulan' && $request->durasi_sewa > 12) {
            return back()->with('error', 'Durasi maksimal adalah 12 bulan.');
        }
        if ($request->tipe_durasi === 'minggu' && $request->durasi_sewa > 52) {
            return back()->with('error', 'Durasi maksimal adalah 52 minggu.');
        }
        if ($request->tipe_durasi === 'hari' && $request->durasi_sewa > 365) {
            return back()->with('error', 'Durasi maksimal adalah 365 hari.');
        }

        $kamar = Kamar::create([
            'nomor_kamar' => $request->nomor_kamar,
            'harga' => $request->harga,
            'durasi_sewa' => $request->durasi_sewa,
            'tipe_durasi' => $request->tipe_durasi,
            'status' => 'tersedia', // Default, otomatis di tampilan (view)
            'foto' => $tempPath ? 'storage/' . $tempPath : null, // Tampilkan file sementara sebelum dioptimasi
            'id_kos' => $kos->id,
        ]);

        if ($tempPath) {
            \App\Jobs\ProcessImageOptimization::dispatch($tempPath, 'kamar', $kamar, 'foto');
        }

        // Tambah fasilitas
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
        // Bersihkan input harga
        if ($request->has('harga')) {
            $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        }

        $user = Auth::user();
        $kos = $user->kos()->first();

        // Pemeriksaan Keamanan: Pastikan kamar milik kos pengguna
        if (!$kos || $kamar->id_kos !== $kos->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'nomor_kamar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kamar')->where(fn($query) => $query->where('id_kos', $kos->id))->ignore($kamar->id)
            ],
            'harga' => 'required|numeric|min:0',
            'durasi_sewa' => 'required|integer|min:1',
            'tipe_durasi' => 'required|in:hari,minggu,bulan',
            'foto' => 'nullable|image|max:512000',
            'foto_camera' => 'nullable|image|max:512000',
            'foto_gallery' => 'nullable|image|max:512000',
        ]);

        // Pemeriksaan durasi maksimal (1 tahun)
        if ($request->tipe_durasi === 'bulan' && $request->durasi_sewa > 12) {
            return back()->with('error', 'Durasi maksimal adalah 12 bulan.');
        }
        if ($request->tipe_durasi === 'minggu' && $request->durasi_sewa > 52) {
            return back()->with('error', 'Durasi maksimal adalah 52 minggu.');
        }
        if ($request->tipe_durasi === 'hari' && $request->durasi_sewa > 365) {
            return back()->with('error', 'Durasi maksimal adalah 365 hari.');
        }

        $updateData = $request->only(['nomor_kamar', 'harga', 'durasi_sewa', 'tipe_durasi']);

        $file = $request->file('foto_camera') ?? $request->file('foto_gallery') ?? $request->file('foto');
        if ($file) {
            $tempPath = $file->store('temp', 'public');
            \App\Jobs\ProcessImageOptimization::dispatch($tempPath, 'kamar', $kamar, 'foto');
        }

        $kamar->update($updateData);

        return back()->with('success', 'Data kamar berhasil diperbarui!');
    }

    public function updateFasilitas(Request $request, Kamar $kamar)
    {
        $user = Auth::user();
        $kos = $user->kos()->first();

        // Pemeriksaan Keamanan: Pastikan kamar milik kos pengguna
        if (!$kos || $kamar->id_kos !== $kos->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'nullable|string',
        ]);

        // Hapus fasilitas lama
        $kamar->fasilitas()->delete();

        // Tambah fasilitas baru
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
        $user = Auth::user();
        $kos = $user->kos()->first();

        // Pemeriksaan Keamanan: Pastikan kamar milik kos pengguna
        if (!$kos || $kamar->id_kos !== $kos->id) {
            abort(403, 'Akses ditolak.');
        }

        $kamar->delete();
        return back()->with('success', 'Kamar berhasil dihapus!');
    }
}

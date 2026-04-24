<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FasilitasKos;
use App\Models\Aduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AduanFasilitasNotification;

class FasilitasController extends Controller
{
    /**
     * Tampilkan fasilitas pengguna saat ini dan riwayat permintaan.
     */
    public function index()
    {
        $user = Auth::user();

        // Gunakan relasi langsung dari model User karena lebih andal untuk penyewa terkonfirmasi (confirmed tenants)
        $kamar = $user->kamar;
        $kosId = $user->id_kos;

        // Dapatkan nama-nama fasilitas yang dimiliki saat ini (tidak peka huruf besar/kecil untuk perbandingan)
        $ownedFacilitiesNames = $kamar ? $kamar->fasilitas->pluck('nama_fasilitas')->map(fn($n) => strtolower(trim($n))) : collect();

        // tambahan (addons) yang tersedia untuk kos ini
        $availableAddons = FasilitasKos::where('id_kos', $kosId)
            ->get()
            ->filter(function ($addon) use ($ownedFacilitiesNames) {
                // Hanya tampilkan tambahan yang BELUM dimiliki oleh pengguna (DO NOT already have)
                return !$ownedFacilitiesNames->contains(strtolower(trim($addon->nama_fasilitas)));
            })
            ->values();

        // fasilitas saat ini (untuk pilihan aduan)
        $facilities = $kamar ? $kamar->fasilitas : collect();

        // riwayat aduan & tambah (history)
        $aduans = Aduan::where('id_user', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.fasilitas', [
            'title' => 'Fasilitas Kos',
            'room' => $kamar,
            'kosId' => $kosId,
            'facilities' => $facilities,
            'availableAddons' => $availableAddons,
            'aduans' => $aduans,
        ]);
    }

    /**
     * Simpan pengaduan fasilitas (facility complaint) yang diajukan oleh pengguna.
     */
    public function storeAduan(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string|max:2000',
            'id_kos' => 'required|exists:kos,id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $aduan = Aduan::create([
                'id_user' => $user->id,
                'id_kos' => $request->id_kos,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
                'kategori' => 'fasilitas',
                'status' => 'belum_dibaca',
            ]);

            // Beri tahu Pemilik Kos (Notify Kos Owner)
            $kos = $aduan->kos;
            if ($kos && $kos->user) {
                $target = $kos->user;
                $data = [
                    'nama' => $user->name,
                    'kos' => $kos->nama_kos,
                    'judul' => $request->judul,
                    'pesan' => $request->pesan,
                    'kategori' => 'fasilitas'
                ];
                dispatch(function () use ($target, $data) {
                    $target->notify(new AduanFasilitasNotification($data));
                })->afterResponse();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan aduan fasilitas berhasil dikirim!'
                ]);
            }

            return back()->with('success', 'Laporan aduan fasilitas berhasil dikirim!');
        } catch (\Exception $e) {
            Log::error('Error storing Aduan: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat mengirim aduan.'
                ], 500);
            }
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Simpan permintaan tambah fasilitas yang diajukan oleh pengguna.
     */
    public function storeTambah(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string|max:2000',
            'id_kos' => 'required|exists:kos,id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $aduan = Aduan::create([
                'id_user' => $user->id,
                'id_kos' => $request->id_kos,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
                'kategori' => 'tambah',
                'status' => 'belum_dibaca',
            ]);

            // Beri tahu Pemilik Kos (Antrean/Queued untuk kecepatan)
            $kos = $aduan->kos;
            if ($kos && $kos->user) {
                $target = $kos->user;
                $target->notify(new AduanFasilitasNotification([
                    'nama' => $user->name,
                    'kos' => $kos->nama_kos,
                    'judul' => $request->judul,
                    'pesan' => $request->pesan,
                    'kategori' => 'tambah'
                ]));
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permintaan tambah fasilitas berhasil dikirim!'
                ]);
            }

            return back()->with('success', 'Permintaan tambah fasilitas berhasil dikirim!');
        } catch (\Exception $e) {
            Log::error('Error storing Tambah Fasilitas: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat mengirim permintaan.'
                ], 500);
            }
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
}

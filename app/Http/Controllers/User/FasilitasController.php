<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FasilitasKos;
use App\Models\Aduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FasilitasController extends Controller
{
    /**
     * Display the user's current facilities and history of requests.
     */
    public function index()
    {
        $user = Auth::user();

        // Use direct relationships from User model as they are more reliable for confirmed tenants
        $kamar = $user->kamar;
        $kosId = $user->id_kos;

        // Get currently owned facilities names (case-insensitive for comparison)
        $ownedFacilitiesNames = $kamar ? $kamar->fasilitas->pluck('nama_fasilitas')->map(fn($n) => strtolower(trim($n))) : collect();

        // available addons for this kos
        $availableAddons = FasilitasKos::where('id_kos', $kosId)
            ->get()
            ->filter(function ($addon) use ($ownedFacilitiesNames) {
                // Only show addons that the user DOES NOT already have
                return !$ownedFacilitiesNames->contains(strtolower(trim($addon->nama_fasilitas)));
            })
            ->values();

        // current facilities (for aduan select)
        $facilities = $kamar ? $kamar->fasilitas : collect();

        // history of aduan & tambah
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
     * Store a facility complaint submitted by the user.
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
            Aduan::create([
                'id_user' => $user->id,
                'id_kos' => $request->id_kos,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
                'kategori' => 'fasilitas',
                'status' => 'belum_dibaca',
            ]);

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
     * Store a tambah fasilitas request submitted by the user.
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
            Aduan::create([
                'id_user' => $user->id,
                'id_kos' => $request->id_kos,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
                'kategori' => 'tambah',
                'status' => 'belum_dibaca',
            ]);

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

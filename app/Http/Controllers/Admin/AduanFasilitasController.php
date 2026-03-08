<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AduanFasilitasController extends Controller
{
    /**
     * Display all aduans for the admin's kos.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $kos = $user->kos()->first();

        $filter = $request->input('filter', 'semua'); // semua | belum_dibaca | sudah_dibaca
        $kategori = $request->input('kategori', 'semua'); // semua | fasilitas | tambah

        $query = Aduan::with(['user.kamar'])
            ->where('id_kos', $kos?->id)
            ->latest();

        if ($filter === 'belum_dibaca') {
            $query->where('status', 'belum_dibaca');
        } elseif ($filter === 'sudah_dibaca') {
            $query->where('status', 'sudah_dibaca');
        }

        if ($kategori !== 'semua') {
            $query->where('kategori', $kategori);
        }

        $aduans = $query->paginate(10);
        $belumDibaca = Aduan::where('id_kos', $kos?->id)->where('status', 'belum_dibaca')->count();
        $sudahDibaca = Aduan::where('id_kos', $kos?->id)->where('status', 'sudah_dibaca')->count();

        // Specific category counts
        $countAduan = Aduan::where('id_kos', $kos?->id)->where('kategori', 'fasilitas')->count();
        $countTambah = Aduan::where('id_kos', $kos?->id)->where('kategori', 'tambah')->count();

        return view('member.pesan_aduan', [
            'title' => 'Kotak Masuk & Aduan',
            'role' => 'admin',
            'aduans' => $aduans,
            'filter' => $filter,
            'kategori' => $kategori,
            'belumDibaca' => $belumDibaca,
            'sudahDibaca' => $sudahDibaca,
            'countAduan' => $countAduan,
            'countTambah' => $countTambah,
        ]);
    }

    /**
     * Mark an aduan as read.
     */
    public function markRead(Aduan $aduan)
    {
        $user = Auth::user();
        $kos = $user->kos()->first();

        if (!$kos || $aduan->id_kos !== $kos->id) {
            abort(403, 'Akses ditolak.');
        }

        $aduan->update([
            'status' => 'sudah_dibaca',
            'dibaca_at' => now(),
        ]);

        return back()->with('success', 'Aduan ditandai sebagai sudah dibaca.');
    }

    /**
     * Mark an aduan as unread.
     */
    public function markUnread(Aduan $aduan)
    {
        $user = Auth::user();
        $kos = $user->kos()->first();

        if (!$kos || $aduan->id_kos !== $kos->id) {
            abort(403, 'Akses ditolak.');
        }

        $aduan->update([
            'status' => 'belum_dibaca',
            'dibaca_at' => null,
        ]);

        return back()->with('success', 'Aduan ditandai sebagai belum dibaca.');
    }

    /**
     * Delete an aduan.
     */
    public function destroy(Aduan $aduan)
    {
        $user = Auth::user();
        $kos = $user->kos()->first();

        if (!$kos || $aduan->id_kos !== $kos->id) {
            abort(403, 'Akses ditolak.');
        }

        $aduan->delete();

        return back()->with('success', 'Aduan berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FasilitasKos;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FasilitasKosController extends Controller
{
    public function index()
    {
        $kos = Kos::where('id_user', Auth::id())->first();
        $fasilitas = FasilitasKos::where('id_kos', $kos?->id)->latest()->get();

        return view('member.fasilitas', [
            'title' => 'Manajemen Fasilitas',
            'role' => 'admin',
            'fasilitas' => $fasilitas,
            'kos' => $kos
        ]);
    }

    public function store(Request $request)
    {
        $kos = Kos::where('id_user', Auth::id())->first();
        if (!$kos) {
            return back()->with('error', 'Anda belum memiliki Kos yang terdaftar.');
        }

        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'harga_tambahan' => 'required|numeric|min:0',
        ]);

        FasilitasKos::create([
            'id_kos' => $kos->id,
            'nama_fasilitas' => $request->nama_fasilitas,
            'harga_tambahan' => $request->harga_tambahan,
        ]);

        return back()->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    public function update(Request $request, FasilitasKos $fasilitas)
    {
        $kos = Kos::where('id_user', Auth::id())->first();
        if ($fasilitas->id_kos !== $kos->id) {
            abort(403);
        }

        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'harga_tambahan' => 'required|numeric|min:0',
        ]);

        $fasilitas->update([
            'nama_fasilitas' => $request->nama_fasilitas,
            'harga_tambahan' => $request->harga_tambahan,
        ]);

        return back()->with('success', 'Fasilitas berhasil diperbarui!');
    }

    public function destroy(FasilitasKos $fasilitas)
    {
        $kos = Kos::where('id_user', Auth::id())->first();
        if ($fasilitas->id_kos !== $kos->id) {
            abort(403);
        }

        $fasilitas->delete();
        return back()->with('success', 'Fasilitas berhasil dihapus!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;

class KosController extends Controller
{
    /**
     * Update the specified kos in storage.
     */
    public function update(Request $request, Kos $kos)
    {
        // Security Check: Ensure owner only updates their own kos
        if ($kos->id_user !== auth()->id()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki akses ke properti ini.');
        }

        $editField = $request->editField;
        $rules = [];
        $updateData = [];

        if ($editField === 'nama_kos') {
            $rules['nama_kos'] = 'required|string|max:255';
            $rules['foto'] = 'nullable|image|max:10240';
            $updateData['nama_kos'] = $request->nama_kos;

            if ($request->hasFile('foto')) {
                // Save original to temp
                $tempPath = $request->file('foto')->store('temp', 'public');

                // Dispatch background optimization
                \App\Jobs\ProcessImageOptimization::dispatch(
                    $tempPath,
                    'kos',
                    $kos,
                    'foto'
                );
            }
        } elseif ($editField === 'alamat') {
            $rules['alamat'] = 'nullable|string|unique:kos,alamat,' . $kos->id;
            $updateData['alamat'] = $request->alamat;
        } elseif ($editField === 'kategori') {
            $rules['kategori'] = 'required|in:putra,putri,campur';
            $updateData['kategori'] = $request->kategori;
        } elseif ($editField === 'kota') {
            $rules['kota'] = 'required|string|max:100';
            $updateData['kota'] = $request->kota;
            $updateData['nama_kota'] = $request->kota;
        } elseif ($editField === 'kode_kos' && !$kos->is_kode_kos_edited) {
            $rules['kode_kos'] = 'required|numeric|digits_between:1,4|unique:kos,kode_kos,' . $kos->id;
            $updateData['kode_kos'] = $request->kode_kos;
            $updateData['is_kode_kos_edited'] = true;
        } else {
            // Default behavior if editField is missing (full update fallback)
            $rules = [
                'nama_kos' => 'required|string|max:255',
                'alamat' => 'nullable|string|unique:kos,alamat,' . $kos->id,
                'kategori' => 'required|in:putra,putri,campur',
            ];
            $updateData = $request->only(['nama_kos', 'alamat', 'kategori']);
        }

        $request->validate($rules, [
            'alamat.unique' => 'Lokasi ini sudah terdaftar untuk kos lain.',
            'kode_kos.unique' => 'Kode kos ini sudah digunakan.',
            'kode_kos.digits_between' => 'Kode kos maksimal 4 angka.',
            'kategori.in' => 'Kategori tidak valid.',
        ]);

        $kos->update($updateData);

        // Batch price update is separate logic
        if ($request->filled('harga_batch')) {
            $kos->kamars()->update(['harga' => $request->harga_batch]);
        }

        return back()->with('success', 'Data properti berhasil diperbarui!');
    }
}

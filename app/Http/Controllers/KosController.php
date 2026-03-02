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
        $editField = $request->editField;
        $rules = [];
        $updateData = [];

        if ($editField === 'nama_kos') {
            $rules['nama_kos'] = 'required|string|max:255';
            $updateData['nama_kos'] = $request->nama_kos;
        } elseif ($editField === 'alamat') {
            $rules['alamat'] = 'nullable|string|unique:kos,alamat,' . $kos->id;
            $updateData['alamat'] = $request->alamat;
        } elseif ($editField === 'kategori') {
            $rules['kategori'] = 'required|in:putra,putri,campur';
            $updateData['kategori'] = $request->kategori;
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

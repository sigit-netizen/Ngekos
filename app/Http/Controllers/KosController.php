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
        $rules = [
            'nama_kos' => 'required|string|max:255',
            'alamat' => 'nullable|string|unique:kos,alamat,' . $kos->id,
            'harga_batch' => 'nullable|numeric|min:0',
            'kategori' => 'required|in:putra,putri,campur',
        ];

        // Only allow kode_kos update if it hasn't been edited before
        if (!$kos->is_kode_kos_edited) {
            $rules['kode_kos'] = 'required|numeric|digits_between:1,4|unique:kos,kode_kos,' . $kos->id;
        }

        $request->validate($rules, [
            'alamat.unique' => 'Lokasi ini sudah terdaftar untuk kos lain.',
            'kode_kos.unique' => 'Kode kos ini sudah digunakan.',
            'kode_kos.digits_between' => 'Kode kos maksimal 4 angka.',
            'kategori.in' => 'Kategori tidak valid.',
        ]);

        $updateData = [
            'nama_kos' => $request->nama_kos,
            'alamat' => $request->alamat,
            'kategori' => $request->kategori,
        ];

        if (!$kos->is_kode_kos_edited && $request->has('kode_kos')) {
            $updateData['kode_kos'] = $request->kode_kos;
            $updateData['is_kode_kos_edited'] = true;
        }

        $kos->update($updateData);

        // If a batch price update is requested, update all associated rooms
        if ($request->filled('harga_batch')) {
            $kos->kamars()->update(['harga' => $request->harga_batch]);
        }

        return back()->with('success', 'Data properti berhasil diperbarui!');
    }
}

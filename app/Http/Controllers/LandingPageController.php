<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisLangganan;

class LandingPageController extends Controller
{
    /**
     * Tampilkan halaman arahan (landing page) dengan paket harga.
     */
    public function index()
    {
        // Ambil semua paket dari database
        $plans = JenisLangganan::all();

        // Pemetaan paket khusus agar mudah diakses di Blade
        $pricing = [
            'premium' => $plans->where('nama', 'MEMBER PREMIUM')->first(),
            'pro' => $plans->where('nama', 'MEMBER PRO')->first(),
            'perKamarPremium' => $plans->where('nama', 'PER KAMAR PREMIUM')->first(),
            'perKamarPro' => $plans->where('nama', 'PER KAMAR PRO')->first(),
            'biasa' => $plans->where('nama', 'MEMBER BIASA')->first(),
        ];

        // Data testimoni statis
        $testimonials = [
            ['name' => 'Bu Sari', 'role' => '3 Kos di Jakarta', 'text' => 'Dulu stress ngecek pembayaran manual. Sekarang tinggal buka HP, langsung tau siapa yang udah bayar. Hemat waktu banget!', 'avatar' => '1'],
            ['name' => 'Pak Budi', 'role' => '5 Kos di Bandung', 'text' => 'Awalnya ragu. Tapi begitu coba, ternyata gampang banget. Anak kos juga seneng karena bisa bayar online.', 'avatar' => '2'],
            ['name' => 'Mbak Rina', 'role' => '2 Kos di Jogja', 'text' => 'Fitur komplain-nya juara! Langsung tau kalau ada yang rusak, gak perlu nunggu chat berkali-kali.', 'avatar' => '3'],
            ['name' => 'Pak Herman', 'role' => 'Kos Mahasiswa Depok', 'text' => 'Setup cuma 5 menit, langsung bisa pakai. Interface simpel, istri saya yang gaptek aja bisa operasikan.', 'avatar' => '4'],
            ['name' => 'Bu Dewi', 'role' => 'Kos Putri Surabaya', 'text' => 'Data KTP penghuni tersimpan rapi. Kalau ada apa-apa, tinggal buka aplikasi. Aman dan praktis!', 'avatar' => '5'],
            ['name' => 'Mas Andi', 'role' => '50+ Kamar di Malang', 'text' => 'Harga terjangkau, fitur lengkap. Dulu pakai aplikasi lain kena charge per kamar, bisa jutaan sebulan.', 'avatar' => '6']
        ];

        // Ambil data kos asli untuk rekomendasi
        $highlightKos = \App\Models\Kos::with(['kamars'])
            ->has('kamars')
            ->latest()
            ->take(5)
            ->get();

        return view('welcome', compact('pricing', 'testimonials', 'highlightKos'));
    }
}

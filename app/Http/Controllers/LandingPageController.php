<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisLangganan;

class LandingPageController extends Controller
{
    /**
     * Display the landing page with pricing plans.
     */
    public function index()
    {
        // Fetch all plans from database
        $plans = JenisLangganan::all();

        // Specific plan mapping for easy access in Blade
        $pricing = [
            'premium' => $plans->where('nama', 'MEMBER PREMIUM')->first(),
            'pro' => $plans->where('nama', 'MEMBER PRO')->first(),
            'perKamarPremium' => $plans->where('nama', 'PER KAMAR PREMIUM')->first(),
            'perKamarPro' => $plans->where('nama', 'PER KAMAR PRO')->first(),
            'biasa' => $plans->where('nama', 'MEMBER BIASA')->first(),
        ];

        // Static testimonials data
        $testimonials = [
            ['name' => 'Bu Sari', 'role' => '3 Kos di Jakarta', 'text' => 'Dulu stress ngecek pembayaran manual. Sekarang tinggal buka HP, langsung tau siapa yang udah bayar. Hemat waktu banget!', 'avatar' => '1'],
            ['name' => 'Pak Budi', 'role' => '5 Kos di Bandung', 'text' => 'Awalnya ragu. Tapi begitu coba, ternyata gampang banget. Anak kos juga seneng karena bisa bayar online.', 'avatar' => '2'],
            ['name' => 'Mbak Rina', 'role' => '2 Kos di Jogja', 'text' => 'Fitur komplain-nya juara! Langsung tau kalau ada yang rusak, gak perlu nunggu chat berkali-kali.', 'avatar' => '3'],
            ['name' => 'Pak Herman', 'role' => 'Kos Mahasiswa Depok', 'text' => 'Setup cuma 5 menit, langsung bisa pakai. Interface simpel, istri saya yang gaptek aja bisa operasikan.', 'avatar' => '4'],
            ['name' => 'Bu Dewi', 'role' => 'Kos Putri Surabaya', 'text' => 'Data KTP penghuni tersimpan rapi. Kalau ada apa-apa, tinggal buka aplikasi. Aman dan praktis!', 'avatar' => '5'],
            ['name' => 'Mas Andi', 'role' => '50+ Kamar di Malang', 'text' => 'Harga terjangkau, fitur lengkap. Dulu pakai aplikasi lain kena charge per kamar, bisa jutaan sebulan.', 'avatar' => '6']
        ];

        return view('welcome', compact('pricing', 'testimonials'));
    }
}

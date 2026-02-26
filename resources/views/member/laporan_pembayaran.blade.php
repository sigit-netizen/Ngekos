@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Laporan Transaksi Pembayaran 💵</h1>
        <p class="text-gray-500">Pusat mutasi dana uang masuk dari tagihan sewa bulanan penyewa Anda.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4">
            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Jurnal Transaksi Tunai/Transfer</h3>
        <p class="text-gray-500">Lihat bukti bayar dan verifikasi pembayaran bulanan secara instan.</p>
    </div>
@endsection
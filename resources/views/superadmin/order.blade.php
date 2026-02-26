@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Manajemen Order Server 📦</h1>
        <p class="text-gray-500">Pantau pemesanan transaksi paket member yang dilakukan oleh pemilik kos.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-50 mb-4">
            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Riwayat Pemesanan Paket (Plans)</h3>
        <p class="text-gray-500">Validasi semua pembayaran paket langganan fitur admin/member jika ada yang pending.</p>
    </div>
@endsection
@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Konfirmasi Order Penyewa 🛒</h1>
        <p class="text-gray-500">List pengajuan pesanan masuk atau antrean sewa dari pendaftar anak kos baru.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up" data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-50 mb-4">
            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Pending Request Order</h3>
        <p class="text-gray-500">Verifikasi calon pengguna yang mem-booking ruangan dari etalase website pencarian.</p>
    </div>
@endsection

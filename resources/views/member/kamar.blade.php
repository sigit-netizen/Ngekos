@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Manajemen Kamar Kos 🛏️</h1>
        <p class="text-gray-500">Tambah, ubah, atau hapus ketersediaan kamar di seluruh properti kos Anda.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#36B2B2]/10 mb-4">
            <svg class="w-8 h-8 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Kamar Terdaftar</h3>
        <p class="text-gray-500">Mulai input data kamar lengkap dengan harga, ketersediaan, dan fasilitasnya.</p>
    </div>
@endsection
@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Cabang Kos 🏘️</h1>
        <p class="text-gray-500">Kelola dan lihat aset lokasi multi-cabang bisnis kos-kosan Anda.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Tambah Lokasi Gedung Baru</h3>
        <p class="text-gray-500">Untuk mengelompokkan kamar berdasarkan area/bangunan agar lebih tertata.</p>
    </div>
@endsection
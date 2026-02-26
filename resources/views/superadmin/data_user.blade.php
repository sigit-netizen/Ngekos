@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Data User 👤</h1>
        <p class="text-gray-500">Lihat semua akun penyewa atau pencari kos biasa yang aktif menggunakan aplikasi.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#36B2B2]/10 mb-4">
            <svg class="w-8 h-8 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Tabel Pengguna Umum</h3>
        <p class="text-gray-500">Seluruh laporan dan aktivitas dari para pengguna ujung (End-Users) akan terpusat di sini.
        </p>
    </div>
@endsection
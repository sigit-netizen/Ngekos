@extends('layouts.dashboard')

@section('dashboard-content')
    <!-- Welcome Banner -->
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-sm border border-white/50 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6"
        data-aos="fade-up" data-aos-duration="800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Selamat datang, Superadmin! 👋</h1>
            <p class="text-gray-500">Akses kontrol sistem dan kelola hak akses setiap role secara terpusat di halaman
                administrator tertinggi ini.</p>
        </div>
        <div class="hidden sm:block shrink-0">
            <div
                class="h-20 w-20 rounded-2xl bg-gradient-to-br from-[#36B2B2] to-[#2b8f8f] flex items-center justify-center shadow-lg shadow-[#36B2B2]/30 animate-pulse">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
            </div>
        </div>
    </div>
@endsection
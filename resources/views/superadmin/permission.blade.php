@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Sistem Akses & Permission 🔑</h1>
        <p class="text-gray-500">Atur hak akses dan konfigurasi tingkat administrator menggunakan Spatie Roles &
            Permissions.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-50 mb-4">
            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Tabel Izin (Permission) Tertutup</h3>
        <p class="text-gray-500">Panel ini adalah alat sensitif untuk memberikan atau mencabut kunci akses ke menu atau URL
            khusus.</p>
    </div>
@endsection
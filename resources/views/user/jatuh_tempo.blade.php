@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Jatuh Tempo ⏰</h1>
        <p class="text-gray-500">Selalu perhatikan tanggal pembayaran dan sisa tempo sewa Anda agar tidak terkena denda.</p>
    </div>

    <!-- Content Placeholder -->
    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm" data-aos="fade-up"
        data-aos-delay="100">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4">
            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Semua Aman!</h3>
        <p class="text-gray-500">Anda tidak memiliki tagihan kos yang mendekati waktu jatuh tempo saat ini.</p>
    </div>
@endsection
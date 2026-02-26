@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Aduan Fasilitas 🛠️</h1>
        <p class="text-gray-500">Sampaikan keluhan atau masalah terkait fasilitas kos langsung kepada pemilik.</p>
    </div>

    <!-- Content Placeholder -->
    <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm" data-aos="fade-up" data-aos-delay="100">
        <form class="space-y-6 max-w-2xl mx-auto">
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Judul Laporan</label>
                <input type="text"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#36B2B2]/20 focus:border-[#36B2B2] transition-colors"
                    placeholder="Cth: AC Kamar Bocor">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Deskripsi Kerusakan</label>
                <textarea rows="4"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#36B2B2]/20 focus:border-[#36B2B2] transition-colors"
                    placeholder="Jelaskan secara detail bagian yang berlum berfungsi..."></textarea>
            </div>

            <button type="button"
                class="px-6 py-3 bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f] text-white rounded-xl font-semibold shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5 w-full sm:w-auto">
                Kirim Aduan
            </button>
        </form>
    </div>
@endsection
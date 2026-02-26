@extends('layouts.dashboard')

@section('dashboard-content')
                <!-- Welcome Banner -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-sm border border-white/50 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6"
                    data-aos="fade-up" data-aos-duration="800">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Selamat datang kembali, Pengguna! 👋</h1>
                        <p class="text-gray-500">Siap mencari kos incaranmu hari ini? Ada beberapa update terbaru untukmu.
                        </p>
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

                <!-- Quick Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10" data-aos="fade-up"
                    data-aos-delay="100" data-aos-duration="800">
                    <!-- Stat 1 -->
                    <div
                        class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm hover:shadow-md border border-gray-100/50 transition-all duration-300 hover:-translate-y-1 group">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="h-12 w-12 rounded-xl bg-[#36B2B2]/10 flex items-center justify-center text-[#36B2B2] group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900">12</h3>
                        <p class="text-sm font-medium text-gray-500 mt-1">Kos Tersimpan</p>
                    </div>

                    <!-- Stat 2 -->
                    <div
                        class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm hover:shadow-md border border-gray-100/50 transition-all duration-300 hover:-translate-y-1 group">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="h-12 w-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900">1</h3>
                        <p class="text-sm font-medium text-gray-500 mt-1">Menunggu Persetujuan</p>
                    </div>

                    <!-- Stat 3 -->
                    <div
                        class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm hover:shadow-md border border-gray-100/50 transition-all duration-300 hover:-translate-y-1 group sm:col-span-2 lg:col-span-1">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="h-12 w-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Kos Melati</h3>
                        <p class="text-sm font-medium text-gray-500 mt-1">Sewa Aktif Berjalan</p>
                    </div>
                </div>

                <!-- Recommendations Section -->
                <div class="mb-10" data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Rekomendasi Untukmu</h2>
                        <a href="#"
                            class="text-sm font-semibold text-[#36B2B2] hover:text-[#2b8f8f] transition-colors flex items-center gap-1 group">
                            Lihat Semua
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                        <!-- Card 1 -->
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer">
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=500&q=80"
                                    alt="Room"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-yellow-500 flex items-center gap-1 shadow-sm">
                                    ★ 4.8
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 mb-1 group-hover:text-[#36B2B2] transition-colors">Kos
                                    Mawar Indah</h3>
                                <p class="text-sm text-gray-500 mb-3 flex items-center gap-1">
                                    📍 Jakarta Selatan
                                </p>
                                <div class="flex items-center gap-2 mb-4">
                                    <span
                                        class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-md">AC</span>
                                    <span
                                        class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-md">WiFi</span>
                                </div>
                                <div class="flex items-end justify-between border-t border-gray-100 pt-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-0.5">Mulai dari</p>
                                        <p class="font-bold text-[#36B2B2]">Rp 1.500.000<span
                                                class="text-xs text-gray-500 font-normal">/bln</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer">
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?w=500&q=80" alt="Room"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-yellow-500 flex items-center gap-1 shadow-sm">
                                    ★ 4.5
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 mb-1 group-hover:text-[#36B2B2] transition-colors">Kos
                                    Eksekutif</h3>
                                <p class="text-sm text-gray-500 mb-3 flex items-center gap-1">
                                    📍 Jakarta Pusat
                                </p>
                                <div class="flex items-center gap-2 mb-4">
                                    <span
                                        class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-md">AC</span>
                                    <span
                                        class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-md">Campur</span>
                                </div>
                                <div class="flex items-end justify-between border-t border-gray-100 pt-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-0.5">Mulai dari</p>
                                        <p class="font-bold text-[#36B2B2]">Rp 2.800.000<span
                                                class="text-xs text-gray-500 font-normal">/bln</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer hidden sm:block">
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1502672260266-1c1de2d9d0da?w=500&q=80"
                                    alt="Room"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-yellow-500 flex items-center gap-1 shadow-sm">
                                    ★ 4.9
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 mb-1 group-hover:text-[#36B2B2] transition-colors">Kos
                                    Asri</h3>
                                <p class="text-sm text-gray-500 mb-3 flex items-center gap-1">
                                    📍 Depok
                                </p>
                                <div class="flex items-center gap-2 mb-4">
                                    <span
                                        class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-md">WiFi</span>
                                </div>
                                <div class="flex items-end justify-between border-t border-gray-100 pt-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-0.5">Mulai dari</p>
                                        <p class="font-bold text-[#36B2B2]">Rp 900.000<span
                                                class="text-xs text-gray-500 font-normal">/bln</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer hidden xl:block">
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1598928506311-c55dedbfc1f9?w=500&q=80"
                                    alt="Room"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-yellow-500 flex items-center gap-1 shadow-sm">
                                    ★ 4.7
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 mb-1 group-hover:text-[#36B2B2] transition-colors">Kos
                                    Meruya</h3>
                                <p class="text-sm text-gray-500 mb-3 flex items-center gap-1">
                                    📍 Jakarta Barat
                                </p>
                                <div class="flex items-center gap-2 mb-4">
                                    <span
                                        class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-md">Kasur</span>
                                </div>
                                <div class="flex items-end justify-between border-t border-gray-100 pt-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-0.5">Mulai dari</p>
                                        <p class="font-bold text-[#36B2B2]">Rp 1.200.000<span
                                                class="text-xs text-gray-500 font-normal">/bln</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spacer for bottom -->
                <div class="h-10"></div>
@endsection
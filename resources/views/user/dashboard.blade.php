@extends('layouts.dashboard')

@section('dashboard-content')
    <style>
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #36B2B2 #f8fafc;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #36B2B2;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #2b8f8f;
        }

        .no-scroll,
        .no-scroll body {
            overflow: hidden !important;
            height: 100dvh !important;
            touch-action: none;
        }
    </style>
    @php
        $user = auth()->user();
        $isPenyewa = $user->isPenyewa();
    @endphp

    @if($isPenyewa)
        @can('fitur.sudah_sewa')
            {{-- ============================================ --}}
            {{-- DASHBOARD PENYEWA (Verified Tenant) --}}
            {{-- ============================================ --}}
            @php
                $kosData = $user->kosAnak;
                $kamarData = $user->kamar;
            @endphp

            <!-- Welcome Banner Penyewa -->
            <div class="bg-gradient-to-br from-[#36B2B2]/10 to-emerald-50 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-sm border border-[#36B2B2]/20 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6"
                data-aos="fade-up" data-aos-duration="800">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-[#36B2B2] text-white">
                            ✓ Penyewa Aktif
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Selamat datang, {{ $user->name }}! 🏠</h1>
                    <p class="text-gray-500">Berikut informasi kos dan kamar Anda.</p>
                </div>
                <div class="hidden sm:block shrink-0">
                    <div
                        class="h-20 w-20 rounded-2xl bg-gradient-to-br from-[#36B2B2] to-emerald-500 flex items-center justify-center shadow-lg shadow-[#36B2B2]/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10" data-aos="fade-up" data-aos-delay="100"
                data-aos-duration="800">
                <!-- Kos Info -->
                <div
                    class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm hover:shadow-md border border-gray-100/50 transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="h-12 w-12 rounded-xl bg-[#36B2B2]/10 flex items-center justify-center text-[#36B2B2] group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-[#36B2B2] bg-[#36B2B2]/10 px-2 py-1 rounded-full">#{{ $kosData->kode_kos ?? '-' }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $kosData->nama_kos ?? 'N/A' }}</h3>
                    <p class="text-sm font-medium text-gray-500 mt-1">{{ $kosData->alamat ?? 'N/A' }}</p>
                </div>

                <!-- Kamar Info -->
                <div
                    class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm hover:shadow-md border border-gray-100/50 transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="h-12 w-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900">Kamar {{ $kamarData->nomor_kamar ?? '-' }}</h3>
                    <p class="text-sm font-medium text-gray-500 mt-1">Nomor Kamar Anda</p>
                </div>

                <!-- Harga -->
                <div
                    class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm hover:shadow-md border border-gray-100/50 transition-all duration-300 hover:-translate-y-1 group sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="h-12 w-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($kamarData->harga ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-sm font-medium text-gray-500 mt-1">Harga Sewa / Bulan</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up"
                data-aos-delay="200">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('user.jatuh_tempo') }}"
                        class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-[#36B2B2]/30 hover:bg-[#36B2B2]/5 transition-all duration-300 group">
                        <div
                            class="h-10 w-10 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Jatuh Tempo</p>
                            <p class="text-xs text-gray-500">Cek jadwal pembayaran</p>
                        </div>
                    </a>
                    <a href="{{ route('user.aduan') }}"
                        class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-[#36B2B2]/30 hover:bg-[#36B2B2]/5 transition-all duration-300 group">
                        <div
                            class="h-10 w-10 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-500 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Aduan Fasilitas</p>
                            <p class="text-xs text-gray-500">Laporkan masalah</p>
                        </div>
                    </a>
                    <a href="{{ route('user.order') }}"
                        class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-[#36B2B2]/30 hover:bg-[#36B2B2]/5 transition-all duration-300 group">
                        <div
                            class="h-10 w-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Riwayat Order</p>
                            <p class="text-xs text-gray-500">Lihat semua order</p>
                        </div>
                    </a>
                </div>
            </div>

        @endcan
    @else
        @can('fitur.belum_sewa')
            {{-- ============================================ --}}
            {{-- DASHBOARD USER BIASA (Not Yet Tenant) --}}
            {{-- ============================================ --}}

            <!-- Welcome Banner -->
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-sm border border-white/50 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6"
                data-aos="fade-up" data-aos-duration="800">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Selamat datang, {{ $user->name }}! 👋</h1>
                    <p class="text-gray-500">Temukan kos impianmu dengan filter pencarian di bawah ini.</p>
                </div>
                <div class="hidden sm:block shrink-0">
                    <div
                        class="h-20 w-20 rounded-2xl bg-gradient-to-br from-[#36B2B2] to-[#2b8f8f] flex items-center justify-center shadow-lg shadow-[#36B2B2]/30 animate-pulse">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Order Status Card -->
            @php
                $latestOrder = \App\Models\Transaksi::where('id_user', $user->id)
                    ->latest()
                    ->with(['kamar.kos'])
                    ->first();

                if (!$latestOrder) {
                    $orderStatus = 'belum_order';
                } elseif ($latestOrder->status === 'pending') {
                    $orderStatus = 'pending';
                } elseif ($latestOrder->status === 'verified') {
                    $orderStatus = 'verified';
                } else {
                    $orderStatus = 'belum_order'; // rejected = can order again
                }
            @endphp

            @if($orderStatus === 'verified')
                {{-- Terverifikasi: auto-reload will show penyewa dashboard --}}
                <div class="bg-gradient-to-br from-emerald-500/10 to-emerald-50 rounded-2xl p-6 shadow-sm border border-emerald-200/50 mb-8"
                    data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-4">
                        <div
                            class="h-14 w-14 rounded-2xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-black text-gray-900">Order Terverifikasi! 🎉</h3>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">VERIFIED</span>
                            </div>
                            <p class="text-sm text-gray-500">Selamat! Anda telah menjadi penyewa. Silakan muat ulang halaman untuk
                                melihat dashboard penyewa.</p>
                        </div>
                        <a href="{{ route('user.dashboard') }}"
                            class="shrink-0 px-5 py-2.5 bg-emerald-500 text-white font-bold text-sm rounded-xl hover:bg-emerald-600 transition-all shadow-md hover:shadow-lg active:scale-95">
                            Muat Ulang
                        </a>
                    </div>
                </div>
            @elseif($orderStatus === 'pending')
                {{-- Pending: menunggu verifikasi admin --}}
                <div class="bg-gradient-to-br from-amber-500/10 to-amber-50 rounded-2xl p-6 shadow-sm border border-amber-200/50 mb-8"
                    data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-14 w-14 rounded-2xl bg-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30 shrink-0">
                            <svg class="w-7 h-7 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-black text-gray-900">Menunggu Verifikasi</h3>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-100 text-amber-600">PENDING</span>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">Order Anda sedang diproses oleh admin. Harap tunggu verifikasi.</p>
                            <div class="flex flex-wrap gap-3 text-xs">
                                <span
                                    class="flex items-center gap-1 text-gray-600 font-medium bg-white px-3 py-1.5 rounded-lg border border-gray-100">
                                    🏠 {{ $latestOrder->kamar->kos->nama_kos ?? '-' }}
                                </span>
                                <span
                                    class="flex items-center gap-1 text-gray-600 font-medium bg-white px-3 py-1.5 rounded-lg border border-gray-100">
                                    🚪 Kamar {{ $latestOrder->kamar->nomor_kamar ?? '-' }}
                                </span>
                                <span
                                    class="flex items-center gap-1 text-[#36B2B2] font-bold bg-white px-3 py-1.5 rounded-lg border border-gray-100">
                                    💰 Rp {{ number_format($latestOrder->jumlah_bayar ?? 0, 0, ',', '.') }}/bln
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Belum Order --}}
                <div class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm border border-gray-100/50 mb-8" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-black text-gray-900">Belum Ada Order</h3>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-gray-100 text-gray-500">INACTIVE</span>
                            </div>
                            <p class="text-sm text-gray-500">Cari kos di bawah dan pilih kamar untuk memulai order pertama Anda.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Search Kos Section with Filters -->
            <div class="mb-10" x-data="kosSearch()" data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">

                <div class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-sm border border-gray-100/50">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Kos
                    </h2>

                    <!-- Filter Row -->
                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                        <!-- Lokasi -->
                        <div class="flex-1">
                            <input type="text" x-model="filters.lokasi" placeholder="🔍 Cari lokasi / alamat..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#36B2B2]/30 focus:border-[#36B2B2] transition-all text-sm font-medium">
                        </div>

                        <!-- Rentang Harga -->
                        <div class="sm:w-48">
                            <select x-model="filters.harga"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#36B2B2]/30 focus:border-[#36B2B2] transition-all text-sm font-medium bg-white appearance-none cursor-pointer">
                                <option value="">Semua Harga</option>
                                <option value="0-500000">
                                    < Rp 500rb</option>
                                <option value="500000-1000000">Rp 500rb - 1jt</option>
                                <option value="1000000-2000000">Rp 1jt - 2jt</option>
                                <option value="2000000-3000000">Rp 2jt - 3jt</option>
                                <option value="3000000-5000000">Rp 3jt - 5jt</option>
                                <option value="5000000-99999999">> Rp 5jt</option>
                            </select>
                        </div>

                        <!-- Kategori -->
                        <div class="sm:w-40">
                            <select x-model="filters.kategori"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#36B2B2]/30 focus:border-[#36B2B2] transition-all text-sm font-medium bg-white appearance-none cursor-pointer">
                                <option value="">Semua Tipe</option>
                                <option value="putra">🧑 Putra</option>
                                <option value="putri">👩 Putri</option>
                                <option value="campur">👥 Campur</option>
                            </select>
                        </div>
                    </div>

                    <!-- City Chips -->
                    <div class="flex flex-wrap gap-2 mb-5">
                        <p class="w-full text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Kota Populer
                        </p>
                        <template x-for="city in ['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Malang', 'Semarang']"
                            :key="city">
                            <button @click="filters.lokasi = city; search()"
                                :class="filters.lokasi === city ? 'bg-[#36B2B2] text-white border-[#36B2B2]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#36B2B2]/50'"
                                class="px-3 py-1.5 rounded-full border text-xs font-bold transition-all active:scale-95"
                                x-text="city"></button>
                        </template>
                        <button @click="filters.is_favorit_only = !filters.is_favorit_only; search()"
                            :class="filters.is_favorit_only ? 'bg-rose-500 text-white border-rose-500' : 'bg-white text-rose-500 border-rose-200 hover:border-rose-300'"
                            class="px-3 py-1.5 rounded-full border text-xs font-bold transition-all active:scale-95 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                            Favorit Saya
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button @click="search()" :disabled="loading"
                            class="flex-1 sm:flex-none px-6 py-3 bg-[#36B2B2] text-white font-bold text-sm rounded-xl hover:bg-[#2b8f8f] transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg shadow-[#36B2B2]/30">
                            <template x-if="loading">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                    </path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Mencari...' : 'Cari Kos'"></span>
                        </button>
                        <button @click="resetFilters()"
                            class="px-4 py-3 bg-gray-100 text-gray-600 font-bold text-sm rounded-xl hover:bg-gray-200 transition-all duration-300">
                            Reset
                        </button>
                    </div>

                    <!-- Error Message -->
                    <template x-if="error">
                        <div class="mt-4 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm font-medium"
                            x-text="error"></div>
                    </template>
                </div>

                <!-- Recommendations Section (Shown before search) -->
                <div x-show="!searchPerformed && kosList.length > 0" class="mt-8" data-aos="fade-up">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-8 bg-[#36B2B2] rounded-full"></span>
                            <h2 class="text-xl font-black text-gray-900 leading-tight">Rekomendasi Kos Terpopuler ✨</h2>
                        </div>
                        <span class="hidden sm:block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pilihan
                            Terbaik</span>
                    </div>

                    <div class="flex overflow-x-auto gap-5 pb-8 snap-x hide-scrollbar" style="scrollbar-width: none;">
                        <template x-for="(kos, index) in kosList.slice(0, 4)" :key="'rec-'+kos.id">
                            <!-- Small Recommendation Card -->
                            <div
                                class="w-[280px] shrink-0 snap-center bg-white rounded-[2rem] p-4 border border-gray-100 hover:border-[#36B2B2]/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 group/rec">
                                <!-- Image Header -->
                                <div class="relative h-40 rounded-2xl overflow-hidden mb-4 shadow-inner bg-gray-50">
                                    <div class="absolute top-3 left-3 z-10">
                                        <span
                                            class="px-3 py-1 bg-[#36B2B2] text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">Featured</span>
                                    </div>
                                    <template x-if="kos.kamars && kos.kamars[0] && kos.kamars[0].foto">
                                        <img :src="kos.kamars[0].foto.startsWith('http') ? kos.kamars[0].foto : (kos.kamars[0].foto.startsWith('/') ? kos.kamars[0].foto : '/images/kamar/' + kos.kamars[0].foto)"
                                            class="w-full h-full object-cover group-hover/rec:scale-110 transition-transform duration-700">
                                    </template>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent">
                                    </div>
                                    <div class="absolute bottom-3 left-3">
                                        <span
                                            class="text-[9px] font-black text-white uppercase tracking-widest bg-black/20 backdrop-blur-md px-2 py-0.5 rounded-md"
                                            x-text="kos.kota || kos.nama_kota || 'Lokasi'"></span>
                                    </div>
                                </div>

                                <!-- Info Box -->
                                <div class="px-1">
                                    <h3 class="font-black text-gray-900 text-base leading-tight mb-1 truncate"
                                        x-text="kos.nama_kos"></h3>
                                    <div class="flex items-center gap-1.5 mb-4 text-gray-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-[11px] font-bold truncate" x-text="kos.alamat"></span>
                                    </div>

                                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-50">
                                        <div class="flex flex-col">
                                            <span class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Mulai
                                                Dari</span>
                                            <div class="flex items-baseline gap-0.5">
                                                <span class="text-[10px] font-black text-[#36B2B2]">Rp</span>
                                                <span class="text-base font-black text-gray-900 leading-none"
                                                    x-text="(kos.kamars && kos.kamars[0] ? Number(kos.kamars[0].harga).toLocaleString('id-ID') : '0')"></span>
                                            </div>
                                        </div>
                                        <button
                                            @click="filters.lokasi = kos.nama_kos; search(); document.getElementById('search-results').scrollIntoView({behavior: 'smooth'})"
                                            class="px-4 py-2.5 bg-gray-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-[#36B2B2] transition-all active:scale-95 shadow-lg shadow-gray-200">
                                            Cek Unit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="search-results">
                    <template x-if="searchPerformed && kosList.length > 0">
                        <div class="mt-6 space-y-4">
                            <!-- Result Count -->
                            <div class="flex items-center justify-between px-1">
                                <p class="text-sm font-bold text-gray-500">
                                    Ditemukan <span class="text-[#36B2B2]" x-text="kosList.length"></span> kos
                                </p>
                            </div>

                            <!-- Kos Cards -->
                            <template x-for="kos in kosList" :key="kos.id">
                                <div
                                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300">
                                    <!-- Kos Header -->
                                    <div class="p-5 bg-gradient-to-r from-[#36B2B2]/5 to-emerald-50/50 border-b border-gray-100 cursor-pointer"
                                        @click="kos._expanded = !kos._expanded">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="h-12 w-12 rounded-xl bg-[#36B2B2] flex items-center justify-center text-white font-black text-lg shadow-md shadow-[#36B2B2]/30 shrink-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-black text-gray-900" x-text="kos.nama_kos"></h3>
                                                    <p class="text-sm text-gray-500 flex items-center gap-1">
                                                        📍 <span x-text="kos.alamat || 'Alamat tidak tersedia'"></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0">
                                                <!-- Favorite Button -->
                                                <button @click.stop="toggleFavorit(kos)"
                                                    class="h-10 w-10 rounded-full flex items-center justify-center transition-all active:scale-75 shadow-sm border"
                                                    :class="kos.is_favorit ? 'bg-rose-50 text-rose-500 border-rose-100' : 'bg-white text-gray-300 border-gray-100 hover:text-rose-400'">
                                                    <svg class="w-5 h-5" :fill="kos.is_favorit ? 'currentColor' : 'none'"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <template x-if="kos.kategori">
                                                    <span
                                                        class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full"
                                                        :class="kos.kategori === 'putra' ? 'bg-blue-100 text-blue-600' : (kos.kategori === 'putri' ? 'bg-pink-100 text-pink-600' : 'bg-purple-100 text-purple-600')"
                                                        x-text="kos.kategori === 'putra' ? '🧑 Putra' : (kos.kategori === 'putri' ? '👩 Putri' : '👥 Campur')"></span>
                                                </template>
                                                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                                                    <span x-text="kos.kamars.length"></span> kamar
                                                </span>
                                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                                    :class="kos._expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kamar List (Expandable) -->
                                    <div x-show="kos._expanded" x-transition class="p-5">
                                        <h4
                                            class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span class="w-1.5 h-4 bg-[#36B2B2] rounded-full"></span>
                                            Kamar Tersedia
                                        </h4>

                                        <template x-if="kos.kamars.length === 0">
                                            <div class="text-center py-8">
                                                <p class="text-gray-400 text-sm font-medium">Tidak ada kamar tersedia saat ini.</p>
                                            </div>
                                        </template>

                                        <div class="flex overflow-x-auto gap-4 pb-6 snap-x hide-scrollbar"
                                            style="scrollbar-width: thin;" x-ref="slider"
                                            @mousedown="isDown = true; startX = $event.pageX - $refs.slider.offsetLeft; scrollLeft = $refs.slider.scrollLeft; $refs.slider.classList.add('cursor-grabbing')"
                                            @mouseleave="isDown = false; $refs.slider.classList.remove('cursor-grabbing')"
                                            @mouseup="isDown = false; $refs.slider.classList.remove('cursor-grabbing')"
                                            @mousemove="if(!isDown) return; $event.preventDefault(); const x = $event.pageX - $refs.slider.offsetLeft; const walk = (x - startX) * 2; $refs.slider.scrollLeft = scrollLeft - walk;">
                                            <template x-for="kamar in kos.kamars" :key="kamar.id">
                                                <!-- Room Card -->
                                                <div x-data="{ showDetail: false }"
                                                    class="w-[85vw] sm:w-[320px] shrink-0 snap-center bg-gray-50/50 rounded-2xl p-4 border border-gray-100 hover:border-[#36B2B2]/30 hover:bg-white transition-all duration-300 group/room cursor-pointer">
                                                    <div class="flex flex-col gap-4">
                                                        <!-- Room Image -->
                                                        <div
                                                            class="w-full h-32 rounded-xl overflow-hidden bg-white border border-gray-100 shrink-0 relative group-hover/room:shadow-md transition-all">
                                                            <template x-if="kamar.foto">
                                                                <img :src="kamar.foto.startsWith('http') ? kamar.foto : (kamar.foto.startsWith('/') ? kamar.foto : '/images/kamar/' + kamar.foto)"
                                                                    class="w-full h-full object-cover group-hover/room:scale-110 transition-transform duration-500">
                                                            </template>
                                                            <template x-if="!kamar.foto">
                                                                <div
                                                                    class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-300">
                                                                    <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="1.5"
                                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="text-[8px] font-black uppercase tracking-widest">No
                                                                        Image</span>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <span
                                                                    class="text-base font-black text-gray-900 group-hover/room:text-[#36B2B2] transition-colors"
                                                                    x-text="'Kamar ' + kamar.nomor_kamar"></span>
                                                                <span
                                                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-600">Tersedia</span>
                                                            </div>

                                                            <div class="text-right mb-3">
                                                                <p
                                                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">
                                                                    Per Bulan</p>
                                                                <div class="flex items-baseline justify-end gap-1">
                                                                    <span class="text-xs font-black text-[#36B2B2]">Rp</span>
                                                                    <span class="text-xl font-black text-gray-900 tracking-tight"
                                                                        x-text="Number(kamar.harga).toLocaleString('id-ID')"></span>
                                                                </div>
                                                            </div>

                                                            <!-- Detail Fasilitas Toggle -->
                                                            <template x-if="kamar.fasilitas && kamar.fasilitas.length > 0">
                                                                <div class="mb-4">
                                                                    <button @click="showDetail = !showDetail" type="button"
                                                                        class="flex items-center gap-1.5 text-xs font-bold text-[#36B2B2] hover:text-[#2b8f8f] transition-colors">
                                                                        <svg class="w-4 h-4 transition-transform duration-200"
                                                                            :class="showDetail ? 'rotate-180' : ''" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                        </svg>
                                                                        <span
                                                                            x-text="showDetail ? 'Sembunyikan Fasilitas' : 'Lihat Fasilitas (' + kamar.fasilitas.length + ')'"></span>
                                                                    </button>
                                                                    <div x-show="showDetail" x-transition.duration.200ms
                                                                        class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                                        <ul class="space-y-1.5">
                                                                            <template x-for="fas in kamar.fasilitas" :key="fas">
                                                                                <li
                                                                                    class="flex items-center gap-2 text-xs text-gray-700">
                                                                                    <span
                                                                                        class="w-1.5 h-1.5 rounded-full bg-[#36B2B2] shrink-0"></span>
                                                                                    <span x-text="fas"></span>
                                                                                </li>
                                                                            </template>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </template>

                                                            <button
                                                                @click="selectedKamar = kamar; selectedKos = kos; jumlahBayar = kamar.harga; paymentMethod = 'manual'; paymentDeadline = ''; showOrderModal = true"
                                                                class="w-full py-3 bg-gray-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-[#36B2B2] hover:-translate-y-0.5 transition-all shadow-lg hover:shadow-[#36B2B2]/20 group/btn">
                                                                <div class="flex items-center justify-center gap-2">
                                                                    <span>Order Kamar</span>
                                                                    <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform"
                                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                                    </svg>
                                                                </div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Order Confirmation Modal -->
                    <div x-show="showOrderModal"
                        x-effect="showOrderModal ? document.documentElement.classList.add('no-scroll') : document.documentElement.classList.remove('no-scroll')"
                        class="fixed inset-0 z-[9999] flex items-center justify-center p-8 sm:p-12 bg-black/70 backdrop-blur-sm"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">

                        <!-- Modal Backdrop (Click to close) -->
                        <div class="absolute inset-0" @click="showOrderModal = false"></div>

                        <!-- Modal Content -->
                        <template x-if="selectedKamar">
                            <div class="bg-white rounded-[2.5rem] w-full max-w-sm shadow-2xl relative z-10 flex flex-col overflow-hidden"
                                style="max-height: calc(100dvh - 80px);"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-8 scale-95" @click.stop>

                                <!-- Header (Fixed) -->
                                <div
                                    class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 shrink-0">
                                    <div>
                                        <h3 class="text-base font-black text-gray-900 leading-tight">Konfirmasi Order</h3>
                                        <p class="text-[9px] font-medium text-gray-500 mt-1 uppercase tracking-wider">Detail
                                            Pesanan Kamar</p>
                                    </div>
                                    <button @click="showOrderModal = false"
                                        class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-200 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12">
                                            </path>
                                        </svg>
                                    </button>
                                </div>

                                <form action="{{ route('user.order.store') }}" method="POST"
                                    class="flex flex-col min-h-0 overflow-hidden h-full">
                                    @csrf
                                    <input type="hidden" name="id_kamar" :value="selectedKamar.id">
                                    <input type="hidden" name="kode_kos" :value="selectedKos.kode_kos">
                                    <input type="hidden" name="jumlah_bayar" :value="jumlahBayar">
                                    <input type="hidden" name="metode_pembayaran" :value="paymentMethod">
                                    <input type="hidden" name="batas_bayar" :value="paymentDeadline">

                                    <!-- Body (Scrollable Area) -->
                                    <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6 overscroll-contain">
                                        <!-- Room Info -->
                                        <div
                                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-white shrink-0 overflow-hidden shadow-sm border border-gray-100">
                                                <template x-if="selectedKamar.foto">
                                                    <img :src="selectedKamar.foto.startsWith('http') ? selectedKamar.foto : (selectedKamar.foto.startsWith('/') ? selectedKamar.foto : '/images/kamar/' + selectedKamar.foto)"
                                                        class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!selectedKamar.foto">
                                                    <div
                                                        class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-300">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[10px] font-black uppercase tracking-widest text-[#36B2B2] mb-1"
                                                    x-text="'Kos ' + selectedKos.nama_kos"></p>
                                                <h4 class="text-base font-black text-gray-900"
                                                    x-text="'Kamar ' + selectedKamar.nomor_kamar"></h4>
                                                <p class="text-sm font-bold text-gray-600 mt-1"
                                                    x-text="'Rp ' + Number(selectedKamar.harga).toLocaleString('id-ID') + ' / bulan'">
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Room Price (Read-only) -->
                                        <div class="mb-5">
                                            <label class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-2">
                                                Harga Kamar (Rp)
                                            </label>
                                            <input type="number" :value="selectedKamar.harga" disabled
                                                class="w-full px-5 py-3.5 bg-gray-100 border-2 border-gray-100 rounded-xl outline-none font-bold text-gray-500 cursor-not-allowed">
                                        </div>

                                        <!-- "Bayar Berapa" Input -->
                                        <div class="mb-5">
                                            <label class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-2">
                                                Bayar Berapa (Rp) <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="number" x-model="jumlahBayar" required
                                                placeholder="Masukkan nominal yang akan dibayar"
                                                class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-[#36B2B2] focus:bg-white outline-none transition-all font-bold text-gray-800">
                                        </div>

                                        <!-- Payment Method Selection -->
                                        <div class="mb-5">
                                            <label class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-2">
                                                Metode Pembayaran <span class="text-rose-500">*</span>
                                            </label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <button type="button" @click="paymentMethod = 'manual'"
                                                    :class="paymentMethod === 'manual' ? 'border-[#36B2B2] bg-[#36B2B2]/5 text-[#36B2B2]' : 'border-gray-100 bg-gray-50 text-gray-500'"
                                                    class="px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all flex flex-col items-center gap-1">
                                                    <span>Manual</span>
                                                    <span class="text-[9px] font-medium opacity-70">Transfer/Cash</span>
                                                </button>
                                                <button type="button" @click="paymentMethod = 'pymen'"
                                                    :class="paymentMethod === 'pymen' ? 'border-[#36B2B2] bg-[#36B2B2]/5 text-[#36B2B2]' : 'border-gray-100 bg-gray-50 text-gray-500'"
                                                    class="px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all flex flex-col items-center gap-1">
                                                    <span>Py-Men</span>
                                                    <span class="text-[9px] font-medium opacity-70">Payment Portal</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Owner Bank Account (Conditional for Py-Men) -->
                                        <div class="mb-5 p-4 bg-blue-50 rounded-2xl border border-blue-100"
                                            x-show="paymentMethod === 'pymen'" x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-2">
                                            <label
                                                class="block text-[10px] font-black uppercase tracking-widest text-blue-600 mb-1">
                                                Nomor Rekening Pemilik
                                            </label>
                                            <div class="flex items-center justify-between">
                                                <span class="text-base font-black text-gray-900"
                                                    x-text="selectedKos.no_rekening || 'Belum diatur'"></span>
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-blue-100 text-blue-600">BANK</span>
                                            </div>
                                            <p class="text-[9px] font-medium text-gray-500 mt-2">Batas waktu pembayaran otomatis 3
                                                hari
                                                untuk metode Py-Men.</p>
                                        </div>

                                        <!-- Payment Deadline (Conditional for Manual) -->
                                        <div class="mb-5" x-show="paymentMethod === 'manual'"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-2">
                                            <label class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-2">
                                                Batas Waktu Pembayaran (Manual) <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="datetime-local" x-model="paymentDeadline"
                                                :required="paymentMethod === 'manual'"
                                                class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-[#36B2B2] focus:bg-white outline-none transition-all font-bold text-gray-800">
                                            <p class="text-[9px] font-medium text-gray-500 mt-1">Silakan pilih tanggal dan jam
                                                sebelum
                                                anda melakukan pembayaran.</p>
                                        </div>

                                        <!-- Additional Notes -->
                                        <div>
                                            <label class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-2">
                                                Pesan / Catatan Tambahan (Opsional)
                                            </label>
                                            <textarea name="catatan" x-model="orderNote" rows="2"
                                                placeholder="Ada request khusus atau informasi lainnya?"
                                                class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-[#36B2B2] focus:bg-white outline-none transition-all font-medium text-gray-800 resize-none"></textarea>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="p-5 pb-6 bg-gray-50 border-t border-gray-100 flex gap-2 shrink-0">
                                        <button type="button" @click="showOrderModal = false"
                                            class="flex-1 px-4 py-3 bg-white text-gray-600 font-bold text-[10px] rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">Batal</button>
                                        <button type="submit"
                                            :disabled="!jumlahBayar || (paymentMethod === 'manual' && !paymentDeadline)"
                                            :class="jumlahBayar && (paymentMethod !== 'manual' || paymentDeadline) ? 'bg-[#36B2B2] hover:bg-[#2b8f8f] shadow-lg shadow-[#36B2B2]/30 cursor-pointer' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                            class="flex-[1.5] px-4 py-3 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all">
                                            Ajukan Pesanan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>

                <script>
                    function kosSearch() {
                        return {
                            filters: {
                                lokasi: '',
                                harga: '',
                                kategori: '',
                                is_favorit_only: false,
                            },
                            loading: false,
                            searchPerformed: false,
                            error: null,
                            kosList: [],
                            showOrderModal: false,
                            selectedKamar: null,
                            selectedKos: null,
                            orderNote: '',
                            jumlahBayar: 0,
                            paymentMethod: 'manual',
                            paymentDeadline: '',
                            init() {
                                const urlParams = new URLSearchParams(window.location.search);
                                if (urlParams.get('filter') === 'favorit') {
                                    this.filters.is_favorit_only = true;
                                }
                                this.search();
                            },
                            resetFilters() {
                                this.filters = {
                                    lokasi: '',
                                    harga: '',
                                    kategori: '',
                                    is_favorit_only: false,
                                };
                                this.searchPerformed = false;
                                this.kosList = [];
                                this.error = null;
                                this.search(); // Restore recommendations
                            },
                            async search() {
                                this.loading = true;
                                this.error = null;
                                this.kosList = [];

                                const payload = {};
                                if (this.filters.lokasi.trim()) payload.lokasi = this.filters.lokasi.trim();
                                if (this.filters.harga) payload.harga = this.filters.harga;
                                if (this.filters.kategori) payload.kategori = this.filters.kategori;

                                // Add explicit kota filter if it matches one of the popular cities
                                const popularCities = ['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Malang', 'Semarang'];
                                if (popularCities.includes(this.filters.lokasi)) {
                                    payload.kota = this.filters.lokasi;
                                }

                                try {
                                    const res = await fetch('{{ route('user.order.search') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify(payload),
                                    });
                                    const data = await res.json();

                                    // Update searchPerformed state correctly
                                    const loc = this.filters.lokasi ? this.filters.lokasi.trim() : '';
                                    const hasFilters = loc || this.filters.harga || this.filters.kategori || this.filters.is_favorit_only;
                                    this.searchPerformed = !!hasFilters;

                                    if (data.success) {
                                        this.kosList = data.data.map((kos, i) => ({
                                            ...kos,
                                            _expanded: i === 0,
                                            is_favorit: kos.favorited_by && kos.favorited_by.length > 0
                                        }));

                                        // Local filter for favorites if selected
                                        if (this.filters.is_favorit_only) {
                                            this.kosList = this.kosList.filter(k => k.is_favorit);
                                            if (this.kosList.length === 0) {
                                                this.error = "Belum ada kos yang ditandai sebagai favorit.";
                                            }
                                        }
                                    } else {
                                        this.error = data.message || 'Tidak ditemukan.';
                                    }
                                } catch (err) {
                                    console.error(err);
                                    this.error = "Terjadi kesalahan saat mencari kos.";
                                } finally {
                                    this.loading = false;
                                }
                            },
                            async toggleFavorit(kos) {
                                try {
                                    const res = await fetch(`/user/kos/${kos.id}/toggle-favorit`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        }
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        kos.is_favorit = data.is_favorit;
                                    }
                                } catch (err) {
                                    console.error(err);
                                }
                            },
                        }
                    }
                </script>
        @endcan
    @endif

        <!-- Spacer for bottom -->
        <div class="h-10"></div>
@endsection
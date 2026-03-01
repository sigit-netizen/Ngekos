@extends('layouts.dashboard')

@section('dashboard-content')
    @php
        $user = auth()->user();
        $isPenyewa = $user->isPenyewa();
    @endphp

    @if($isPenyewa)
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

    @else
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

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button @click="search()" :disabled="loading"
                        class="flex-1 sm:flex-none px-6 py-3 bg-[#36B2B2] text-white font-bold text-sm rounded-xl hover:bg-[#2b8f8f] transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg shadow-[#36B2B2]/30">
                        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="loading ? 'Mencari...' : '🔍 Cari Kos'"></span>
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

            <!-- Search Results -->
            <template x-if="kosList.length > 0">
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
                                    <div class="flex items-center gap-2 shrink-0">
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

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <template x-for="kamar in kos.kamars" :key="kamar.id">
                                        <div x-data="{ showDetail: false }"
                                            class="border border-gray-100 rounded-xl p-4 hover:border-[#36B2B2]/30 hover:shadow-sm transition-all duration-300 group">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-base font-black text-gray-900">Kamar <span
                                                        x-text="kamar.nomor_kamar"></span></span>
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-600">Tersedia</span>
                                            </div>
                                            <p class="text-lg font-bold text-[#36B2B2] mb-3">
                                                Rp <span x-text="Number(kamar.harga).toLocaleString('id-ID')"></span>
                                                <span class="text-xs text-gray-500 font-normal">/bln</span>
                                            </p>

                                            <!-- Detail Fasilitas Toggle -->
                                            <template x-if="kamar.fasilitas && kamar.fasilitas.length > 0">
                                                <div class="mb-3">
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
                                                                <li class="flex items-center gap-2 text-xs text-gray-700">
                                                                    <span
                                                                        class="w-1.5 h-1.5 rounded-full bg-[#36B2B2] shrink-0"></span>
                                                                    <span x-text="fas"></span>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </template>

                                            <form method="POST" action="{{ route('user.order.store') }}">
                                                @csrf
                                                <input type="hidden" name="id_kamar" :value="kamar.id">
                                                <input type="hidden" name="kode_kos" :value="kos.kode_kos">
                                                <button type="submit"
                                                    class="w-full py-2 bg-[#36B2B2] text-white font-bold text-sm rounded-xl hover:bg-[#2b8f8f] transition-all duration-300 shadow-sm hover:shadow-md active:scale-95">
                                                    Order Kamar
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <script>
                    function kosSearch() {
                        return {
                            filters: {
                                lokasi: '',
                                harga: '',
                                kategori: '',
                            },
                            loading: false,
                            error: null,
                            kosList: [],
                            resetFilters() {
                                this.filters = { lokasi: '', harga: '', kategori: '' };
                                this.kosList = [];
                                this.error = null;
                            },
                            async search() {
                                this.loading = true;
                                this.error = null;
                                this.kosList = [];

                                // Build filter payload (only send non-empty values)
                                const payload = {};
                                if (this.filters.lokasi.trim()) payload.lokasi = this.filters.lokasi.trim();
                                if (this.filters.harga) payload.harga = this.filters.harga;
                                if (this.filters.kategori) payload.kategori = this.filters.kategori;

                                try {
                                    const res = await fetch('{{ route("user.order.search") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify(payload),
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        // Add _expanded property for accordion
                                        this.kosList = data.data.map((kos, i) => ({
                                            ...kos,
                                            _expanded: i === 0, // auto-expand first result
                                        }));
                                    } else {
                                        this.error = data.message || 'Tidak ditemukan.';
                                    }
                                } catch (e) {
                                    this.error = 'Terjadi kesalahan. Silakan coba lagi.';
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }
                    }
        </script>
    @endif

    <!-- Spacer for bottom -->
    <div class="h-10"></div>
@endsection
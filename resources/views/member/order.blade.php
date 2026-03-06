@extends('layouts.dashboard')

@section('dashboard-content')
    @php
        $tab = $tab ?? 'order';
        $statusFilter = $statusFilter ?? 'active';
    @endphp

    <div x-data="{
            activeTab: '{{ $tab }}',
            currentStatus: '{{ $statusFilter }}',
            showProof: false,
            proofUrl: ''
        }"
        x-init="$watch('showProof', val => val ? document.body.classList.add('modal-open') : document.body.classList.remove('modal-open'))">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 md:mb-10 px-1" data-aos="fade-up">
            <div class="flex-1">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-[10px] font-black uppercase tracking-widest mb-4 border border-[#36B2B2]/20">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#36B2B2] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#36B2B2]"></span>
                    </span>
                    Manajemen Verifikasi
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 leading-tight tracking-tight">
                    Order & Verifikasi <span class="text-[#36B2B2]">Penyewa</span>
                </h1>
                <p class="text-gray-500 mt-2 md:mt-4 text-xs sm:text-sm md:text-base font-medium max-w-xl leading-relaxed">
                    Kelola data penyewa yang mendaftar @if(isset($kos)) menggunakan kode kos <span
                    class="text-[#36B2B2] font-black">#{{ $kos->kode_kos }}</span> @endif dalam satu dasbor terpadu.
                </p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-8 sm:mb-10">
            {{-- Verifikasi (Pending Orders) --}}
            <button @click="activeTab = 'order'; currentStatus = 'verif'; window.location.href = '?tab=order&status=verif'"
                class="relative w-full p-4 sm:p-5 rounded-3xl sm:rounded-[2.5rem] border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="(currentStatus === 'verif' || currentStatus === 'pending')
                                                                    ? 'bg-rose-50 border-rose-500 shadow-xl shadow-rose-100 -translate-y-1'
                                                                    : 'bg-white border-gray-50 hover:border-rose-200 shadow-md shadow-gray-200/50'">
                @if(($orderPendingCount ?? 0) + ($pendingCount ?? 0) > 0)
                    <div
                        class="absolute top-3 right-3 text-rose-600 text-[9px] font-black z-10 animate-pulse bg-white px-2 py-0.5 rounded-lg border border-rose-100 shadow-sm">
                        NEW
                    </div>
                @endif
                <div class="mb-3 sm:mb-4">
                    <div class="inline-flex p-2 sm:p-2.5 rounded-2xl transition-all duration-500"
                        :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'bg-white shadow-sm text-rose-500 border border-rose-100' : 'bg-rose-50 text-rose-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-2xl sm:text-3xl font-black transition-colors"
                        :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'text-rose-600' : 'text-gray-900'">
                        {{ ($orderPendingCount ?? 0) + ($pendingCount ?? 0) }}
                    </h3>
                </div>
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-colors mt-0.5 sm:mt-1"
                    :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'text-rose-500' : 'text-gray-400'">
                    Verifikasi</p>
            </button>

            {{-- Menunggu (Verified, waiting for payment) --}}
            <button
                @click="activeTab = 'order'; currentStatus = 'menunggu'; window.location.href = '?tab=order&status=menunggu'"
                class="relative w-full p-4 sm:p-5 rounded-3xl sm:rounded-[2.5rem] border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'menunggu'
                                                                    ? 'bg-amber-50 border-amber-500 shadow-xl shadow-amber-100 -translate-y-1'
                                                                    : 'bg-white border-gray-50 hover:border-amber-200 shadow-md shadow-gray-200/50'">
                <div class="mb-3 sm:mb-4">
                    <div class="inline-flex p-2 sm:p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'menunggu' ? 'bg-white shadow-sm text-amber-500 border border-amber-100' : 'bg-amber-50 text-amber-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black transition-colors"
                    :class="currentStatus === 'menunggu' ? 'text-amber-600' : 'text-gray-900'">
                    {{ $orderMenungguCount ?? 0 }}
                </h3>
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-colors mt-0.5 sm:mt-1"
                    :class="currentStatus === 'menunggu' ? 'text-amber-500' : 'text-gray-400'">
                    Menunggu</p>
            </button>

            {{-- Konfirmasi (Verified, proof uploaded) --}}
            <button
                @click="activeTab = 'order'; currentStatus = 'konfirmasi'; window.location.href = '?tab=order&status=konfirmasi'"
                class="relative w-full p-4 sm:p-5 rounded-3xl sm:rounded-[2.5rem] border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'konfirmasi'
                                                                    ? 'bg-emerald-50 border-emerald-500 shadow-xl shadow-emerald-100 -translate-y-1'
                                                                    : 'bg-white border-gray-50 hover:border-emerald-200 shadow-md shadow-gray-200/50'">
                @if(($orderKonfirmasiCount ?? 0) > 0)
                    <div class="absolute top-3 right-3 flex h-5 w-5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-5 w-5 bg-rose-500 border-2 border-white flex items-center justify-center text-[10px] font-black text-white leading-none">
                            {{ $orderKonfirmasiCount }}
                        </span>
                    </div>
                @endif
                <div class="mb-3 sm:mb-4">
                    <div class="inline-flex p-2 sm:p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'konfirmasi' ? 'bg-white shadow-sm text-emerald-500 border border-emerald-100' : 'bg-emerald-50 text-emerald-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black transition-colors"
                    :class="currentStatus === 'konfirmasi' ? 'text-emerald-600' : 'text-gray-900'">
                    {{ $orderKonfirmasiCount ?? 0 }}
                </h3>
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-colors mt-0.5 sm:mt-1"
                    :class="currentStatus === 'konfirmasi' ? 'text-emerald-500' : 'text-gray-400'">
                    Konfirmasi</p>
            </button>

            {{-- Verifikasi Sewa (Recurring Rent) --}}
            <button @click="activeTab = 'order'; currentStatus = 'sewa'; window.location.href = '?tab=order&status=sewa'"
                class="relative w-full p-4 sm:p-5 rounded-3xl sm:rounded-[2.5rem] border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'sewa'
                                                                    ? 'bg-amber-50 border-amber-500 shadow-xl shadow-amber-100 -translate-y-1'
                                                                    : 'bg-white border-gray-50 hover:border-amber-200 shadow-md shadow-gray-200/50'">
                @if(($rentKonfirmasiCount ?? 0) > 0)
                    <div
                        class="absolute top-3 right-3 flex h-5 w-5 bg-amber-500 text-white text-[10px] font-black rounded-lg items-center justify-center shadow-md shadow-amber-200 animate-bounce">
                        {{ $rentKonfirmasiCount }}
                    </div>
                @endif
                <div class="mb-3 sm:mb-4">
                    <div class="inline-flex p-2 sm:p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'sewa' ? 'bg-white shadow-sm text-amber-500 border border-amber-100' : 'bg-amber-50 text-amber-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black transition-colors"
                    :class="currentStatus === 'sewa' ? 'text-amber-600' : 'text-gray-900'">
                    {{ $rentKonfirmasiCount ?? 0 }}
                </h3>
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-colors mt-0.5 sm:mt-1"
                    :class="currentStatus === 'sewa' ? 'text-amber-500' : 'text-gray-400'">
                    Verif Sewa</p>
            </button>

            {{-- Aktif (Penyewa Aktif) --}}
            <button
                @click="activeTab = 'riwayat'; currentStatus = 'active'; window.location.href = '?tab=riwayat&status=active'"
                class="relative w-full p-4 sm:p-5 rounded-3xl sm:rounded-[2.5rem] border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'active'
                                                                    ? 'bg-[#36B2B2]/5 border-[#36B2B2] shadow-xl shadow-[#36B2B2]/10 -translate-y-1'
                                                                    : 'bg-white border-gray-50 hover:border-[#36B2B2]/20 shadow-md shadow-gray-200/50'">
                <div class="mb-3 sm:mb-4">
                    <div class="inline-flex p-2 sm:p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'active' ? 'bg-white shadow-sm text-[#36B2B2] border border-teal-100' : 'bg-[#36B2B2]/10 text-[#36B2B2]'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black transition-colors"
                    :class="currentStatus === 'active' ? 'text-[#36B2B2]' : 'text-gray-900'">
                    {{ $activeCount ?? 0 }}
                </h3>
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-colors mt-0.5 sm:mt-1"
                    :class="currentStatus === 'active' ? 'text-[#36B2B2]' : 'text-gray-400'">
                    Aktif</p>
            </button>

            {{-- Ditolak --}}
            <button
                @click="activeTab = 'riwayat'; currentStatus = 'rejected'; window.location.href = '?tab=riwayat&status=rejected'"
                class="relative w-full p-4 sm:p-5 rounded-3xl sm:rounded-[2.5rem] border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'rejected'
                                                                    ? 'bg-slate-50 border-slate-500 shadow-xl shadow-slate-100 -translate-y-1'
                                                                    : 'bg-white border-gray-50 hover:border-slate-200 shadow-md shadow-gray-200/50'">
                <div class="mb-3 sm:mb-4">
                    <div class="inline-flex p-2 sm:p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'rejected' ? 'bg-white shadow-sm text-slate-500 border border-slate-200' : 'bg-slate-50 text-slate-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black transition-colors"
                    :class="currentStatus === 'rejected' ? 'text-slate-600' : 'text-gray-900'">
                    {{ $rejectedCount ?? 0 }}
                </h3>
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-colors mt-0.5 sm:mt-1"
                    :class="currentStatus === 'rejected' ? 'text-slate-500' : 'text-gray-400'">
                    Ditolak</p>
            </button>
        </div>

        {{-- Verification Alert Card (Action Needed) --}}
        @if(($orderKonfirmasiCount ?? 0) > 0 || ($pendingCount ?? 0) > 0 || ($rentKonfirmasiCount ?? 0) > 0)
            <div class="mb-10 px-1" data-aos="fade-up" data-aos-delay="50">
                <div
                    class="bg-white rounded-[2.5rem] md:rounded-[3rem] p-6 sm:p-8 md:p-10 border-2 border-rose-100 shadow-2xl shadow-rose-100/50 flex flex-col lg:flex-row items-center justify-between gap-8 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-rose-50 rounded-full -mr-32 -mt-32 group-hover:scale-110 transition-transform duration-1000 opacity-50">
                    </div>
                    <div class="relative flex flex-col sm:flex-row items-center gap-6 sm:gap-8 text-center sm:text-left">
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-3xl sm:rounded-[2rem] flex items-center justify-center shadow-2xl shadow-rose-200 shrink-0 transform group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 animate-pulse" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">Perlu Tindakan Anda 💸</h3>
                            <p class="text-gray-500 text-xs sm:text-sm md:text-base font-medium mt-2 max-w-md leading-relaxed">
                                Ada pembayaran atau registrasi baru yang menunggu validasi Anda hari ini.
                            </p>
                        </div>
                    </div>
                    <div
                        class="relative flex flex-col sm:flex-row flex-wrap items-center justify-center gap-3 w-full lg:w-auto">
                        @if(($orderKonfirmasiCount ?? 0) > 0)
                            <button
                                @click="activeTab = 'order'; currentStatus = 'konfirmasi'; window.location.href = '?tab=order&status=konfirmasi'"
                                class="w-full sm:w-auto px-6 py-3.5 bg-rose-500 text-white font-black rounded-2xl hover:bg-rose-600 transition-all shadow-xl shadow-rose-200 text-xs uppercase tracking-widest whitespace-nowrap active:scale-95">
                                Bayar ({{ $orderKonfirmasiCount }})
                            </button>
                        @endif
                        @if(($pendingCount ?? 0) > 0)
                            <button
                                @click="activeTab = 'order'; currentStatus = 'verif'; window.location.href = '?tab=order&status=verif'"
                                class="w-full sm:w-auto px-6 py-3.5 bg-rose-50 text-rose-600 font-black rounded-2xl hover:bg-rose-100 transition-all text-xs uppercase tracking-widest whitespace-nowrap active:scale-95 border border-rose-100">
                                Regis ({{ $pendingCount }})
                            </button>
                        @endif
                        @if(($rentKonfirmasiCount ?? 0) > 0)
                            <button
                                @click="activeTab = 'order'; currentStatus = 'sewa'; window.location.href = '?tab=order&status=sewa'"
                                class="w-full sm:w-auto px-6 py-3.5 bg-amber-500 text-white font-black rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-200 text-xs uppercase tracking-widest whitespace-nowrap active:scale-95">
                                Sewa ({{ $rentKonfirmasiCount }})
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Content Area --}}
        <div class="bg-white rounded-[2.5rem] border-2 border-gray-50 shadow-2xl shadow-gray-200/50 overflow-hidden min-h-[600px]"
            data-aos="fade-up" data-aos-delay="100">

            {{-- Tab Header --}}
            <div
                class="px-5 sm:px-10 py-6 md:py-8 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/20">
                <div class="flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-[#36B2B2] rounded-full"></span>
                    <h3 class="text-base sm:text-lg md:text-xl font-black text-gray-900 whitespace-nowrap" x-text="
                                                                            activeTab === 'order' ? 'Antrean Verifikasi' :
                                                                            (currentStatus === 'active' ? 'Basis Data Penyewa' : 'Arsip Penolakan')
                                                                        "></h3>
                </div>

                <template x-if="activeTab === 'riwayat'">
                    <div
                        class="flex bg-gray-100 p-1 rounded-2xl items-center border border-gray-200 shadow-inner w-full md:w-auto overflow-x-auto no-scrollbar">
                        <button @click="currentStatus = 'active'; window.location.href = '?tab=riwayat&status=active'"
                            class="flex-1 md:flex-none px-6 sm:px-8 py-2.5 sm:py-3 rounded-[0.85rem] text-[9px] sm:text-[10px] font-black transition-all duration-300 uppercase tracking-widest whitespace-nowrap"
                            :class="currentStatus === 'active' ? 'bg-white text-[#36B2B2] shadow-sm' : 'text-gray-400 hover:text-gray-600'">AKTIF</button>
                        <button @click="currentStatus = 'rejected'; window.location.href = '?tab=riwayat&status=rejected'"
                            class="flex-1 md:flex-none px-6 sm:px-8 py-2.5 sm:py-3 rounded-[0.85rem] text-[9px] sm:text-[10px] font-black transition-all duration-300 uppercase tracking-widest whitespace-nowrap"
                            :class="currentStatus === 'rejected' ? 'bg-white text-rose-500 shadow-sm' : 'text-gray-400 hover:text-rose-600'">DITOLAK</button>
                    </div>
                </template>
                <template x-if="activeTab === 'order'">
                    <div
                        class="flex bg-gray-100 p-1 rounded-2xl items-center border border-gray-200 shadow-inner w-full md:w-auto overflow-x-auto no-scrollbar scrollbar-hide">
                        <button @click="currentStatus = 'verif'; window.location.href = '?tab=order&status=verif'"
                            class="flex-1 md:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-[0.85rem] text-[9px] sm:text-[10px] font-black transition-all duration-300 uppercase tracking-widest whitespace-nowrap"
                            :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'bg-white text-rose-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'">REGIS</button>
                        <button @click="currentStatus = 'menunggu'; window.location.href = '?tab=order&status=menunggu'"
                            class="flex-1 md:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-[0.85rem] text-[9px] sm:text-[10px] font-black transition-all duration-300 uppercase tracking-widest whitespace-nowrap"
                            :class="currentStatus === 'menunggu' ? 'bg-white text-amber-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'">MENUNGGU</button>
                        <button @click="currentStatus = 'konfirmasi'; window.location.href = '?tab=order&status=konfirmasi'"
                            class="flex-1 md:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-[0.85rem] text-[9px] sm:text-[10px] font-black transition-all duration-300 uppercase tracking-widest whitespace-nowrap"
                            :class="currentStatus === 'konfirmasi' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'">BAYAR</button>
                        <button @click="currentStatus = 'sewa'; window.location.href = '?tab=order&status=sewa'"
                            class="flex-1 md:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-[0.85rem] text-[9px] sm:text-[10px] font-black transition-all duration-300 uppercase tracking-widest whitespace-nowrap"
                            :class="currentStatus === 'sewa' ? 'bg-white text-[#36B2B2] shadow-sm' : 'text-gray-400 hover:text-gray-600'">SEWA</button>
                        <button @click="currentStatus = 'gagal'; window.location.href = '?tab=order&status=gagal'"
                            class="flex-1 md:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-[0.85rem] text-[9px] sm:text-[10px] font-black transition-all duration-300 uppercase tracking-widest whitespace-nowrap"
                            :class="currentStatus === 'gagal' ? 'bg-white text-rose-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'">GAGAL</button>
                    </div>
                </template>
            </div>

            <div class="p-0" x-data="{ showProof: false, proofUrl: '' }">
                {{-- Proof Modal --}}
                <div x-show="showProof"
                    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm"
                    @click="showProof = false" x-cloak>
                    <div class="relative max-w-2xl w-full bg-white rounded-3xl p-2 shadow-2xl overflow-hidden" @click.stop>
                        <button @click="showProof = false"
                            class="absolute top-4 right-4 p-2.5 bg-gray-900/40 hover:bg-gray-900/60 text-white rounded-full transition-all z-[70] backdrop-blur-sm shadow-xl border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <img :src="proofUrl" class="w-full h-auto rounded-2xl max-h-[80vh] object-contain">
                    </div>
                </div>

                {{-- 1. Order Kamar --}}
                <div x-show="activeTab === 'order'">

                    {{-- Desktop Table View --}}
                    <div x-show="currentStatus === 'regis'" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-4" style="display: none;">
                        <div
                            class="hidden md:block overflow-x-auto mb-8 bg-white border border-t-0 border-gray-100 rounded-b-3xl">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead>
                                    <tr
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                        <th class="px-8 py-5">Calon Penyewa</th>
                                        <th class="px-8 py-5">WhatsApp</th>
                                        <th class="px-8 py-5">Alamat</th>
                                        <th class="px-8 py-5">Tanggal Daftar</th>
                                        <th class="px-8 py-5 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($pendingPenyewa as $pending)
                                        <tr
                                            class="group hover:bg-amber-50/40 transition-all border-b border-gray-50 last:border-0">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/50 text-amber-600 flex items-center justify-center font-black text-sm border border-amber-200/50 shadow-sm">
                                                        {{ substr($pending->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <div class="font-black text-gray-900 leading-none">
                                                                {{ $pending->name }}
                                                            </div>
                                                            <div x-data="{ 
                                                                                                                                                    expiryTime: new Date('{{ optional($pending->created_at)->addDay()?->toIso8601String() ?? now()->toIso8601String() }}').getTime(),
                                                                                                                                                    now: new Date().getTime(),
                                                                                                                                                    timer: '',
                                                                                                                                                    init() {
                                                                                                                                                        this.updateTimer();
                                                                                                                                                        setInterval(() => { this.now = new Date().getTime(); this.updateTimer(); }, 1000);
                                                                                                                                                    },
                                                                                                                                                    updateTimer() {
                                                                                                                                                        let diff = this.expiryTime - this.now;
                                                                                                                                                        if (diff <= 0) { this.timer = 'EXPIRED'; return; }
                                                                                                                                                        let h = Math.floor(diff / (1000 * 60 * 60));
                                                                                                                                                        let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                                                                                                                        let s = Math.floor((diff % (1000 * 60)) / 1000);
                                                                                                                                                        this.timer = `${h}j ${m}m ${s}d`;
                                                                                                                                                    }
                                                                                                                                                }"
                                                                class="px-2 py-0.5 bg-rose-500 text-white text-[9px] font-black rounded-lg shadow-sm animate-pulse">
                                                                <span x-text="timer"></span>
                                                            </div>
                                                        </div>
                                                        <div class="text-[10px] text-gray-400 font-bold tracking-tight">
                                                            {{ $pending->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="font-black text-gray-700 text-sm tracking-tight">{{ $pending->nomor_wa }}</span>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="text-xs font-bold text-gray-500 max-w-[200px] line-clamp-2 leading-relaxed">{{ $pending->alamat ?? '-' }}</span>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ optional($pending->created_at)->format('d M Y') ?? '-' }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <form method="POST"
                                                        action="{{ route('admin.penyewa.verify', $pending->id) }}">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Terima Pendaftaran?', 'Calon penyewa ini akan diverifikasi.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="px-5 py-2.5 rounded-xl text-[10px] font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-100 active:scale-95 uppercase tracking-widest">
                                                            ✓ Terima
                                                        </button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('admin.penyewa.reject', $pending->id) }}">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Tolak Pendaftaran?', 'Hapus data ini?', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="px-5 py-2.5 rounded-xl text-[10px] font-black bg-white border-2 border-rose-50 text-rose-500 hover:bg-rose-50 transition-all uppercase tracking-widest active:scale-95">
                                                            ✗ Tolak
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="grid grid-cols-1 gap-4 md:hidden px-4 mb-10 mt-2">
                            @foreach($pendingPenyewa as $pending)
                                <div
                                    class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm relative group overflow-hidden flex flex-col gap-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center font-bold text-lg shrink-0">
                                            {{ substr($pending->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-gray-900 text-base leading-tight truncate">
                                                {{ $pending->name }}
                                            </h4>
                                            <p class="text-[11px] text-gray-500 tracking-tight mt-0.5 truncate">
                                                {{ $pending->nomor_wa }} &bull;
                                                {{ optional($pending->created_at)->format('d/m/y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-100/50 flex flex-col gap-2">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span
                                                class="text-xs text-gray-600 leading-relaxed line-clamp-2">{{ $pending->alamat ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between mt-1 pt-2 border-t border-gray-200/50">
                                            <span class="text-[10px] font-semibold text-gray-500 uppercase">Sisa Waktu</span>
                                            <div x-data="{ 
                                                                                                        expiryTime: new Date('{{ optional($pending->created_at)->addDay()?->toIso8601String() ?? now()->toIso8601String() }}').getTime(),
                                                                                                        now: new Date().getTime(),
                                                                                                        timer: '',
                                                                                                        init() {
                                                                                                            this.updateTimer();
                                                                                                            setInterval(() => { this.now = new Date().getTime(); this.updateTimer(); }, 1000);
                                                                                                        },
                                                                                                        updateTimer() {
                                                                                                            let diff = this.expiryTime - this.now;
                                                                                                            if (diff <= 0) { this.timer = 'EXPIRED'; return; }
                                                                                                            let h = Math.floor(diff / (1000 * 60 * 60));
                                                                                                            let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                                                                            let s = Math.floor((diff % (1000 * 60)) / 1000);
                                                                                                            this.timer = `${h}j ${m}m ${s}d`;
                                                                                                        }
                                                                                                    }"
                                                class="px-2 py-0.5 bg-rose-50 text-rose-600 text-[10px] font-bold rounded-md animate-pulse">
                                                <span x-text="timer"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('admin.penyewa.verify', $pending->id) }}"
                                            class="flex-1">
                                            @csrf
                                            <button type="button"
                                                @click="window.swalConfirm('Terima Pendaftaran?', 'Calon penyewa ini akan diverifikasi.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                class="w-full py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition-colors uppercase tracking-wide">
                                                Terima
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.penyewa.reject', $pending->id) }}"
                                            class="flex-1">
                                            @csrf
                                            <button type="button"
                                                @click="window.swalConfirm('Tolak Pendaftaran?', 'Hapus pendaftaran ini?', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                class="w-full py-2.5 rounded-xl text-xs font-bold bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 transition-colors uppercase tracking-wide">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="currentStatus !== 'regis'" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-4" style="display: none;">
                        <div class="px-8 py-5 bg-blue-50/50 border-y border-blue-100/50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-gray-900 leading-none" x-text="
                                                                                        currentStatus === 'verif' ? 'Verifikasi Order Baru' :
                                                                                        (currentStatus === 'menunggu' ? 'Menunggu Pembayaran' : 
                                                                                        (currentStatus === 'konfirmasi' ? 'Konfirmasi Bukti Transfer' : 'Riwayat Pembatalan'))
                                                                                    "></h4>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1" x-text="
                                                                                        currentStatus === 'verif' ? 'Pesanan baru yang baru masuk' :
                                                                                        (currentStatus === 'menunggu' ? 'Pesanan diterima & menunggu bukti bayar' : 
                                                                                        (currentStatus === 'konfirmasi' ? 'Pembayaran yang perlu Anda konfirmasi' : 'Riwayat pesanan yang dibatalkan atau ditolak'))
                                                                                    "></p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border"
                                :class="{
                                                                                    'bg-rose-100 text-rose-700 border-rose-200': currentStatus === 'verif',
                                                                                    'bg-amber-100 text-amber-700 border-amber-200': currentStatus === 'menunggu',
                                                                                    'bg-emerald-100 text-emerald-700 border-emerald-200': currentStatus === 'konfirmasi'
                                                                                }">
                                {{ count($orderTransaksi ?? []) }} Data
                            </span>
                        </div>
                        {{-- Desktop Table View --}}
                        <div
                            class="hidden md:block overflow-x-auto bg-white border border-t-0 border-gray-100 rounded-b-3xl">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead>
                                    <tr
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                        <th class="px-8 py-5">User</th>
                                        <th class="px-8 py-5">WhatsApp</th>
                                        <th class="px-8 py-5">No. Kamar</th>
                                        <th class="px-8 py-5">Harga</th>
                                        @if($statusFilter === 'gagal')
                                            <th class="px-8 py-5">Keterangan Gagal</th>
                                        @else
                                            <th class="px-8 py-5">Catatan</th>
                                        @endif
                                        <th class="px-8 py-5">Tanggal</th>
                                        <th class="px-8 py-5 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($orderTransaksi ?? [] as $order)
                                        <tr class="group hover:bg-slate-50 transition-colors">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black text-sm">
                                                        {{ substr(optional($order->user)->name ?? '?', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <div class="font-bold text-gray-900">
                                                                {{ $order->user->name ?? 'N/A' }}
                                                            </div>
                                                            @if($order->status === 'pending')
                                                                <div x-data="{ 
                                                                                                                                                                                            expiryTime: new Date('{{ optional($order->created_at)->addDay()?->toIso8601String() ?? now()->toIso8601String() }}').getTime(),
                                                                                                                                                                                            now: new Date().getTime(),
                                                                                                                                                                                            timer: '',
                                                                                                                                                                                            init() {
                                                                                                                                                                                                this.updateTimer();
                                                                                                                                                                                                setInterval(() => { this.now = new Date().getTime(); this.updateTimer(); }, 1000);
                                                                                                                                                                                            },
                                                                                                                                                                                            updateTimer() {
                                                                                                                                                                                                let diff = this.expiryTime - this.now;
                                                                                                                                                                                                if (diff <= 0) { this.timer = 'EXPIRED'; return; }
                                                                                                                                                                                                let h = Math.floor(diff / (1000 * 60 * 60));
                                                                                                                                                                                                let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                                                                                                                                                                let s = Math.floor((diff % (1000 * 60)) / 1000);
                                                                                                                                                                                                this.timer = `${h}j ${m}m ${s}d`;
                                                                                                                                                                                            }
                                                                                                                                                                                        }"
                                                                    class="px-2 py-0.5 bg-red-600 text-red-50 text-[9px] font-black rounded-md animate-pulse">
                                                                    <span x-text="timer"></span>
                                                                </div>
                                                            @elseif($order->status === 'verified' && $order->bukti_pembayaran)
                                                                {{-- Countdown Timer for Owner to Confirm Payment --}}
                                                                <div x-data="{ 
                                                                                                                                                                                            expiryTime: new Date('{{ optional($order->tanggal_pembayaran)->addDay()?->toIso8601String() ?? now()->toIso8601String() }}').getTime(),
                                                                                                                                                                                            now: new Date().getTime(),
                                                                                                                                                                                            timer: '',
                                                                                                                                                                                            init() {
                                                                                                                                                                                                this.updateTimer();
                                                                                                                                                                                                setInterval(() => {
                                                                                                                                                                                                    this.now = new Date().getTime();
                                                                                                                                                                                                    this.updateTimer();
                                                                                                                                                                                                }, 1000);
                                                                                                                                                                                            },
                                                                                                                                                                                            updateTimer() {
                                                                                                                                                                                                let diff = this.expiryTime - this.now;
                                                                                                                                                                                                if (diff <= 0) {
                                                                                                                                                                                                    this.timer = 'EXPIRED';
                                                                                                                                                                                                    return;
                                                                                                                                                                                                }
                                                                                                                                                                                                let h = Math.floor(diff / (1000 * 60 * 60));
                                                                                                                                                                                                let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                                                                                                                                                                let s = Math.floor((diff % (1000 * 60)) / 1000);
                                                                                                                                                                                                this.timer = `${h}j ${m}m ${s}d`;
                                                                                                                                                                                            }
                                                                                                                                                                                        }"
                                                                    class="px-2 py-0.5 bg-red-600 text-red-50 text-[9px] font-black rounded-md animate-pulse">
                                                                    <span x-text="timer"></span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="text-[10px] text-gray-400 font-medium">
                                                            {{ $order->user->email ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="font-bold text-gray-700 text-sm">{{ $order->kamar->nomor_kamar ?? '-' }}</span>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span class="font-bold text-[#36B2B2] text-sm">Rp
                                                    {{ number_format($order->jumlah_bayar, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-8 py-6">
                                                @if($statusFilter === 'gagal')
                                                    <div class="flex flex-col gap-1">
                                                        <span
                                                            class="text-[10px] font-bold px-2 py-1 rounded-md w-fit border {{ $order->status === 'rejected' ? 'text-rose-500 bg-rose-50 border-rose-100' : 'text-slate-500 bg-slate-100 border-slate-200' }}">
                                                            {{ $order->keterangan_gagal }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span
                                                        class="text-xs text-gray-500 line-clamp-2 max-w-[150px]">{{ $order->catatan ?? '-' }}</span>
                                                @endif
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="text-xs font-bold text-gray-500">{{ optional($order->created_at)->format('d M Y') ?? '-' }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    @if($order->status === 'pending')
                                                        @if($order->tipe === 'sewa')
                                                            <div class="flex items-center gap-2">
                                                                <form method="POST"
                                                                    action="{{ route('admin.order.confirm', $order->id) }}">
                                                                    @csrf
                                                                    <button type="button"
                                                                        @click="window.swalConfirm('Konfirmasi Pembayaran Sewa?', 'Pastikan uang sudah masuk ke rekening Anda.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                        class="px-6 py-2 rounded-xl text-[11px] font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md active:scale-95 uppercase tracking-wider">
                                                                        ✓ Konfirmasi
                                                                    </button>
                                                                </form>
                                                                <form method="POST"
                                                                    action="{{ route('admin.order.reject', $order->id) }}">
                                                                    @csrf
                                                                    <button type="button"
                                                                        @click="window.swalConfirm('Tolak Pembayaran?', 'Pembayaran sewa ini akan ditolak.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                        class="px-4 py-2 rounded-xl text-[10px] font-bold text-red-500 hover:bg-red-50 transition-all uppercase tracking-widest border border-red-100">
                                                                        Tolak
                                                                    </button>
                                                                </form>
                                                            </div>
                                                            @if($order->bukti_pembayaran)
                                                                <button
                                                                    @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                                                    class="text-[10px] font-bold text-[#36B2B2] hover:underline uppercase tracking-widest mt-1">
                                                                    Lihat Bukti
                                                                </button>
                                                            @endif
                                                        @else
                                                            <form method="POST" action="{{ route('admin.order.verify', $order->id) }}">
                                                                @csrf
                                                                <button type="button"
                                                                    @click="window.swalConfirm('Terima Order?', 'Pesanan kamar ini akan disetujui dan penyewa bisa lanjut bayar.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                    class="px-6 py-2 rounded-xl text-[11px] font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md active:scale-95 uppercase tracking-wider">
                                                                    ✓ Terima
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('admin.order.reject', $order->id) }}">
                                                                @csrf
                                                                <button type="button"
                                                                    @click="window.swalConfirm('Tolak Order?', 'Pesanan ini akan dibatalkan.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                    class="text-[10px] font-bold text-red-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                                                                    Tolak
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @elseif($order->status === 'verified')
                                                        @if($order->bukti_pembayaran)
                                                            @if($statusFilter === 'konfirmasi')
                                                                <div class="flex items-center gap-2">
                                                                    <form method="POST"
                                                                        action="{{ route('admin.order.confirm', $order->id) }}">
                                                                        @csrf
                                                                        <button type="button"
                                                                            @click="window.swalConfirm('Konfirmasi Pembayaran?', 'Pastikan uang sudah masuk ke rekening Anda.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                            class="px-6 py-2 rounded-xl text-[11px] font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md active:scale-95 uppercase tracking-wider">
                                                                            ✓ Konfirmasi
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST"
                                                                        action="{{ route('admin.order.reject', $order->id) }}">
                                                                        @csrf
                                                                        <button type="button"
                                                                            @click="window.swalConfirm('Tolak Pembayaran?', 'Pesanan ini akan dibatalkan dan kamar akan tersedia kembali.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                            class="px-4 py-2 rounded-xl text-[10px] font-bold text-red-500 hover:bg-red-50 transition-all uppercase tracking-widest border border-red-100">
                                                                            Tolak
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                                <button
                                                                    @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                                                    class="text-[10px] font-bold text-[#36B2B2] hover:underline uppercase tracking-widest mt-1">
                                                                    Lihat Bukti
                                                                </button>
                                                            @else
                                                                <span
                                                                    class="px-4 py-1.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-tight">Sudah
                                                                    Bayar</span>
                                                                <button
                                                                    @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                                                    class="text-[9px] font-bold text-[#36B2B2] hover:underline uppercase tracking-widest mt-1">
                                                                    Lihat Bukti
                                                                </button>
                                                            @endif
                                                        @else
                                                            <span
                                                                class="px-4 py-1.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tight">Menunggu
                                                                Bukti</span>
                                                            @if($order->batas_bayar)
                                                                <span class="text-[8px] font-medium text-gray-400 italic">Hingga:
                                                                    {{ \Carbon\Carbon::parse($order->batas_bayar)->format('H:i, d M') }}</span>
                                                            @endif
                                                        @endif
                                                    @elseif($order->status === 'paid')
                                                        <span
                                                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">BERHASIL</span>
                                                    @elseif($order->status === 'failed')
                                                        <span
                                                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">GAGAL</span>
                                                    @else
                                                        <span
                                                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600">DITOLAK</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-8 py-20 text-center">
                                                <div
                                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-gray-400 text-sm font-medium">Belum ada order kamar dari user.
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="grid grid-cols-1 gap-4 md:hidden px-4 mb-4">
                            @forelse($orderTransaksi ?? [] as $order)
                                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col gap-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm shrink-0">
                                                {{ substr(optional($order->user)->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-gray-900 text-base truncate">
                                                    {{ $order->user->name ?? 'N/A' }}
                                                </h4>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-[11px] font-medium text-gray-500">Unit
                                                        {{ $order->kamar->nomor_kamar ?? '-' }}</span>
                                                    @if($order->status === 'pending')
                                                        <div x-data="{ 
                                                                                                                                                expiryTime: new Date('{{ optional($order->created_at)->addDay()?->toIso8601String() ?? now()->toIso8601String() }}').getTime(),
                                                                                                                                                now: new Date().getTime(),
                                                                                                                                                timer: '',
                                                                                                                                                init() {
                                                                                                                                                    this.updateTimer();
                                                                                                                                                    setInterval(() => { this.now = new Date().getTime(); this.updateTimer(); }, 1000);
                                                                                                                                                },
                                                                                                                                                updateTimer() {
                                                                                                                                                    let diff = this.expiryTime - this.now;
                                                                                                                                                    if (diff <= 0) { this.timer = 'EXPIRED'; return; }
                                                                                                                                                    let h = Math.floor(diff / (1000 * 60 * 60));
                                                                                                                                                    let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                                                                                                                    let s = Math.floor((diff % (1000 * 60)) / 1000);
                                                                                                                                                    this.timer = `${h}j ${m}m ${s}d`;
                                                                                                                                                }
                                                                                                                                            }"
                                                            class="px-1.5 py-0.5 bg-red-50 text-red-600 text-[9px] font-bold rounded animate-pulse">
                                                            <span x-text="timer"></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-[9px] text-gray-400 font-semibold block uppercase">Total</span>
                                            <span
                                                class="text-sm font-bold text-[#36B2B2]">Rp{{ number_format($order->jumlah_bayar, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-3 flex flex-col gap-1.5 text-xs">
                                        <div class="flex justify-between items-center text-gray-600">
                                            <span class="text-gray-500 font-medium whitespace-nowrap">Tgl Order:</span>
                                            <span
                                                class="font-medium text-gray-700 ml-2 text-right">{{ optional($order->created_at)->format('d M Y') }}</span>
                                        </div>
                                        @if($statusFilter === 'gagal')
                                            <div
                                                class="flex justify-between items-start text-gray-600 mt-1 pt-2 border-t border-gray-200/60">
                                                <span class="text-gray-500 font-medium whitespace-nowrap mt-0.5">Alasan:</span>
                                                <div class="flex flex-col items-end gap-1 ml-2">
                                                    <span
                                                        class="text-[9px] font-bold px-2 py-1 rounded-md text-right border {{ $order->status === 'rejected' ? 'text-rose-500 bg-rose-50 border-rose-100' : 'text-slate-500 bg-slate-100 border-slate-200' }}">
                                                        {{ $order->keterangan_gagal_singkat }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex justify-between items-center text-gray-600">
                                                <span class="text-gray-500 font-medium whitespace-nowrap">Catatan:</span>
                                                <span
                                                    class="font-medium text-gray-700 ml-2 truncate max-w-[150px] text-right">{{ $order->catatan ?? '-' }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        @if($order->status === 'pending')
                                            @if($order->tipe === 'sewa')
                                                <div class="flex gap-2">
                                                    <form method="POST" action="{{ route('admin.order.confirm', $order->id) }}"
                                                        class="flex-1">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Konfirmasi Pembayaran Sewa?', 'Pastikan uang sudah masuk ke rekening Anda.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="w-full py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition-colors uppercase tracking-wide">
                                                            Konfirmasi
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.order.reject', $order->id) }}"
                                                        class="flex-1">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Tolak Pembayaran?', 'Pembayaran sewa ini akan ditolak.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="w-full py-2.5 rounded-xl text-xs font-bold bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 transition-colors uppercase tracking-wide">
                                                            Tolak
                                                        </button>
                                                    </form>
                                                </div>
                                                @if($order->bukti_pembayaran)
                                                    <button @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                                        class="w-full py-2 text-xs font-bold text-[#36B2B2] hover:bg-[#36B2B2]/5 rounded-xl transition-colors mt-1 uppercase">
                                                        Lihat Bukti
                                                    </button>
                                                @endif
                                            @else
                                                <div class="flex gap-2">
                                                    <form method="POST" action="{{ route('admin.order.verify', $order->id) }}"
                                                        class="flex-1">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Terima Order?', 'Pesanan kamar ini akan disetujui dan penyewa bisa lanjut bayar.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="w-full py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition-colors uppercase tracking-wide">
                                                            Terima
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.order.reject', $order->id) }}"
                                                        class="flex-1">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Tolak Order?', 'Pesanan ini akan dibatalkan.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="w-full py-2.5 rounded-xl text-xs font-bold bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 transition-colors uppercase tracking-wide">
                                                            Tolak
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @elseif($order->status === 'verified')
                                            @if($order->bukti_pembayaran)
                                                @if($statusFilter === 'konfirmasi')
                                                    <div class="flex gap-2">
                                                        <form method="POST" action="{{ route('admin.order.confirm', $order->id) }}"
                                                            class="flex-1">
                                                            @csrf
                                                            <button type="button"
                                                                @click="window.swalConfirm('Konfirmasi Pembayaran?', 'Pastikan uang sudah masuk ke rekening Anda.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                class="w-full py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition-colors uppercase tracking-wide">
                                                                Konfirmasi
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.order.reject', $order->id) }}"
                                                            class="flex-1">
                                                            @csrf
                                                            <button type="button"
                                                                @click="window.swalConfirm('Tolak Pembayaran?', 'Pesanan ini akan dibatalkan dan kamar akan tersedia kembali.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                class="w-full py-2.5 rounded-xl text-xs font-bold bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 transition-colors uppercase tracking-wide">
                                                                Tolak
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <button @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                                        class="w-full py-2 text-xs font-bold text-[#36B2B2] hover:bg-[#36B2B2]/5 rounded-xl transition-colors mt-1 uppercase tracking-wider">
                                                        Lihat Bukti
                                                    </button>
                                                @else
                                                    <div class="flex items-center justify-between">
                                                        <span
                                                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">Sudah
                                                            Bayar ✓</span>
                                                        <button
                                                            @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                                            class="text-[10px] font-bold text-[#36B2B2] hover:underline uppercase tracking-wider">
                                                            Lihat Bukti
                                                        </button>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="flex items-center justify-between">
                                                    <span
                                                        class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100 uppercase">Menunggu
                                                        Bukti</span>
                                                    @if($order->batas_bayar)
                                                        <span class="text-[10px] font-medium text-gray-400">Hingga:
                                                            {{ \Carbon\Carbon::parse($order->batas_bayar)->format('H:i, d M') }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @elseif($order->status === 'paid')
                                            <span
                                                class="px-3 py-2 rounded-xl text-xs font-bold text-center bg-emerald-50 text-emerald-600 uppercase border border-emerald-100">Berhasil
                                                ✓</span>
                                        @elseif($order->status === 'failed')
                                            <span
                                                class="px-3 py-2 rounded-xl text-xs font-bold text-center bg-slate-50 text-slate-500 uppercase border border-slate-200">Gagal
                                                ✗</span>
                                        @else
                                            <span
                                                class="px-3 py-2 rounded-xl text-xs font-bold text-center bg-rose-50 text-rose-500 uppercase border border-rose-100">Ditolak
                                                ✗</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 text-center mx-4">
                                    <p class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">Database Kosong</p>
                                </div>
                            @endforelse
                        </div>

                        @if(isset($orderTransaksi) && $orderTransaksi instanceof \Illuminate\Pagination\LengthAwarePaginator && $orderTransaksi->hasPages())
                            <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
                                {{ $orderTransaksi->appends(['tab' => 'order'])->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 3. Riwayat (Active/Rejected) --}}
                <div x-show="activeTab === 'riwayat'">
                    <div class="px-8 py-5 bg-teal-50/50 border-y border-teal-100/50 flex items-center justify-between mb-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-900 leading-none"
                                    x-text="currentStatus === 'active' ? 'Data Penyewa Aktif' : 'Data Penyewa Ditolak'">
                                </h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1"
                                    x-text="currentStatus === 'active' ? 'Penyewa yang sedang aktif menghuni unit Anda' : 'Daftar pendaftaran yang telah Anda batalkan'">
                                </p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-teal-200 bg-teal-100 text-teal-700">
                            {{ count($riwayatPenyewa ?? []) }} Data
                        </span>
                    </div>

                    {{-- Desktop Table View --}}
                    <div
                        class="hidden md:block overflow-x-auto bg-white border border-t-0 border-gray-100 rounded-b-3xl mb-8">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">Nama Penyewa</th>
                                    <th class="px-8 py-5">No. WhatsApp</th>
                                    <th class="px-8 py-5">Alamat</th>
                                    <th class="px-8 py-5">Tanggal</th>
                                    <th class="px-8 py-5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($riwayatPenyewa ?? [] as $penyewa)
                                    <tr class="group hover:bg-gray-50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-11 h-11 rounded-2xl {{ $statusFilter === 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }} flex items-center justify-center font-black text-sm border {{ $statusFilter === 'active' ? 'border-emerald-200' : 'border-rose-200' }}">
                                                    {{ substr(optional($penyewa)->name ?? '?', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-black text-gray-900 leading-none mb-1">
                                                        {{ optional($penyewa)->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">
                                                        {{ optional($penyewa)->email ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="font-black text-gray-700 text-sm italic tracking-tight">{{ optional($penyewa)->nomor_wa ?? '-' }}</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="text-xs font-bold text-gray-500 line-clamp-2 max-w-[200px] leading-relaxed">{{ $penyewa->alamat ?? '-' }}</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ optional($penyewa->created_at)->format('d M Y') ?? '-' }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex justify-center">
                                                @if($statusFilter === 'active')
                                                    <span
                                                        class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-emerald-500 text-white shadow-lg shadow-emerald-100 border border-emerald-400/20">AKTIF</span>
                                                @else
                                                    <span
                                                        class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-rose-500 border-2 border-rose-50 shadow-sm">DITOLAK</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-24 text-center">
                                            <div
                                                class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-gray-100">
                                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-400 text-sm font-black uppercase tracking-widest">Database
                                                Kosong</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Card View --}}
                    <div class="grid grid-cols-1 gap-4 md:hidden px-4 mb-4">
                        @forelse($riwayatPenyewa ?? [] as $penyewa)
                            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 {{ $statusFilter === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-full flex items-center justify-center font-bold text-sm shrink-0">
                                            {{ substr(optional($penyewa)->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-gray-900 text-sm truncate">
                                                {{ optional($penyewa)->name ?? 'N/A' }}
                                            </h4>
                                            <p class="text-xs text-gray-500 truncate">{{ optional($penyewa)->nomor_wa ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        @if($statusFilter === 'active')
                                            <span
                                                class="px-2.5 py-1 text-[9px] font-bold bg-emerald-500 text-white rounded-md uppercase">Aktif</span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 text-[9px] font-bold bg-white border border-rose-200 text-rose-500 rounded-md uppercase">Ditolak</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3 flex flex-col gap-1.5 text-xs">
                                    <div class="flex justify-between items-center text-gray-600">
                                        <span class="text-gray-500 font-medium">Email:</span>
                                        <span
                                            class="font-medium truncate max-w-[150px] text-right">{{ optional($penyewa)->email ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-start text-gray-600">
                                        <span class="text-gray-500 font-medium mt-0.5">Alamat:</span>
                                        <span
                                            class="font-medium text-right line-clamp-2 max-w-[180px]">{{ $penyewa->alamat ?? '-' }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center text-gray-600 mt-1 pt-2 border-t border-gray-200/60">
                                        <span class="text-gray-500 font-medium whitespace-nowrap">Tanggal Terdaftar:</span>
                                        <span class="font-medium">{{ optional($penyewa->created_at)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 text-center mx-4">
                                <p class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">Database Kosong</p>
                            </div>
                        @endforelse
                    </div>
                    @if(isset($riwayatPenyewa) && $riwayatPenyewa instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatPenyewa->hasPages())
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
                            {{ $riwayatPenyewa->appends(['tab' => 'riwayat', 'status' => $statusFilter])->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>

    {{-- Proof Modal --}}
    <template x-teleport="body">
        <div x-show="showProof" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 lg:p-10 overflow-hidden" x-cloak>

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="showProof = false"></div>

            {{-- Modal Content --}}
            <div x-show="showProof" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden active:scale-[0.99] transition-transform">

                {{-- Modal Header --}}
                <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-6 bg-[#36B2B2] rounded-full"></div>
                        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Bukti Pembayaran</h3>
                    </div>
                    <button @click="showProof = false"
                        class="p-2 hover:bg-rose-50 text-gray-400 hover:text-rose-500 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 sm:p-8 bg-white max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="relative group rounded-3xl overflow-hidden border-4 border-gray-50 shadow-inner">
                        <img :src="proofUrl" class="w-full h-auto object-contain bg-gray-50 min-h-[200px]"
                            alt="Bukti Pembayaran">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-all"></div>
                    </div>
                    <div class="mt-6 p-5 bg-teal-50/50 rounded-2xl border border-teal-100/50 flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-white text-[#36B2B2] rounded-xl flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-[#36B2B2] uppercase tracking-widest mb-1">Verifikasi
                                Manual</p>
                            <p class="text-xs text-teal-700 font-bold leading-relaxed">Silakan periksa nominal dan tanggal
                                pada bukti cetak ini dengan mutasi rekening Anda.</p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex justify-end">
                    <button @click="showProof = false"
                        class="px-8 py-3.5 bg-gray-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-xl shadow-gray-200 active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
@endsection
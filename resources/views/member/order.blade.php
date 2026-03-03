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
    }" x-init="$watch('showProof', val => val ? document.body.classList.add('modal-open') : document.body.classList.remove('modal-open'))">

        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-sm border border-white/50 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6"
            data-aos="fade-up">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Order & Verifikasi Penyewa 📦</h1>
                <p class="text-gray-500 text-sm">Kelola data penyewa yang mendaftar menggunakan kode kos Anda
                    @if(isset($kos))
                        <span class="font-bold text-[#36B2B2]">#{{ $kos->kode_kos }}</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="flex flex-nowrap gap-3 mb-8 overflow-x-auto pb-4 scrollbar-hide">
            {{-- Verifikasi (Pending Orders) --}}
            <button @click="activeTab = 'order'; currentStatus = 'verif'; window.location.href = '?tab=order&status=verif'"
                class="relative flex-shrink-0 w-44 p-4 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="(currentStatus === 'verif' || currentStatus === 'pending')
                        ? 'bg-rose-50 border-rose-500 shadow-lg shadow-rose-100 -translate-y-1'
                        : 'bg-white border-gray-50 hover:border-rose-200 shadow-md shadow-gray-200/50'">
                {{-- Action Badge --}}
                @if(($orderPendingCount ?? 0) + ($pendingCount ?? 0) > 0)
                    <div
                        class="absolute top-3 right-3 text-red-500 text-[11px] font-black z-10 animate-pulse">
                        {{ ($orderPendingCount ?? 0) + ($pendingCount ?? 0) }}
                    </div>
                @endif
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'bg-white shadow-sm text-rose-500 border border-rose-100' : 'bg-rose-50 text-rose-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black mb-0.5 transition-colors"
                    :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'text-rose-600' : 'text-gray-900'">
                    {{ ($orderPendingCount ?? 0) + ($pendingCount ?? 0) }}
                </h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors"
                    :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'text-rose-500' : 'text-gray-400'">
                    Verifikasi</p>
            </button>

            {{-- Menunggu (Verified, waiting for payment) --}}
            <button
                @click="activeTab = 'order'; currentStatus = 'menunggu'; window.location.href = '?tab=order&status=menunggu'"
                class="relative flex-shrink-0 w-44 p-4 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'menunggu'
                        ? 'bg-amber-50 border-amber-500 shadow-lg shadow-amber-100 -translate-y-1'
                        : 'bg-white border-gray-50 hover:border-amber-200 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'menunggu' ? 'bg-white shadow-sm text-amber-500 border border-amber-100' : 'bg-amber-50 text-amber-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black mb-0.5 transition-colors"
                    :class="currentStatus === 'menunggu' ? 'text-amber-600' : 'text-gray-900'">
                    {{ $orderMenungguCount ?? 0 }}</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors"
                    :class="currentStatus === 'menunggu' ? 'text-amber-500' : 'text-gray-400'">
                    Menunggu</p>
            </button>

            {{-- Konfirmasi (Verified, proof uploaded) --}}
            <button
                @click="activeTab = 'order'; currentStatus = 'konfirmasi'; window.location.href = '?tab=order&status=konfirmasi'"
                class="relative flex-shrink-0 w-44 p-4 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'konfirmasi'
                        ? 'bg-emerald-50 border-emerald-500 shadow-lg shadow-emerald-100 -translate-y-1'
                        : 'bg-white border-gray-50 hover:border-emerald-200 shadow-md shadow-gray-200/50'">
                {{-- Action Badge --}}
                @if(($orderKonfirmasiCount ?? 0) > 0)
                    <div
                        class="absolute top-3 right-3 text-red-500 text-[11px] font-black z-10 animate-pulse">
                        {{ $orderKonfirmasiCount }}
                    </div>
                @endif
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'konfirmasi' ? 'bg-white shadow-sm text-emerald-500 border border-emerald-100' : 'bg-emerald-50 text-emerald-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black mb-0.5 transition-colors"
                    :class="currentStatus === 'konfirmasi' ? 'text-emerald-600' : 'text-gray-900'">
                    {{ $orderKonfirmasiCount ?? 0 }}</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors"
                    :class="currentStatus === 'konfirmasi' ? 'text-emerald-500' : 'text-gray-400'">
                    Konfirmasi</p>
            </button>

            {{-- Aktif (Penyewa Aktif) --}}
            <button
                @click="activeTab = 'riwayat'; currentStatus = 'active'; window.location.href = '?tab=riwayat&status=active'"
                class="relative flex-shrink-0 w-44 p-4 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'active'
                        ? 'bg-teal-50 border-teal-500 shadow-lg shadow-teal-100 -translate-y-1'
                        : 'bg-white border-gray-50 hover:border-[#36B2B2]/20 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'active' ? 'bg-white shadow-sm text-[#36B2B2] border border-teal-100' : 'bg-[#36B2B2]/10 text-[#36B2B2]'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black mb-0.5 transition-colors"
                    :class="currentStatus === 'active' ? 'text-teal-600' : 'text-gray-900'">
                    {{ $activeCount ?? 0 }}</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors"
                    :class="currentStatus === 'active' ? 'text-[#36B2B2]' : 'text-gray-400'">
                    Penyewa Aktif</p>
            </button>

            {{-- Ditolak --}}
            <button
                @click="activeTab = 'riwayat'; currentStatus = 'rejected'; window.location.href = '?tab=riwayat&status=rejected'"
                class="relative flex-shrink-0 w-44 p-4 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="currentStatus === 'rejected'
                        ? 'bg-slate-50 border-slate-500 shadow-lg shadow-slate-100 -translate-y-1'
                        : 'bg-white border-gray-50 hover:border-slate-200 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="currentStatus === 'rejected' ? 'bg-white shadow-sm text-slate-500 border border-slate-200' : 'bg-slate-50 text-slate-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black mb-0.5 transition-colors"
                    :class="currentStatus === 'rejected' ? 'text-slate-600' : 'text-gray-900'">
                    {{ $rejectedCount ?? 0 }}</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors"
                    :class="currentStatus === 'rejected' ? 'text-slate-500' : 'text-gray-400'">
                    Ditolak</p>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden min-h-[500px]" data-aos="fade-up"
            data-aos-delay="100">

            {{-- Tab Header --}}
            <div
                class="px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
                <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-6 bg-[#36B2B2] rounded-full"></span>
                    <span x-text="
                                activeTab === 'order' ? 'Daftar Order & Verifikasi' :
                                (currentStatus === 'active' ? 'Penyewa Aktif' : 'Penyewa Ditolak')
                            "></span>
                </h3>

                <template x-if="activeTab === 'riwayat'">
                    <div
                        class="flex items-center p-1.5 bg-gray-100/50 backdrop-blur-md border border-gray-200 rounded-2xl shadow-inner">
                        <button @click="currentStatus = 'active'; window.location.href = '?tab=riwayat&status=active'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300"
                            :class="currentStatus === 'active' ? 'bg-white text-[#36B2B2] shadow-lg' : 'text-gray-400 hover:text-[#36B2B2]'">AKTIF</button>
                        <button @click="currentStatus = 'rejected'; window.location.href = '?tab=riwayat&status=rejected'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300"
                            :class="currentStatus === 'rejected' ? 'bg-white text-red-500 shadow-lg' : 'text-gray-400 hover:text-red-600'">DITOLAK</button>
                    </div>
                </template>
                <template x-if="activeTab === 'order'">
                    <div
                        class="flex items-center p-1.5 bg-gray-100/50 backdrop-blur-md border border-gray-200 rounded-2xl shadow-inner">
                        <button @click="currentStatus = 'verif'; window.location.href = '?tab=order&status=verif'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300"
                            :class="(currentStatus === 'verif' || currentStatus === 'pending') ? 'bg-white text-rose-600 shadow-lg' : 'text-gray-400 hover:text-rose-600'">VERIFIKASI</button>
                        <button @click="currentStatus = 'menunggu'; window.location.href = '?tab=order&status=menunggu'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300"
                            :class="currentStatus === 'menunggu' ? 'bg-white text-amber-600 shadow-lg' : 'text-gray-400 hover:text-amber-600'">MENUNGGU</button>
                        <button @click="currentStatus = 'konfirmasi'; window.location.href = '?tab=order&status=konfirmasi'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300"
                            :class="currentStatus === 'konfirmasi' ? 'bg-white text-emerald-600 shadow-lg' : 'text-gray-400 hover:text-emerald-600'">KONFIRMASI</button>
                    </div>
                </template>
            </div>

            <div class="p-0" x-data="{ showProof: false, proofUrl: '' }">
                {{-- Proof Modal --}}
                <div x-show="showProof"
                    class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm"
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

                    {{-- Section 1: Pendaftaran Akun Baru (PendingUser) - Only in VERIF status --}}
                    @if(($statusFilter === 'verif' || $statusFilter === 'pending') && isset($pendingPenyewa) && count($pendingPenyewa) > 0)
                        <div class="px-8 py-5 bg-amber-50/50 border-b border-amber-100/50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-amber-900 leading-none">Pendaftaran Akun Baru</h4>
                                    <p class="text-[10px] text-amber-600 font-bold uppercase tracking-wider mt-1">User mendaftar
                                        menggunakan Kode Kos Anda</p>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-200">{{ count($pendingPenyewa) }}
                                Menunggu</span>
                        </div>
                        <div class="overflow-x-auto mb-8">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead>
                                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
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
                                            class="group hover:bg-amber-50/30 transition-colors border-l-4 border-transparent hover:border-amber-400">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-black text-sm">
                                                        {{ substr($pending->name, 0, 1) }}</div>
                                                    <div>
                                                        <div class="font-bold text-gray-900">{{ $pending->name }}</div>
                                                        <div class="text-[10px] text-gray-400 font-medium">{{ $pending->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6"><span
                                                    class="font-bold text-gray-700 text-sm">{{ $pending->nomor_wa }}</span></td>
                                            <td class="px-8 py-6"><span
                                                    class="text-xs font-medium text-gray-500 max-w-[150px] truncate block">{{ $pending->alamat ?? '-' }}</span>
                                            </td>
                                            <td class="px-8 py-6"><span
                                                    class="text-xs font-bold text-gray-500">{{ $pending->created_at->format('d M Y') }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <form method="POST" action="{{ route('admin.penyewa.verify', $pending->id) }}">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Terima Pendaftaran?', 'Calon penyewa ini akan diverifikasi dan mendapatkan akses akun.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="px-4 py-2 rounded-xl text-xs font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-sm">
                                                            ✓ Terima
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.penyewa.reject', $pending->id) }}">
                                                        @csrf
                                                        <button type="button"
                                                            @click="window.swalConfirm('Tolak Pendaftaran?', 'Data pendaftaran ini akan dihapus permanen.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                            class="px-4 py-2 rounded-xl text-xs font-black bg-red-400 text-white hover:bg-red-500 transition-all">
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
                    @endif
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
                                            (currentStatus === 'menunggu' ? 'Menunggu Pembayaran' : 'Konfirmasi Bukti Transfer')
                                        "></h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1" x-text="
                                            currentStatus === 'verif' ? 'Pesanan baru yang baru masuk' :
                                            (currentStatus === 'menunggu' ? 'Pesanan diterima & menunggu bukti bayar' : 'Pembayaran yang perlu Anda konfirmasi')
                                        "></p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border" :class="{
                                        'bg-rose-100 text-rose-700 border-rose-200': currentStatus === 'verif',
                                        'bg-amber-100 text-amber-700 border-amber-200': currentStatus === 'menunggu',
                                        'bg-emerald-100 text-emerald-700 border-emerald-200': currentStatus === 'konfirmasi'
                                    }">
                            {{ count($orderTransaksi ?? []) }} Data
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">User</th>
                                    <th class="px-8 py-5">No. Kamar</th>
                                    <th class="px-8 py-5">Harga</th>
                                    <th class="px-8 py-5">Catatan</th>
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
                                                    {{ substr($order->user->name ?? '?', 0, 1) }}</div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                                                    <div class="text-[10px] text-gray-400 font-medium">
                                                        {{ $order->user->email ?? '' }}</div>
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
                                            <span
                                                class="text-xs text-gray-500 max-w-[150px] truncate block">{{ $order->catatan ?? '-' }}</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex flex-col items-center gap-1.5">
                                                @if($order->status === 'pending')
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
                                                @elseif($order->status === 'verified')
                                                    @if($order->bukti_pembayaran)
                                                        @if($statusFilter === 'konfirmasi')
                                                            <form method="POST" action="{{ route('admin.order.confirm', $order->id) }}">
                                                                @csrf
                                                                <button type="button"
                                                                    @click="window.swalConfirm('Konfirmasi Pembayaran?', 'Pastikan uang sudah masuk ke rekening Anda.').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                                    class="px-6 py-2 rounded-xl text-[11px] font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md active:scale-95 uppercase tracking-wider">
                                                                    ✓ Konfirmasi
                                                                </button>
                                                            </form>
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
                                            <p class="text-gray-400 text-sm font-medium">Belum ada order kamar dari user.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($orderTransaksi) && $orderTransaksi instanceof \Illuminate\Pagination\LengthAwarePaginator && $orderTransaksi->hasPages())
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
                            {{ $orderTransaksi->appends(['tab' => 'order'])->links() }}</div>
                    @endif
                </div>

                {{-- 3. Riwayat (Active/Rejected) --}}
                <div x-show="activeTab === 'riwayat'">
                    <div class="overflow-x-auto">
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
                                    <tr
                                        class="group {{ $statusFilter === 'active' ? 'hover:bg-emerald-50/50' : 'hover:bg-rose-50/50' }} transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full {{ $statusFilter === 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }} flex items-center justify-center font-black text-sm">
                                                    {{ substr($penyewa->name ?? '?', 0, 1) }}</div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ $penyewa->name ?? 'N/A' }}</div>
                                                    <div class="text-[10px] text-gray-400 font-medium">{{ $penyewa->email ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6"><span
                                                class="font-bold text-gray-700 text-sm">{{ $penyewa->nomor_wa ?? '-' }}</span></td>
                                        <td class="px-8 py-6"><span
                                                class="text-xs font-medium text-gray-500 max-w-[200px] truncate block">{{ $penyewa->alamat ?? '-' }}</span>
                                        </td>
                                        <td class="px-8 py-6"><span
                                                class="text-xs font-bold text-gray-500">{{ $penyewa->created_at ? $penyewa->created_at->format('d M Y') : '-' }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @if($statusFilter === 'active')
                                                <span
                                                    class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">AKTIF</span>
                                            @else
                                                <span
                                                    class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600">DITOLAK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-20 text-center">
                                            <div
                                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-400 text-sm font-medium">Belum ada data penyewa.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($riwayatPenyewa) && $riwayatPenyewa instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatPenyewa->hasPages())
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
                            {{ $riwayatPenyewa->appends(['tab' => 'riwayat', 'status' => $statusFilter])->links() }}</div>
                    @endif
                </div>

            </div>
        </div>

    </div>
@endsection
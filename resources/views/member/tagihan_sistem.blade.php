@extends('layouts.dashboard')

@section('dashboard-content')
    <div x-data="{ 
                        showUploadModal: false, 
                        selectedPlanName: '{{ addslashes(($pendingSubscription ?? $subscription)?->jenis_langganan?->nama ?? '') }}',
                        selectedPlanAmount: {{ 
                            $pendingSubscription
        ? (in_array($pendingSubscription->id_langganan, [4, 5]) ? $pendingSubscription->jenis_langganan->harga * $pendingSubscription->jumlah_kamar : $pendingSubscription->jenis_langganan->harga)
        : ($subscription?->jenis_langganan?->harga ?? 0) 
                        }},
                        previewUrl: null,
                        isPerKamar: false,
                        checkPlan(el) {
                            if(!el) return;
                            this.isPerKamar = el.options[el.selectedIndex].text.toLowerCase().includes('kamar');
                        }
                    }"
        x-init="$watch('showUploadModal', val => val ? document.body.classList.add('modal-open') : document.body.classList.remove('modal-open'))">

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Tagihan Sistem / Langganan 🧾</h1>
            <p class="text-gray-500">Lihat faktur masa aktif sewa sistem aplikasi Ngekos Anda atau beli paket baru.</p>
        </div>

        {{-- Pending Payment Instructions or Waiting Verification --}}
        @if($pendingSubscription?->status == 'pending')
            @if($pendingSubscription->bukti_pembayaran)
                {{-- Waiting for Verification State --}}
                <div class="bg-emerald-50 rounded-[2.5rem] p-10 sm:p-16 shadow-xl shadow-emerald-900/5 mb-10 overflow-hidden relative group border-2 border-emerald-100"
                    data-aos="fade-down" x-data="{
                                                deadline: {{ $pendingSubscription->updated_at->addDay()->timestamp * 1000 }},
                                                remaining: '00:00:00',
                                                updateTimer() {
                                                    let now = new Date().getTime();
                                                    let diff = this.deadline - now;
                                                    if (diff <= 0) {
                                                        this.remaining = '00:00:00';
                                                        return;
                                                    }
                                                    let h = Math.floor(diff / (1000 * 60 * 60));
                                                    let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                    let s = Math.floor((diff % (1000 * 60)) / 1000);
                                                    this.remaining = 
                                                        String(h).padStart(2, '0') + ':' + 
                                                        String(m).padStart(2, '0') + ':' + 
                                                        String(s).padStart(2, '0');
                                                }
                                            }" x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
                    {{-- Decorative background elements --}}
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-emerald-100/50 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-emerald-200/50 transition-colors duration-700">
                    </div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-emerald-100/30 rounded-full -ml-24 -mb-24 blur-2xl"></div>

                    <div class="relative z-10 flex flex-col items-center text-center max-w-2xl mx-auto">
                        <div
                            class="w-24 h-24 bg-white rounded-[2rem] shadow-xl shadow-emerald-200/50 flex items-center justify-center mb-8 relative group-hover:scale-110 transition-transform duration-500">
                            {{-- Pulse effect --}}
                            <div class="absolute inset-0 bg-emerald-400 rounded-[2rem] animate-ping opacity-20"></div>

                            <svg class="w-12 h-12 text-emerald-500 relative z-10" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h3 class="text-3xl font-black text-emerald-950 uppercase tracking-tighter mb-4">Bukti Terkirim! 🚀</h3>
                        <div class="h-1.5 w-20 bg-emerald-400 rounded-full mb-8"></div>

                        <p class="text-emerald-900 text-lg font-bold leading-relaxed mb-6">
                            Terima kasih! Bukti pembayaran Anda untuk paket <span
                                class="bg-emerald-200/50 px-2 py-1 rounded text-emerald-950">{{ $pendingSubscription->jenis_langganan->nama }}</span>
                            sudah kami terima.
                        </p>

                        <div class="mt-8 flex flex-col sm:flex-row items-center gap-4">
                            <div
                                class="bg-white/60 backdrop-blur-sm px-8 py-4 rounded-2xl border border-emerald-100 flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 animate-spin" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span class="text-sm font-black text-emerald-900 uppercase tracking-widest">Menunggu Konfirmasi
                                    Admin</span>
                            </div>

                        </div>

                        <div
                            class="mt-4 flex items-center gap-2 bg-emerald-950 text-emerald-400 px-6 py-2 rounded-full shadow-lg border border-emerald-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-lg font-black tracking-widest font-mono" x-text="remaining">00:00:00</span>
                        </div>

                        <p class="mt-8 text-xs text-emerald-700/60 font-medium italic">
                            Proses verifikasi maksimal 1x24 jam. Silakan segarkan halaman ini secara berkala.
                        </p>
                    </div>
                </div>
            @else
                {{-- Payment Instructions State (Original UI) --}}
                <div class="bg-blue-50 rounded-[2.5rem] p-8 sm:p-12 shadow-xl shadow-blue-900/5 mb-10 overflow-hidden relative group border-2 border-blue-100"
                    data-aos="fade-down">
                    <div class="relative z-10 flex flex-col lg:flex-row gap-10">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-8">
                                <div
                                    class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                                    <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-blue-900 uppercase tracking-tight">Sirkulasi Pembayaran
                                        Paket</h3>
                                    <div class="h-1.5 w-24 bg-blue-600 rounded-full mt-1"></div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-10">
                                <p class="text-blue-800 text-lg font-semibold leading-relaxed">
                                    Anda telah memilih paket <span
                                        class="text-blue-600 font-extrabold underline decoration-blue-200 underline-offset-4">{{ $pendingSubscription->jenis_langganan->nama }}</span>.
                                    Silakan lakukan pembayaran sebesar <span class="text-gray-900 font-black">Rp
                                        {{ number_format(in_array($pendingSubscription->id_langganan, [4, 5]) ? $pendingSubscription->jenis_langganan->harga * $pendingSubscription->jumlah_kamar : $pendingSubscription->jenis_langganan->harga, 0, ',', '.') }}</span>
                                    ke salah satu
                                    rekening di bawah ini:
                                </p>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div
                                    class="bg-white p-6 rounded-[2rem] border-2 border-blue-100 shadow-sm hover:border-blue-300 transition-all group/bank">
                                    <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-3">BANK BCA</p>
                                    <p class="text-2xl font-black text-gray-900 mb-1">1234567890</p>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">A.N NGEKOS INDONESIA</p>
                                </div>
                                <div
                                    class="bg-white p-6 rounded-[2rem] border-2 border-indigo-100 shadow-sm hover:border-indigo-300 transition-all group/bank">
                                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-3">DANA / OVO</p>
                                    <p class="text-2xl font-black text-gray-900 mb-1">0812-3456-7890</p>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">A.N NGEKOS INDONESIA</p>
                                </div>
                            </div>
                        </div>

                        <div class="lg:w-96 flex-shrink-0">
                            <div
                                class="bg-white rounded-[2.5rem] p-10 text-center shadow-xl relative overflow-hidden group/card h-full flex flex-col justify-center border-2 border-blue-100">
                                {{-- Decorative light circle --}}
                                <div
                                    class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-10 -mt-10 blur-2xl opacity-50">
                                </div>

                                <div
                                    class="w-20 h-20 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-blue-100">
                                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-xl font-black text-blue-900 mb-3 uppercase tracking-tighter">Sudah Bayar?</h4>
                                <p class="text-xs text-blue-800 mb-8 leading-relaxed font-medium">Klik tombol di bawah untuk unggah
                                    bukti dan aktifkan paket Anda secara instan.</p>

                                <button type="button" @click="showUploadModal = true"
                                    class="w-full py-5 bg-blue-600 text-blue rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 active:scale-95 flex items-center justify-center gap-3">
                                    <span>Unggah Bukti</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Rejected Notification --}}
        @if($pendingSubscription?->status == 'rejected')
            <div class="bg-rose-50 border-l-4 border-rose-500 p-6 mb-10 rounded-r-[2rem] shadow-sm shadow-rose-900/5"
                data-aos="fade-down">
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-rose-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-rose-900 font-black uppercase tracking-tight mb-1">Pembayaran Paket Ditolak</h4>
                        <p class="text-rose-800 text-sm font-medium leading-relaxed">
                            Maaf, bukti pembayaran Anda ditolak oleh admin. Silakan periksa kembali nominal atau kualitas foto
                            bukti bayar Anda, lalu ajukan kembali.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Notification Banner --}}
        @if($subscription?->status == 'active')
            @if($computedStatus == 'grace')
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-8 rounded-r-xl shadow-sm" data-aos="fade-down">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-800 font-medium font-inter">
                                <span class="font-black text-amber-900">MASA TENGGANG!</span>
                                Paket Anda sudah habis. Anda memiliki sisa toleransi <span
                                    class="bg-amber-200 px-2 py-0.5 rounded font-black text-amber-900">{{ $graceDaysRemaining }}
                                    Hari</span> sebelum akses dibatasi.
                                <br>
                                <span class="text-xs opacity-80 italic italic-inter">Segera perbarui paket untuk tetap dapat
                                    mengelola kos Anda.</span>
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($computedStatus == 'inactive')
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-xl shadow-sm" data-aos="fade-down">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800 font-medium">
                                <span class="font-bold">AKUN MATI!</span> Masa aktif paket Anda telah habis lebih dari 3 hari.
                                Akses fitur akan segera dibatasi. Silakan lakukan pembayaran paket baru sekarang!
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
            <!-- Active Package Card -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                    <!-- Decoration -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#36B2B2]/5 rounded-full blur-3xl"></div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z">
                                </path>
                            </svg>
                            Paket Aktif Saat Ini
                        </h3>
                        @if($subscription)
                            @if($computedStatus == 'active')
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-green-50 text-green-600 border border-green-100 uppercase tracking-tighter">
                                    Sistem Aktif
                                </span>
                            @elseif($computedStatus == 'grace')
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tighter">
                                    Masa Tenggang
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-red-50 text-red-600 border border-red-100 uppercase tracking-tighter">
                                    Akun Mati
                                </span>
                            @endif
                        @elseif($pendingSubscription?->status == 'pending')
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-tighter">
                                Menunggu Aktivasi
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-gray-50 text-gray-400 border border-gray-100 uppercase tracking-tighter">
                                Belum Berlangganan
                            </span>
                        @endif
                    </div>

                    @php
                        $purchaseDate = $subscription?->tanggal_pembayaran ? \Carbon\Carbon::parse($subscription->tanggal_pembayaran) : null;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Basic Info -->
                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jenis Paket
                                </p>
                                <p class="text-xl font-black text-gray-800">
                                    {{ $subscription?->jenis_langganan?->nama ?? 'Non-Aktif' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Harga Paket
                                </p>
                                <p class="text-lg font-bold text-[#36B2B2]">
                                    Rp {{ number_format($subscription?->jenis_langganan?->harga ?? 0, 0, ',', '.') }}
                                    @if($subscription?->jumlah_kamar > 0)
                                        <span class="text-gray-400 text-xs font-normal">/ {{ $subscription->jumlah_kamar }}
                                            Kamar</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Date Info -->
                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal
                                    Pembelian</p>
                                <p class="text-gray-800 font-bold">
                                    {{ $purchaseDate ? $purchaseDate->translatedFormat('d F Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kapan Berakhir
                                </p>
                                <p class="text-gray-800 font-bold">
                                    {{ $expiryDate ? $expiryDate->translatedFormat('d F Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Countdown Info -->
                        @php
                            $statusColors = [
                                'active' => 'bg-[#36B2B2]/5 border-[#36B2B2]/10 text-[#36B2B2]',
                                'grace' => 'bg-amber-50 border-amber-100 text-amber-600',
                                'inactive' => 'bg-red-50 border-red-100 text-red-600'
                            ];
                            $currentColor = $statusColors[$computedStatus] ?? $statusColors['active'];
                        @endphp
                        <div class="{{ $currentColor }} rounded-2xl p-6 flex flex-col items-center justify-center border">
                            <p class="text-[10px] font-bold opacity-60 uppercase tracking-widest mb-2">
                                @if($computedStatus == 'grace')
                                    Masa Tenggang
                                @elseif($computedStatus == 'inactive')
                                    Masa Mati
                                @else
                                    Sisa Masa Aktif
                                @endif
                            </p>
                            <div class="flex items-baseline gap-1">
                                @if($computedStatus == 'grace')
                                    <span class="text-4xl font-black">{{ $graceDaysRemaining }}</span>
                                @elseif($computedStatus == 'inactive')
                                    <span class="text-4xl font-black">{{ $matiDaysCount }}</span>
                                @else
                                    <span class="text-4xl font-black">{{ abs($daysRemaining) }}</span>
                                @endif
                                <span class="text-sm font-bold opacity-60">Hari</span>
                            </div>
                            @if($computedStatus == 'grace')
                                <p class="text-[9px] font-bold mt-2 uppercase">Sisa Toleransi</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-50 text-[11px] text-gray-400 italic">
                        * Sistem menggunakan metode pembayaran di muka. Paket otomatis aktif setelah konfirmasi pembayaran
                        diterima.
                    </div>
                </div>

                <!-- History Section Expanded -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Lengkap Pembelian
                    </h4>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                                    <th class="pb-4">Paket</th>
                                    <th class="pb-4">Tgl Pembelian</th>
                                    <th class="pb-4">Masa Terakhir</th>
                                    <th class="pb-4">Harga</th>
                                    <th class="pb-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs">
                                @forelse($history as $item)
                                    <tr class="border-b border-gray-50 last:border-0">
                                        <td class="py-4 font-bold text-gray-800">{{ $item->jenis_langganan->nama }}</td>
                                        <td class="py-4 text-gray-600">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d M Y') }}
                                        </td>
                                        <td class="py-4 text-red-500 font-medium">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->addDays(30)->format('d M Y') }}
                                        </td>
                                        <td class="py-4 font-bold text-gray-800">Rp
                                            {{ number_format($item->jenis_langganan->harga, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4">
                                            <span
                                                class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-1 rounded-md uppercase">Lunas</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada riwayat
                                            transaksi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $history->links() }}
                    </div>
                </div>
            </div>

            <!-- Buy New Plan Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Beli & Ganti Paket
                    </h4>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">Pilih paket baru untuk memperpanjang atau
                        meningkatkan fitur aplikasi Anda.</p>

                    <form action="{{ route('admin.subscription.update') }}" method="POST" class="space-y-4"
                        x-init="nextTick(() => checkPlan($refs.planSelect))">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Daftar
                                Paket</label>
                            <select name="id_langganan" x-ref="planSelect" @change="checkPlan($event.target)"
                                class="w-full rounded-xl border-gray-100 bg-gray-50 text-sm font-medium focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 transition-all cursor-pointer">
                                @foreach($availablePlans as $plan)
                                    @if($plan->id != 1) {{-- Hide Member Biasa (Basic/Free) from dropdown --}}
                                        <option value="{{ $plan->id }}" {{ $subscription?->id_langganan == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->nama }} - Rp {{ number_format($plan->harga, 0, ',', '.') }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div x-show="isPerKamar" x-transition class="mt-4">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Jumlah
                                Kamar</label>
                            <input type="number" name="jumlah_kamar" min="1" :disabled="!isPerKamar"
                                value="{{ max(1, $subscription?->jumlah_kamar ?? 1) }}"
                                class="w-full rounded-xl border-gray-100 bg-gray-50 text-sm font-bold focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 transition-all">
                        </div>

                        <button type="submit" 
                                @if($pendingSubscription?->status == 'pending' && $pendingSubscription->bukti_pembayaran && !$pendingSubscription->updated_at->addDay()->isPast()) disabled @endif 
                                class="w-full py-4 rounded-xl font-bold transition-all active:scale-95 shadow-lg mt-2 flex items-center justify-center gap-2
                                @if($pendingSubscription?->status == 'pending' && $pendingSubscription->bukti_pembayaran && !$pendingSubscription->updated_at->addDay()->isPast())
                                    bg-gray-100 text-gray-400 cursor-not-allowed shadow-none
                                @else
                                    bg-gray-900 text-white hover:bg-black shadow-gray-200
                                @endif">
                            @if($pendingSubscription?->status == 'pending' && $pendingSubscription->bukti_pembayaran && !$pendingSubscription->updated_at->addDay()->isPast())
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Sedang Diverifikasi...</span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $pendingSubscription && $pendingSubscription->status == 'pending' ? 'Ganti Pesanan' : 'Proses Pembelian' }}</span>
                            @endif
                        </button>

                        @if($pendingSubscription?->status == 'pending' && $pendingSubscription->bukti_pembayaran && !$pendingSubscription->updated_at->addDay()->isPast())
                            <p class="text-[9px] text-rose-500 font-black text-center mt-3 uppercase tracking-tighter">
                                Pesanan Anda sedang diproses oleh sistem!
                            </p>
                        @elseif($pendingSubscription?->status == 'pending' && !$pendingSubscription->bukti_pembayaran)
                            <p class="text-[9px] text-amber-500 font-black text-center mt-3 uppercase tracking-tighter">
                                Anda bisa mengganti paket sebelum membayar!
                            </p>
                        @endif
                        <p class="text-[9px] text-gray-400 text-center mt-2">Tersedia via Transfer Bank & E-Wallet</p>
                    </form>
                </div>
            </div>
        </div>

        {{-- Upload Modal --}}
        <template x-teleport="body">
            <div x-show="showUploadModal" class="fixed inset-0 z-[9999] overflow-y-auto"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak style="display: none;">

                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showUploadModal = false"></div>

                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]"
                        @click.stop>

                        <!-- Modal Header (Fixed) -->
                        <div class="p-6 bg-gray-50 border-b border-gray-100 flex items-center justify-between shrink-0">
                            <div>
                                <h3 class="text-xl font-black text-gray-900">Unggah Bukti Bayar</h3>
                                <p class="text-xs text-gray-500 mt-1" x-text="selectedPlanName"></p>
                            </div>
                            <button @click="showUploadModal = false"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body (Scrollable) -->
                        <div class="overflow-y-auto flex-1 custom-scrollbar">
                            <form action="{{ route('admin.subscription.upload-proof') }}" method="POST"
                                enctype="multipart/form-data" class="p-8">
                                @csrf
                                <div class="mb-6">
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih
                                        Metode</label>
                                    <select name="metode_pembayaran" required
                                        class="w-full rounded-xl border-gray-100 bg-gray-50 text-xs font-bold focus:border-blue-500 focus:ring-blue-500/10">
                                        <option value="BCA">Transfer BCA</option>
                                        <option value="DANA">DANA</option>
                                        <option value="OVO">OVO</option>
                                        <option value="LAINNYA">Lainnya</option>
                                    </select>
                                </div>

                                <div class="mb-6 p-4 bg-blue-50 rounded-2xl border border-blue-100">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-blue-600 mb-1">Total
                                        Pembayaran</div>
                                    <div class="text-xl font-black text-gray-900">Rp <span
                                            x-text="new Intl.NumberFormat('id-ID').format(selectedPlanAmount)"></span>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <label
                                        class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-4 text-center">
                                        Pilih Cara Unggah Bukti
                                    </label>

                                    <div class="grid grid-cols-2 gap-4">
                                        <button type="button" onclick="document.getElementById('cameraInput').click()"
                                            class="flex flex-col items-center justify-center p-6 rounded-3xl bg-emerald-50 border-2 border-emerald-100 hover:border-emerald-500 hover:bg-emerald-100 transition-all group">
                                            <div
                                                class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <span
                                                class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Kamera</span>
                                        </button>

                                        <button type="button" onclick="document.getElementById('galleryInput').click()"
                                            class="flex flex-col items-center justify-center p-6 rounded-3xl bg-blue-50 border-2 border-blue-100 hover:border-blue-500 hover:bg-blue-100 transition-all group">
                                            <div
                                                class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <span
                                                class="text-[10px] font-black uppercase tracking-widest text-blue-700">Galeri</span>
                                        </button>
                                    </div>

                                    <input type="file" id="cameraInput" name="bukti_pembayaran" accept="image/*"
                                        capture="environment" class="hidden"
                                        @change="const file = $event.target.files[0]; if(file) { previewUrl = URL.createObjectURL(file); document.getElementById('galleryInput').value = ''; }">
                                    <input type="file" id="galleryInput" name="bukti_pembayaran" accept="image/*"
                                        class="hidden"
                                        @change="const file = $event.target.files[0]; if(file) { previewUrl = URL.createObjectURL(file); document.getElementById('cameraInput').value = ''; }">

                                    <div x-show="previewUrl"
                                        class="mt-8 p-3 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100 transition-all"
                                        x-cloak>
                                        <div class="relative group">
                                            <img :src="previewUrl"
                                                class="w-full h-auto max-h-[300px] object-contain rounded-2xl shadow-sm bg-gray-100">
                                            <button type="button" @click="previewUrl = null"
                                                class="absolute -top-3 -right-3 p-2 bg-rose-500 text-white rounded-full shadow-lg hover:bg-rose-600 transition-all hover:scale-110">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p
                                            class="text-[10px] text-center text-blue-600 font-black mt-3 uppercase tracking-widest">
                                            FOTO SIAP DIUNGGAH! ✨</p>
                                    </div>

                                    <p class="text-[10px] text-gray-400 mt-4 text-center font-medium">Format: JPG, PNG,
                                        JPEG. Max: 500MB</p>
                                </div>

                                <button type="submit" :disabled="!previewUrl"
                                    class="w-full py-5 rounded-[1.5rem] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 shadow-xl"
                                    :class="previewUrl ? 'bg-blue-600 text-white hover:bg-blue-700 active:scale-95 shadow-blue-500/30' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                    :style="previewUrl ? 'background-color: #2563eb !important; color: white !important;' : 'background-color: #f3f4f6 !important; color: #9ca3af !important;'">
                                    <span>Konfirmasi Pembayaran</span>
                                    <svg class="w-5 h-5 transition-transform" :class="previewUrl ? 'translate-x-1' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
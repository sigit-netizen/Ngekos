@extends('layouts.dashboard')

@section('dashboard-content')
    @php
        $tab = request('tab', 'all');
    @endphp

    <div x-data="{ 
                                            activeTab: '{{ $tab }}', 
                                            showUploadModal: false, 
                                            selectedOrderId: null,
                                            selectedOrderName: '',
                                            selectedOrderAmount: 0,
                                            showProof: false,
                                            proofUrl: '',
                                            previewUrl: null
                                        }"
        x-init="$watch('showUploadModal', val => val ? document.body.classList.add('modal-open') : document.body.classList.remove('modal-open')); 
                                                    $watch('showProof', val => val ? document.body.classList.add('modal-open') : document.body.classList.remove('modal-open'))">

        {{-- Header Summary Card --}}
        <div class="bg-gradient-to-br from-[#36B2B2] to-[#2D8E8E] rounded-[2.5rem] p-8 sm:p-12 shadow-2xl shadow-[#36B2B2]/20 mb-10 overflow-hidden relative group"
            data-aos="fade-up">
            <div
                class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700">
            </div>
            <div
                class="absolute -bottom-24 -left-24 w-64 h-64 bg-black/10 rounded-full blur-3xl group-hover:bg-black/20 transition-all duration-700">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3">Pesanan Anda 🏠</h1>
                    <p class="text-white/80 text-lg font-medium max-w-md line-clamp-2">Kelola seluruh riwayat sewa kos Anda
                        dengan mudah dalam satu tempat.</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md rounded-3xl p-6 border border-white/30 text-center min-w-[160px]">
                    <span
                        class="block text-5xl font-black text-white mb-1">{{ ($pendingCount ?? 0) + ($verifiedCount ?? 0) + ($rejectedCount ?? 0) }}</span>
                    <span class="text-[10px] font-bold text-white/80 uppercase tracking-[0.2em]">Total Pesanan</span>
                </div>
            </div>
        </div>

        {{-- Order Cards List --}}
        <div class="space-y-6">
            @forelse($orders ?? [] as $index => $order)
                @php
                    $statusConfig = [
                        'pending' => [
                            'bg' => 'bg-gradient-to-r from-amber-400 to-amber-500',
                            'text' => 'text-white shadow-lg shadow-amber-500/30',
                            'label' => 'Menunggu Verifikasi',
                            'message' => 'Sabar bosku, pesananmu lagi dicek nih! ⏳',
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
                        ],
                        'verified' => [
                            'bg' => 'bg-gradient-to-r from-blue-400 to-blue-500',
                            'text' => 'text-white shadow-lg shadow-blue-500/30',
                            'label' => $order->bukti_pembayaran ? 'Menunggu Validasi' : 'Verifikasi Berhasil',
                            'message' => $order->bukti_pembayaran ? 'Mantap! Bukti bayarmu lagi diproses! 🚀' : 'Yeay! Verifikasi Berhasil, gas langsung bayar yuk! 💸',
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                        ],
                        'paid' => [
                            'bg' => 'bg-gradient-to-r from-emerald-400 to-emerald-500',
                            'text' => 'text-white shadow-lg shadow-emerald-500/30',
                            'label' => 'Pesanan Diterima',
                            'message' => 'Kelas! Kos impianmu udah official jadi markas barumu. 🔥',
                            'icon' => 'M5 13l4 4L19 7'
                        ],
                        'failed' => [
                            'bg' => 'bg-gradient-to-r from-gray-400 to-gray-500',
                            'text' => 'text-white shadow-lg shadow-gray-500/30',
                            'label' => 'Gagal',
                            'message' => 'Waduh, pesanan gagal. Tetap semangat cari kos yang lain! 💪',
                            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
                        ],
                        'rejected' => [
                            'bg' => 'bg-gradient-to-r from-rose-400 to-rose-500',
                            'text' => 'text-white shadow-lg shadow-rose-500/30',
                            'label' => 'Ditolak',
                            'message' => 'Eits ditolak bos. Kalem, pasti ada kos yang lebih keren buatmu! 🌟',
                            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
                        ],
                    ];
                    $sc = $statusConfig[$order->status] ?? [
                        'bg' => 'bg-gray-100',
                        'text' => 'text-gray-600',
                        'label' => $order->status,
                        'message' => 'Status tidak diketahui.',
                        'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    ];
                @endphp
                <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border-2 {{ $order->status === 'paid' ? 'border-emerald-200' : 'border-gray-50' }} shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden relative group"
                    data-aos="fade-up" data-aos-delay="{{ ($index % 5) * 100 }}">

                    {{-- Status Ribbon --}}
                    <div
                        class="absolute top-0 right-0 px-6 py-2.5 {{ $sc['bg'] }} {{ $sc['text'] }} rounded-bl-3xl font-black text-[10px] uppercase tracking-widest z-10 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sc['icon'] }}"></path>
                        </svg>
                        {{ $sc['label'] }}

                        {{-- Countdown Timer --}}
                        @php
                            $expiryTime = null;
                            if ($order->status === 'pending') {
                                $expiryTime = $order->created_at->addDay();
                            } elseif ($order->status === 'verified' && !$order->bukti_pembayaran) {
                                $expiryTime = $order->batas_bayar;
                            } elseif ($order->status === 'verified' && $order->bukti_pembayaran) {
                                $expiryTime = $order->tanggal_pembayaran->addDay();
                            }
                        @endphp

                        @if($expiryTime)
                            <div x-data="{ 
                                            expiryTime: new Date('{{ $expiryTime->toIso8601String() }}').getTime(),
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
                                        }" class="px-2 py-1 bg-red-600 text-white rounded-lg animate-pulse ml-2 shadow-inner border border-red-400">
                                <span x-text="timer" class="font-bold text-[11px] leading-none"></span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col lg:flex-row gap-8 relative z-0 mt-6">
                        {{-- Main Info --}}
                        <div class="flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                                <div>
                                    <h3
                                        class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-[#36B2B2] to-blue-600 transition-colors leading-tight mb-2">
                                        {{ $order->kamar->kos->nama_kos ?? 'N/A' }}
                                    </h3>

                                    {{-- Cool Message based on status --}}
                                    <p class="text-sm font-bold text-gray-400 italic mb-4">"{{ $sc['message'] }}"</p>

                                    <div class="flex flex-wrap items-center gap-3 mt-2">
                                        <div
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-xl border border-gray-100">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                            <span class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">Kamar:
                                                {{ $order->kamar->nomor_kamar ?? '-' }}</span>
                                        </div>
                                        <div
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-[#36B2B2]/10 rounded-xl border border-[#36B2B2]/20">
                                            <svg class="w-4 h-4 text-[#36B2B2]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <span class="text-[11px] font-black text-[#36B2B2] uppercase tracking-wider">Rp
                                                {{ number_format($order->jumlah_bayar, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $order->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Progress Stepper --}}
                            <div class="grid grid-cols-4 gap-3 py-6 relative">
                                @php
                                    $steps = [
                                        ['label' => 'Dipesan', 'done' => true],
                                        ['label' => 'Diverifikasi', 'done' => in_array($order->status, ['verified', 'paid'])],
                                        ['label' => 'Dibayar', 'done' => $order->bukti_pembayaran || $order->status === 'paid'],
                                        ['label' => 'Selesai', 'done' => $order->status === 'paid'],
                                    ];
                                @endphp
                                @foreach($steps as $index => $step)
                                    <div class="space-y-3">
                                        <div
                                            class="h-1.5 rounded-full transition-all duration-1000 ease-out {{ $step['done'] ? 'bg-[#36B2B2] shadow-[0_0_10px_rgba(54,178,178,0.5)]' : 'bg-gray-100' }}">
                                        </div>
                                        <span
                                            class="block text-[8px] font-bold uppercase tracking-widest {{ $step['done'] ? 'text-[#36B2B2]' : 'text-gray-300' }}">
                                            {{ $step['label'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Actions Footer --}}
                            <div class="flex flex-wrap items-center gap-4 pt-6 border-t border-gray-50 mt-2 relative z-50">
                                @if($order->status === 'pending')
                                    <button type="button"
                                        @click="window.swalConfirm('Batalkan Pesanan?', 'Tindakan ini permanen dan tidak dapat diubah.', 'warning').then(res => res.isConfirmed && document.getElementById('cancel-order-{{ $order->id }}').submit())"
                                        class="px-8 py-3 bg-rose-50 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-100 transition-all active:scale-95 cursor-pointer">
                                        Batalkan Pesanan
                                    </button>
                                    <form id="cancel-order-{{ $order->id }}" action="{{ route('user.order.cancel', $order->id) }}"
                                        method="POST" class="hidden">@csrf</form>
                                @elseif($order->status === 'verified' && !$order->bukti_pembayaran)
                                    <button type="button"
                                        x-on:click.prevent="selectedOrderId = {{ $order->id }}; selectedOrderName = '{{ addslashes($order->kamar->kos->nama_kos ?? 'N/A') }}'; selectedOrderAmount = {{ $order->jumlah_bayar }}; showUploadModal = true;"
                                        class="px-10 py-3.5 bg-[#36B2B2] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-[#2D8E8E] transition-all shadow-xl shadow-[#36B2B2]/20 active:scale-95 cursor-pointer">
                                        Unggah Bukti Pembayaran
                                    </button>
                                @elseif($order->bukti_pembayaran)
                                    <button type="button"
                                        @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                        class="px-8 py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all flex items-center gap-2 border border-gray-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Bukti Saya
                                    </button>
                                @endif

                                @if($order->status === 'rejected')
                                    <div class="flex items-center gap-2 text-rose-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider">Pesanan Ditolak oleh
                                            Pengelola</span>
                                    </div>
                                @endif

                                @if($order->status === 'paid')
                                    <div class="flex items-center gap-2 text-emerald-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider">Sewa Telah Aktif - Selamat
                                            Menikmati!</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Side Icon Dekorasi --}}
                        <div
                            class="hidden lg:flex items-center justify-center w-48 bg-gray-50/50 rounded-3xl group-hover:bg-[#36B2B2]/5 transition-all duration-500 border border-transparent group-hover:border-[#36B2B2]/10">
                            <div class="relative">
                                <svg class="w-20 h-20 text-gray-200 group-hover:text-[#36B2B2]/20 transition-all duration-700"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path>
                                </svg>
                                <div
                                    class="absolute inset-0 flex items-center justify-center scale-0 group-hover:scale-100 transition-transform duration-500 delay-100">
                                    <div
                                        class="w-12 h-12 bg-white rounded-2xl shadow-lg flex items-center justify-center text-[#36B2B2]">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-[2.5rem] p-20 text-center border border-dashed border-gray-200" data-aos="fade-up">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Belum ada Order</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">Anda belum memiliki riwayat pesanan sewa kos. Cari kos impian Anda
                        sekarang!</p>
                    <a href="{{ route('user.dashboard') }}"
                        class="inline-flex items-center gap-2 mt-8 px-8 py-3.5 bg-[#36B2B2] text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:shadow-xl hover:shadow-[#36B2B2]/30 transition-all">
                        Cari Kos Sekarang
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Proof Modal --}}
        <template x-teleport="body">
            <div x-show="showProof"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm"
                @click="showProof = false" x-cloak style="display: none;">
                <div class="relative max-w-2xl w-full bg-white rounded-3xl p-2 shadow-2xl overflow-hidden" @click.stop>
                    <button @click="showProof = false"
                        class="absolute top-4 right-4 p-2.5 bg-gray-900/40 hover:bg-gray-900/60 text-white rounded-full transition-all z-[70] backdrop-blur-sm shadow-xl border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                    <img :src="proofUrl" class="w-full h-auto rounded-2xl max-h-[80vh] object-contain">
                </div>
            </div>
        </template>

        {{-- Pagination --}}
        <!-- removed closing div to wrap the modals -->

        {{-- Upload Modal --}}
        <template x-teleport="body">
            <div x-show="showUploadModal" class="fixed inset-0 z-[9999] overflow-y-auto"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak style="display: none;">

                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showUploadModal = false"></div>

                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden"
                        @click.stop>

                        <div class="p-6 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-black text-gray-900">Unggah Bukti</h3>
                                <p class="text-xs text-gray-500 mt-1" x-text="selectedOrderName"></p>
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

                        <form :action="'/user/order/' + selectedOrderId + '/upload-proof'" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="p-8">
                                <div class="mb-6 p-4 bg-[#36B2B2]/5 rounded-2xl border border-[#36B2B2]/10">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-[#36B2B2] mb-1">Total
                                        Pembayaran</div>
                                    <div class="text-xl font-black text-gray-900">Rp <span
                                            x-text="new Intl.NumberFormat('id-ID').format(selectedOrderAmount)"></span>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label
                                        class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-4 text-center">
                                        Pilih Cara Unggah Bukti
                                    </label>

                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Camera Button -->
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

                                        <!-- Gallery Button -->
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

                                    <!-- Hidden Inputs -->
                                    <input type="file" id="cameraInput" name="bukti_pembayaran_camera" accept="image/*"
                                        capture="environment" class="hidden"
                                        @change="const file = $event.target.files[0]; if(file) { previewUrl = URL.createObjectURL(file); document.getElementById('galleryInput').value = ''; }">
                                    <input type="file" id="galleryInput" name="bukti_pembayaran_gallery" accept="image/*"
                                        class="hidden"
                                        @change="const file = $event.target.files[0]; if(file) { previewUrl = URL.createObjectURL(file); document.getElementById('cameraInput').value = ''; }">

                                    <div x-show="previewUrl"
                                        class="mt-8 p-3 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100 transition-all"
                                        x-cloak>
                                        <div class="relative group">
                                            <img :src="previewUrl"
                                                class="w-full h-auto max-h-[300px] object-contain rounded-2xl shadow-sm bg-gray-100">
                                            <button type="button"
                                                @click="previewUrl = null; document.getElementById('cameraInput').value = ''; document.getElementById('galleryInput').value = '';"
                                                class="absolute -top-3 -right-3 p-2 bg-rose-500 text-white rounded-full shadow-lg hover:bg-rose-600 transition-all hover:scale-110">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p
                                            class="text-[10px] text-center text-[#36B2B2] font-black mt-3 uppercase tracking-widest text-[0.15em]">
                                            FOTO SIAP DIUNGGAH! ✨</p>
                                    </div>

                                    <p class="text-[10px] text-gray-400 mt-4 text-center font-medium">Format: JPG, PNG,
                                        JPEG.
                                        Max: 10MB</p>
                                </div>

                                <button type="submit" :disabled="!previewUrl"
                                    :class="!previewUrl ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#2D8E8E] active:scale-95 shadow-[#36B2B2]/30'"
                                    class="w-full py-4 bg-[#36B2B2] text-white font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl">
                                    Konfirmasi Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
        </template>
    </div> <!-- Closing the main x-data wrapper -->
@endsection
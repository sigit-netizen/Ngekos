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
        proofUrl: ''
    }" x-init="$watch('showUploadModal', val => val ? document.body.classList.add('modal-open') : document.body.classList.remove('modal-open')); 
                $watch('showProof', val => val ? document.body.classList.add('modal-open') : document.body.classList.remove('modal-open'))">

        {{-- Header Summary Card --}}
        <div class="bg-gradient-to-br from-[#36B2B2] to-[#2D8E8E] rounded-[2.5rem] p-8 sm:p-12 shadow-2xl shadow-[#36B2B2]/20 mb-10 overflow-hidden relative group" data-aos="fade-up">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-black/10 rounded-full blur-3xl group-hover:bg-black/20 transition-all duration-700"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3">Pesanan Anda 🏠</h1>
                    <p class="text-white/80 text-lg font-medium max-w-md line-clamp-2">Kelola seluruh riwayat sewa kos Anda dengan mudah dalam satu tempat.</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md rounded-3xl p-6 border border-white/30 text-center min-w-[160px]">
                    <span class="block text-5xl font-black text-white mb-1">{{ ($pendingCount ?? 0) + ($verifiedCount ?? 0) + ($rejectedCount ?? 0) }}</span>
                    <span class="text-[10px] font-bold text-white/80 uppercase tracking-[0.2em]">Total Pesanan</span>
                </div>
            </div>
        </div>

        {{-- Order Cards List --}}
        <div class="space-y-6">
            @forelse($orders ?? [] as $index => $order)
                <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden relative group" 
                    data-aos="fade-up" data-aos-delay="{{ ($index % 5) * 100 }}">
                    <div class="flex flex-col lg:flex-row gap-8">
                        {{-- Main Info --}}
                        <div class="flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                                <div>
                                    <h3 class="text-2xl font-black text-gray-900 group-hover:text-[#36B2B2] transition-colors leading-tight">
                                        {{ $order->kamar->kos->nama_kos ?? 'N/A' }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="px-3 py-1 bg-gray-100 rounded-lg text-[10px] font-bold text-gray-500 uppercase tracking-wider">No. Kamar: {{ $order->kamar->nomor_kamar ?? '-' }}</span>
                                        <span class="px-3 py-1 bg-[#36B2B2]/10 rounded-lg text-[10px] font-bold text-[#36B2B2] uppercase tracking-wider">Rp {{ number_format($order->jumlah_bayar, 0, ',', '.') }}</span>
                                        <span class="px-3 py-1 bg-blue-50 rounded-lg text-[10px] font-bold text-blue-400 uppercase tracking-wider">ID: #{{ $order->id }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $order->created_at->format('d M Y') }}</span>
                                    
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'label' => 'Menunggu Verifikasi'],
                                            'verified' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'label' => $order->bukti_pembayaran ? 'Menunggu Validasi' : 'Verifikasi Berhasil'],
                                            'paid' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'label' => 'Pesanan Diterima'],
                                            'failed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'Gagal'],
                                            'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'label' => 'Ditolak'],
                                        ];
                                        $sc = $statusConfig[$order->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => $order->status];
                                    @endphp
                                    <span class="mt-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $sc['bg'] }} {{ $sc['text'] }}">
                                        {{ $sc['label'] }}
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
                                        <div class="h-1.5 rounded-full transition-all duration-1000 ease-out {{ $step['done'] ? 'bg-[#36B2B2] shadow-[0_0_10px_rgba(54,178,178,0.5)]' : 'bg-gray-100' }}"></div>
                                        <span class="block text-[8px] font-bold uppercase tracking-widest {{ $step['done'] ? 'text-[#36B2B2]' : 'text-gray-300' }}">
                                            {{ $step['label'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Actions Footer --}}
                            <div class="flex flex-wrap items-center gap-4 pt-6 border-t border-gray-50 mt-2">
                                @if($order->status === 'pending')
                                    <button type="button"
                                        @click="window.swalConfirm('Batalkan Pesanan?', 'Tindakan ini permanen dan tidak dapat diubah.', 'warning').then(res => res.isConfirmed && document.getElementById('cancel-order-{{ $order->id }}').submit())"
                                        class="px-8 py-3 bg-rose-50 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-100 transition-all active:scale-95">
                                        Batalkan Pesanan
                                    </button>
                                    <form id="cancel-order-{{ $order->id }}" action="{{ route('user.order.cancel', $order->id) }}" method="POST" class="hidden">@csrf</form>
                                @elseif($order->status === 'verified' && !$order->bukti_pembayaran)
                                    <button type="button"
                                        @click="selectedOrderId = {{ $order->id }}; selectedOrderName = '{{ $order->kamar->kos->nama_kos ?? 'N/A' }}'; selectedOrderAmount = {{ $order->jumlah_bayar }}; showUploadModal = true"
                                        class="px-10 py-3.5 bg-[#36B2B2] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-[#2D8E8E] transition-all shadow-xl shadow-[#36B2B2]/20 active:scale-95">
                                        Unggah Bukti Pembayaran
                                    </button>
                                @elseif($order->bukti_pembayaran)
                                    <button type="button" @click="proofUrl = '{{ asset($order->bukti_pembayaran) }}'; showProof = true"
                                        class="px-8 py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all flex items-center gap-2 border border-gray-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Bukti Saya
                                    </button>
                                @endif

                                @if($order->status === 'rejected')
                                    <div class="flex items-center gap-2 text-rose-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider">Pesanan Ditolak oleh Pengelola</span>
                                    </div>
                                @endif
                                
                                @if($order->status === 'paid')
                                    <div class="flex items-center gap-2 text-emerald-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider">Sewa Telah Aktif - Selamat Menikmati!</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Side Icon Dekorasi --}}
                        <div class="hidden lg:flex items-center justify-center w-48 bg-gray-50/50 rounded-3xl group-hover:bg-[#36B2B2]/5 transition-all duration-500 border border-transparent group-hover:border-[#36B2B2]/10">
                            <div class="relative">
                                <svg class="w-20 h-20 text-gray-200 group-hover:text-[#36B2B2]/20 transition-all duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center scale-0 group-hover:scale-100 transition-transform duration-500 delay-100">
                                     <div class="w-12 h-12 bg-white rounded-2xl shadow-lg flex items-center justify-center text-[#36B2B2]">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Belum ada Order</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">Anda belum memiliki riwayat pesanan sewa kos. Cari kos impian Anda sekarang!</p>
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 mt-8 px-8 py-3.5 bg-[#36B2B2] text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:shadow-xl hover:shadow-[#36B2B2]/30 transition-all">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <img :src="proofUrl" class="w-full h-auto rounded-2xl max-h-[80vh] object-contain">
                </div>
            </div>
        </template>

        {{-- Pagination --}}
        @if(isset($orders) && $orders instanceof \Illuminate\Pagination\LengthAwarePaginator && $orders->hasPages())
            <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $orders->appends(['tab' => $tab])->links() }}</div>
        @endif
    </div>

    {{-- Upload Modal --}}
    <template x-teleport="body">
        <div x-show="showUploadModal" 
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            
            <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-gray-100"
                @click.away="showUploadModal = false">
                
                <div class="p-6 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">Unggah Bukti</h3>
                        <p class="text-xs text-gray-500 mt-1" x-text="selectedOrderName"></p>
                    </div>
                    <button @click="showUploadModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form :action="'/user/order/' + selectedOrderId + '/upload-proof'" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-8">
                        <div class="mb-6 p-4 bg-[#36B2B2]/5 rounded-2xl border border-[#36B2B2]/10">
                            <div class="text-[10px] font-black uppercase tracking-widest text-[#36B2B2] mb-1">Total Pembayaran</div>
                            <div class="text-xl font-black text-gray-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(selectedOrderAmount)"></span></div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-700 mb-2">Pilih File Bukti Transfer</label>
                            <input type="file" name="bukti_pembayaran" required accept="image/*"
                                class="w-full px-4 py-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl focus:border-[#36B2B2] outline-none text-xs font-bold text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#36B2B2] file:text-white hover:file:bg-[#2b8f8f] transition-all cursor-pointer">
                            <p class="text-[10px] text-gray-400 mt-2 font-medium">Format: JPG, PNG, JPEG. Max: 2MB</p>
                        </div>

                        <button type="submit" 
                            class="w-full py-4 bg-[#36B2B2] text-white font-black uppercase tracking-widest rounded-2xl hover:bg-[#2D8E8E] transition-all shadow-xl shadow-[#36B2B2]/30 active:scale-95">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
@endsection
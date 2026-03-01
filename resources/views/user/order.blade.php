@extends('layouts.dashboard')

@section('dashboard-content')
    @php
        $tab = request('tab', 'all');
    @endphp

    <div x-data="{ activeTab: '{{ $tab }}' }">

        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Daftar Order 🛍️</h1>
            <p class="text-gray-500">Pantau seluruh riwayat pesanan sewa kos Anda di halaman ini.</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8" data-aos="fade-up" data-aos-delay="100">
            <button @click="activeTab = 'pending'; window.location.href = '?tab=pending'"
                class="relative p-5 rounded-2xl border-2 transition-all duration-300 text-left group overflow-hidden"
                :class="activeTab === 'pending' ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' : 'bg-white border-gray-50 hover:border-[#36B2B2]/30 shadow-md'">
                <h3 class="text-3xl font-black mb-1 transition-colors" :class="activeTab === 'pending' ? 'text-white' : 'text-gray-900'">{{ $pendingCount ?? 0 }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors" :class="activeTab === 'pending' ? 'text-white/90' : 'text-amber-500'">Menunggu</p>
            </button>
            <button @click="activeTab = 'verified'; window.location.href = '?tab=verified'"
                class="relative p-5 rounded-2xl border-2 transition-all duration-300 text-left group overflow-hidden"
                :class="activeTab === 'verified' ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' : 'bg-white border-gray-50 hover:border-emerald-200 shadow-md'">
                <h3 class="text-3xl font-black mb-1 transition-colors" :class="activeTab === 'verified' ? 'text-white' : 'text-gray-900'">{{ $verifiedCount ?? 0 }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors" :class="activeTab === 'verified' ? 'text-white/90' : 'text-emerald-500'">Diterima</p>
            </button>
            <button @click="activeTab = 'rejected'; window.location.href = '?tab=rejected'"
                class="relative p-5 rounded-2xl border-2 transition-all duration-300 text-left group overflow-hidden"
                :class="activeTab === 'rejected' ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' : 'bg-white border-gray-50 hover:border-rose-200 shadow-md'">
                <h3 class="text-3xl font-black mb-1 transition-colors" :class="activeTab === 'rejected' ? 'text-white' : 'text-gray-900'">{{ $rejectedCount ?? 0 }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors" :class="activeTab === 'rejected' ? 'text-white/90' : 'text-rose-500'">Ditolak</p>
            </button>
        </div>

        {{-- Order Table --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden" data-aos="fade-up" data-aos-delay="200">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/30">
                <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-6 bg-[#36B2B2] rounded-full"></span>
                    Riwayat Order
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                            <th class="px-8 py-5">Kos</th>
                            <th class="px-8 py-5">No. Kamar</th>
                            <th class="px-8 py-5">Harga</th>
                            <th class="px-8 py-5">Tanggal Order</th>
                            <th class="px-8 py-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($orders ?? [] as $order)
                            @php
                                $shouldShow = true;
                                if ($tab === 'pending' && $order->status !== 'pending') $shouldShow = false;
                                if ($tab === 'verified' && $order->status !== 'verified') $shouldShow = false;
                                if ($tab === 'rejected' && $order->status !== 'rejected') $shouldShow = false;
                            @endphp
                            @if($shouldShow)
                                <tr class="group hover:bg-[#36B2B2]/5 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-[#36B2B2]/10 flex items-center justify-center text-[#36B2B2] font-black text-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $order->kamar->kos->nama_kos ?? 'N/A' }}</div>
                                                <div class="text-[10px] text-gray-400 font-medium">Kode: {{ $order->kode_kos }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="font-bold text-gray-700 text-sm">{{ $order->kamar->nomor_kamar ?? '-' }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="font-bold text-[#36B2B2] text-sm">Rp {{ number_format($order->jumlah_bayar, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($order->status === 'pending')
                                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-600">Menunggu</span>
                                        @elseif($order->status === 'verified')
                                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">Diterima</span>
                                        @else
                                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada Order</h3>
                                    <p class="text-gray-500">Silakan cari kos di <a href="{{ route('user.dashboard') }}" class="text-[#36B2B2] font-bold hover:underline">Dashboard</a> dan buat pesanan pertama Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($orders) && $orders instanceof \Illuminate\Pagination\LengthAwarePaginator && $orders->hasPages())
                <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $orders->appends(['tab' => $tab])->links() }}</div>
            @endif
        </div>
    </div>
@endsection
@extends('layouts.dashboard')

@section('dashboard-content')
    @php
        $tab = $tab ?? 'order';
        $statusFilter = $statusFilter ?? 'active';
    @endphp

    <div x-data="{ activeTab: '{{ $tab }}', currentStatus: '{{ $statusFilter }}' }">

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
        <div class="flex gap-4 mb-8">
            {{-- Order Kamar --}}
            <button @click="activeTab = 'order'; window.location.href = '?tab=order'"
                class="relative flex-1 p-5 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'order'
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1'
                    : 'bg-white border-gray-50 hover:border-blue-200 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'order' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    @if(($orderPendingCount ?? 0) > 0)
                        <span class="flex h-3 w-3"><span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                    @endif
                </div>
                <h3 class="text-3xl font-black mb-0.5 transition-colors" :class="activeTab === 'order' ? 'text-white' : 'text-gray-900'">{{ $orderPendingCount ?? 0 }}</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors" :class="activeTab === 'order' ? 'text-white/90' : 'text-blue-500'">Order Kamar</p>
            </button>

            {{-- Aktif --}}
            <button @click="activeTab = 'riwayat'; window.location.href = '?tab=riwayat&status=active'"
                class="relative flex-1 p-5 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'riwayat' && currentStatus === 'active'
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1'
                    : 'bg-white border-gray-50 hover:border-emerald-200 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'riwayat' && currentStatus === 'active' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-black mb-0.5 transition-colors" :class="activeTab === 'riwayat' && currentStatus === 'active' ? 'text-white' : 'text-gray-900'">{{ $activeCount ?? 0 }}</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors" :class="activeTab === 'riwayat' && currentStatus === 'active' ? 'text-white/90' : 'text-emerald-500'">Penyewa Aktif</p>
            </button>

            {{-- Ditolak --}}
            <button @click="activeTab = 'riwayat'; window.location.href = '?tab=riwayat&status=rejected'"
                class="relative flex-1 p-5 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'riwayat' && currentStatus === 'rejected'
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1'
                    : 'bg-white border-gray-50 hover:border-rose-200 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'riwayat' && currentStatus === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-500'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-black mb-0.5 transition-colors" :class="activeTab === 'riwayat' && currentStatus === 'rejected' ? 'text-white' : 'text-gray-900'">{{ $rejectedCount ?? 0 }}</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.15em] transition-colors" :class="activeTab === 'riwayat' && currentStatus === 'rejected' ? 'text-white/90' : 'text-rose-500'">Ditolak</p>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden min-h-[500px]" data-aos="fade-up" data-aos-delay="100">

            {{-- Tab Header --}}
            <div class="px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
                <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-6 bg-[#36B2B2] rounded-full"></span>
                    <span x-text="
                        activeTab === 'order' ? 'Order Kamar dari User' :
                        (currentStatus === 'active' ? 'Penyewa Aktif' : 'Penyewa Ditolak')
                    "></span>
                </h3>

                <template x-if="activeTab === 'riwayat'">
                    <div class="flex items-center p-1.5 bg-gray-100/50 backdrop-blur-md border border-gray-200 rounded-2xl shadow-inner">
                        <button @click="currentStatus = 'active'; window.location.href = '?tab=riwayat&status=active'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300"
                            :class="currentStatus === 'active' ? 'bg-green-500 text-white shadow-lg' : 'text-gray-400 hover:text-green-600'">AKTIF</button>
                        <button @click="currentStatus = 'rejected'; window.location.href = '?tab=riwayat&status=rejected'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300"
                            :class="currentStatus === 'rejected' ? 'bg-red-500 text-white shadow-lg' : 'text-gray-400 hover:text-red-600'">DITOLAK</button>
                    </div>
                </template>
            </div>

            <div class="p-0">

                {{-- 1. Order Kamar --}}
                <div x-show="activeTab === 'order'">
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
                                    <tr class="group hover:bg-blue-50/50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black text-sm">{{ substr($order->user->name ?? '?', 0, 1) }}</div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                                                    <div class="text-[10px] text-gray-400 font-medium">{{ $order->user->email ?? '' }}</div>
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
                                            <span class="text-xs text-gray-500 max-w-[150px] truncate block">{{ $order->catatan ?? '-' }}</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @if($order->status === 'pending')
                                                <div class="flex items-center justify-center gap-2">
                                                    <form method="POST" action="{{ route('admin.order.verify', $order->id) }}" onsubmit="return confirm('Verifikasi order ini? User akan menjadi penyewa.')">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md active:scale-95">
                                                            ✓ Terima
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.order.reject', $order->id) }}" onsubmit="return confirm('Tolak order ini?')">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black bg-red-500 text-white hover:bg-red-600 transition-all shadow-sm hover:shadow-md active:scale-95">
                                                            ✗ Tolak
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($order->status === 'verified')
                                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">DITERIMA</span>
                                            @else
                                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600">DITOLAK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-8 py-20 text-center">
                                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                            <p class="text-gray-400 text-sm font-medium">Belum ada order kamar dari user.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($orderTransaksi) && $orderTransaksi instanceof \Illuminate\Pagination\LengthAwarePaginator && $orderTransaksi->hasPages())
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $orderTransaksi->appends(['tab' => 'order'])->links() }}</div>
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
                                    <tr class="group {{ $statusFilter === 'active' ? 'hover:bg-emerald-50/50' : 'hover:bg-rose-50/50' }} transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full {{ $statusFilter === 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }} flex items-center justify-center font-black text-sm">{{ substr($penyewa->name, 0, 1) }}</div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ $penyewa->name }}</div>
                                                    <div class="text-[10px] text-gray-400 font-medium">{{ $penyewa->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6"><span class="font-bold text-gray-700 text-sm">{{ $penyewa->nomor_wa }}</span></td>
                                        <td class="px-8 py-6"><span class="text-xs font-medium text-gray-500 max-w-[200px] truncate block">{{ $penyewa->alamat }}</span></td>
                                        <td class="px-8 py-6"><span class="text-xs font-bold text-gray-500">{{ $penyewa->created_at->format('d M Y') }}</span></td>
                                        <td class="px-8 py-6 text-center">
                                            @if($statusFilter === 'active')
                                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">AKTIF</span>
                                            @else
                                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600">DITOLAK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-20 text-center">
                                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <p class="text-gray-400 text-sm font-medium">Belum ada data penyewa.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($riwayatPenyewa) && $riwayatPenyewa instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatPenyewa->hasPages())
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $riwayatPenyewa->appends(['tab' => 'riwayat', 'status' => $statusFilter])->links() }}</div>
                    @endif
                </div>

            </div>
        </div>

    </div>
@endsection

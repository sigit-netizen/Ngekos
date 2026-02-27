@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Tagihan Sistem / Langganan 🧾</h1>
        <p class="text-gray-500">Lihat faktur masa aktif sewa sistem aplikasi Ngekos Anda atau beli paket baru.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
        <!-- Active Package Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                <!-- Decoration -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#36B2B2]/5 rounded-full blur-3xl"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path>
                        </svg>
                        Paket Aktif Saat Ini
                    </h3>
                    @if($subscription?->status == 'active')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-green-50 text-green-600 border border-green-100 uppercase tracking-tighter">
                            Sistem Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tighter">
                            Menunggu Aktivasi
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
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jenis Paket</p>
                            <p class="text-xl font-black text-gray-800">{{ $subscription?->jenis_langganan?->nama ?? 'Non-Aktif' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Harga Paket</p>
                            <p class="text-lg font-bold text-[#36B2B2]">
                                Rp {{ number_format($subscription?->jenis_langganan?->harga ?? 0, 0, ',', '.') }}
                                @if($subscription?->jumlah_kamar > 0)
                                    <span class="text-gray-400 text-xs font-normal">/ {{ $subscription->jumlah_kamar }} Kamar</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Date Info -->
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Pembelian</p>
                            <p class="text-gray-800 font-bold">{{ $purchaseDate ? $purchaseDate->translatedFormat('d F Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kapan Berakhir</p>
                            <p class="text-gray-800 font-bold">{{ $expiryDate ? $expiryDate->translatedFormat('d F Y') : '-' }}</p>
                        </div>
                    </div>

                    <!-- Countdown Info -->
                    <div class="bg-[#36B2B2]/5 rounded-2xl p-6 flex flex-col items-center justify-center border border-[#36B2B2]/10">
                        <p class="text-[10px] font-bold text-[#36B2B2] uppercase tracking-widest mb-2">Sisa Masa Aktif</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-black text-[#36B2B2]">{{ $daysRemaining }}</span>
                            <span class="text-sm font-bold text-[#36B2B2]/60">Hari</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-50 text-[11px] text-gray-400 italic">
                    * Sistem menggunakan metode pembayaran di muka. Paket otomatis aktif setelah konfirmasi pembayaran diterima.
                </div>
            </div>

            <!-- History Section Expanded -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Lengkap Pembelian
                </h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
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
                                    <td class="py-4 text-gray-600">{{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d M Y') }}</td>
                                    <td class="py-4 text-red-500 font-medium">{{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->addDays(30)->format('d M Y') }}</td>
                                    <td class="py-4 font-bold text-gray-800">Rp {{ number_format($item->jenis_langganan->harga, 0, ',', '.') }}</td>
                                    <td class="py-4">
                                        <span class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-1 rounded-md uppercase">Lunas</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada riwayat transaksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Buy New Plan Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Beli & Ganti Paket
                </h4>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Pilih paket baru untuk memperpanjang atau meningkatkan fitur aplikasi Anda.</p>
                
                <form action="{{ route('admin.subscription.update') }}" method="POST" class="space-y-4" x-data="{ 
                    isPerKamar: false,
                    checkPlan(el) {
                        this.isPerKamar = el.options[el.selectedIndex].text.toLowerCase().includes('kamar');
                    }
                }" x-init="checkPlan($refs.planSelect)">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Daftar Paket</label>
                        <select name="id_langganan" x-ref="planSelect" @change="checkPlan($event.target)" class="w-full rounded-xl border-gray-100 bg-gray-50 text-sm font-medium focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 transition-all cursor-pointer">
                            @foreach($availablePlans as $plan)
                                <option value="{{ $plan->id }}" {{ $subscription?->id_langganan == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->nama }} - Rp {{ number_format($plan->harga, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="isPerKamar" x-transition class="mt-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Jumlah Kamar</label>
                        <input type="number" name="jumlah_kamar" min="1" value="{{ $subscription?->jumlah_kamar ?? 1 }}" class="w-full rounded-xl border-gray-100 bg-gray-50 text-sm font-bold focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 transition-all">
                    </div>

                    <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200 mt-2">
                        Proses Pembelian
                    </button>
                    <p class="text-[9px] text-gray-400 text-center">Tersedia via Transfer Bank & E-Wallet</p>
                </form>
            </div>
        </div>
    </div>
@endsection
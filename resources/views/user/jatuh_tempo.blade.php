@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Jatuh Tempo Sewa ⏰</h1>
        <p class="text-gray-500 text-sm">Selalu perhatikan tanggal pembayaran dan sisa tempo sewa Anda agar tidak terkena denda.</p>
    </div>

    {{-- Banner Notifikasi (Notification Banner) --}}
    @if($computedStatus == 'grace')
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-8 rounded-r-xl shadow-sm" data-aos="fade-down">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-amber-800 font-medium">
                        <span class="font-black text-amber-900 uppercase">MASA TENGGANG!</span> 
                        Batas sewa Anda sudah habis. Anda memiliki sisa toleransi <span class="bg-amber-200 px-2 py-0.5 rounded font-black text-amber-900">{{ $graceDaysRemaining }} Hari</span> sebelum akses dibatasi.
                        <br>
                        <span class="text-xs opacity-80 italic">Segera lakukan pembayaran sewa untuk tetap dapat menempati kamar.</span>
                    </p>
                </div>
            </div>
        </div>
    @elseif($computedStatus == 'inactive')
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-xl shadow-sm" data-aos="fade-down">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800 font-medium">
                        <span class="font-bold uppercase">AKUN MATI!</span> Masa aktif sewa Anda telah habis lebih dari 3 hari. 
                        Akses layanan akan dibatasi. Silakan hubungi pengelola atau lakukan pembayaran segera!
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
        <!-- Kartu Paket Aktif (Active Package Card) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                <!-- Dekorasi (Decoration) -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#36B2B2]/5 rounded-full blur-3xl"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path>
                        </svg>
                        Status Sewa Aktif
                    </h3>
                    @if($computedStatus == 'active')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-green-50 text-green-600 border border-green-100 uppercase tracking-tighter">
                            Sewa Aktif
                        </span>
                    @elseif($computedStatus == 'grace')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tighter">
                            Masa Tenggang
                        </span>
                    @elseif($computedStatus == 'inactive')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-red-50 text-red-600 border border-red-100 uppercase tracking-tighter">
                            Akun Mati
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-gray-50 text-gray-600 border border-gray-100 uppercase tracking-tighter">
                            Belum Ada Sewa
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Informasi Dasar (Basic Info) -->
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kamar Ditempati</p>
                            <p class="text-xl font-black text-gray-800">{{ $user->kamar?->nomor_kamar ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Harga Sewa</p>
                            <p class="text-lg font-bold text-[#36B2B2]">
                                Rp {{ number_format($user->kamar?->harga ?? 0, 0, ',', '.') }}
                                <span class="text-gray-400 text-xs font-normal">/ {{ $user->kamar?->tipe_durasi ?? 'bulan' }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Informasi Tanggal (Date Info) -->
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Bayar Terakhir</p>
                            <p class="text-gray-800 font-bold">{{ $lastRent?->tanggal_pembayaran ? $lastRent->tanggal_pembayaran->translatedFormat('d F Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jatuh Tempo</p>
                            <p class="text-gray-800 font-bold">{{ $expiryDate ? $expiryDate->translatedFormat('d F Y') : '-' }}</p>
                        </div>
                    </div>

                    <!-- Informasi Hitung Mundur (Countdown Info) -->
                    @php
                        $statusColors = [
                            'active' => 'bg-[#36B2B2]/5 border-[#36B2B2]/10 text-[#36B2B2]',
                            'grace' => 'bg-amber-50 border-amber-100 text-amber-600',
                            'inactive' => 'bg-red-50 border-red-100 text-red-600',
                            'none' => 'bg-gray-50 border-gray-100 text-gray-400'
                        ];
                        $currentColor = $statusColors[$computedStatus] ?? $statusColors['active'];
                    @endphp
                    <div class="{{ $currentColor }} rounded-2xl p-6 flex flex-col items-center justify-center border transition-all duration-500">
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
                            @elseif($computedStatus == 'none')
                                <span class="text-4xl font-black">0</span>
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
                    * Pembayaran sewa dilakukan di awal periode. Mohon lakukan konfirmasi pembayaran setelah transfer agar status diperbarui.
                </div>
            </div>

            <!-- Bagian Riwayat (History Section) -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Pembayaran Sewa
                </h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                                <th class="pb-4">Nomor Kamar</th>
                                <th class="pb-4">Tgl Bayar</th>
                                <th class="pb-4">Jumlah</th>
                                <th class="pb-4">Metode</th>
                                <th class="pb-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse($history as $item)
                                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 font-bold text-gray-800">{{ $item->kamar?->nomor_kamar ?? '-' }}</td>
                                    <td class="py-4 text-gray-600">{{ $item->tanggal_pembayaran ? $item->tanggal_pembayaran->format('d M Y') : '-' }}</td>
                                    <td class="py-4 font-bold text-gray-800">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td class="py-4 text-gray-500 uppercase">{{ $item->metode_pembayaran }}</td>
                                    <td class="py-4">
                                        @if($item->status == 'paid')
                                            <span class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-1 rounded-md uppercase">Lunas</span>
                                        @else
                                            <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-md uppercase">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada riwayat pembayaran sewa</td>
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

        <!-- Kartu Beli Paket Baru (Buy New Plan Card) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden h-fit">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Bayar Sewa
                </h4>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Perpanjang masa aktif sewa Anda dengan melakukan pembayaran untuk periode berikutnya.</p>
                
                @if($user->isPenyewa())
                    <form action="{{ route('user.jatuh_tempo.store') }}" method="POST" class="space-y-4" x-data="{ metodePembayaran: 'manual' }">
                        @csrf
                        <input type="hidden" name="id_kamar" value="{{ $user->id_kamar }}">
                        <input type="hidden" name="kode_kos" value="{{ $user->kosAnak?->kode_kos }}">
                        <input type="hidden" name="jumlah_bayar" value="{{ $user->kamar?->harga }}">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Metode Pembayaran</label>
                            <select name="metode_pembayaran" x-model="metodePembayaran" required class="w-full rounded-xl border-gray-100 bg-gray-50 text-sm font-medium focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 transition-all cursor-pointer">
                                <option value="manual">Transfer Manual</option>
                                <option value="pymen">Pymen (Otomatis)</option>
                            </select>
                        </div>

                        <div x-show="metodePembayaran === 'manual'" x-transition class="space-y-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Batas Bayar (Kapan Anda akan bayar?)</label>
                            <input type="datetime-local" name="batas_bayar" :required="metodePembayaran === 'manual'" class="w-full rounded-xl border-gray-100 bg-gray-50 text-sm font-medium focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 transition-all">
                            <p class="text-[9px] text-gray-400 italic">* Tentukan kapan Anda berencana melakukan transfer.</p>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Total Bayar</span>
                                <span class="text-sm font-black text-gray-900">Rp {{ number_format($user->kamar?->harga ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-[9px] text-gray-400 italic">Harga sesuai tarif kamar Anda.</p>
                        </div>

                        <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200 mt-2">
                            Lakukan Pembayaran
                        </button>
                    </form>
                @else
                    <div class="p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200 flex flex-col items-center text-center">
                        <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs font-medium text-gray-500">Anda belum menyewa kamar apapun saat ini.</p>
                    </div>
                @endif

                <p class="text-[9px] text-gray-400 text-center mt-4 uppercase tracking-tighter">Pembayaran Aman & Terverifikasi</p>
            </div>
        </div>
    </div>
@endsection

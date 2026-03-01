@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 shadow-sm border border-white/50 mb-8" data-aos="fade-up">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Laporan Pembayaran member 💰</h1>
                <p class="text-gray-500 text-xs mt-1">Analisis pendapatan dan status langganan seluruh pemilik kos.</p>
            </div>
        </div>

        <!-- Horizontal Compact Filters -->
        <form action="{{ route('superadmin.laporan_pembayaran') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
            <!-- Search Member -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nama
                    Member</label>
                <div class="relative group">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari pemilik..."
                        class="w-full rounded-xl border-gray-200 bg-white text-xs font-bold focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 pl-10 h-10 transition-all">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Plan Filter -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Paket</label>
                <select name="plan"
                    class="w-full rounded-xl border-gray-200 bg-white text-xs font-bold focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 h-10 cursor-pointer transition-all">
                    <option value="">Semua Paket</option>
                    @foreach($availablePlans as $plan)
                        <option value="{{ $plan->id }}" {{ $selectedPlan == $plan->id ? 'selected' : '' }}>{{ $plan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Year Filter -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tahun</label>
                <select name="year"
                    class="w-full rounded-xl border-gray-200 bg-white text-xs font-bold focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 h-10 cursor-pointer transition-all">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Month Filter -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Bulan</label>
                <select name="month"
                    class="w-full rounded-xl border-gray-200 bg-white text-xs font-bold focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 h-10 cursor-pointer transition-all">
                    <option value="">Jan - Des</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-2">
                <label
                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
                <select name="status"
                    class="w-full rounded-xl border-gray-200 bg-white text-xs font-bold focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 h-10 cursor-pointer transition-all">
                    <option value="">Semua Status</option>
                    <option value="active" {{ $selectedStatus == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="grace" {{ $selectedStatus == 'grace' ? 'selected' : '' }}>Masa Tenggang</option>
                    <option value="inactive" {{ $selectedStatus == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="lg:col-span-12 xl:col-span-2 flex gap-2">
                <button type="submit"
                    class="flex-1 h-10 bg-[#36B2B2] text-white rounded-xl text-xs font-bold hover:bg-[#2b8f8f] transition-all shadow-sm active:scale-95">
                    Cari
                </button>
                <a href="{{ route('superadmin.laporan_pembayaran') }}"
                    class="flex-1 h-10 bg-gray-200 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-300 transition-all flex items-center justify-center active:scale-95">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Standardized Stats Widgets -->
    <div class="flex flex-row flex-nowrap gap-4 mb-8" data-aos="fade-up" data-aos-delay="50">
        <!-- Total Member -->
        <div
            class="flex-1 min-w-0 group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Member</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-gray-900">{{ $totalMember }}</span>
                        <span class="text-[10px] text-gray-400 font-medium">Orang</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Aktif -->
        <div
            class="flex-1 min-w-0 group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-[#36B2B2]">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-[#36B2B2]/10 flex items-center justify-center text-[#36B2B2] group-hover:bg-[#36B2B2]/20 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Member Aktif</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-[#36B2B2]">{{ $totalActive }}</span>
                        <span class="text-[10px] text-[#36B2B2]/60 font-medium">Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Masa Tenggang -->
        <div
            class="flex-1 min-w-0 group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-amber-500">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 group-hover:bg-amber-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Masa Tenggang</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-amber-600">{{ $totalGrace }}</span>
                        <span class="text-[10px] text-amber-400 font-medium">Hampir Mati</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Mati -->
        <div
            class="flex-1 min-w-0 group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-red-500">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 group-hover:bg-red-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Member Mati</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-red-600">{{ $totalInactive }}</span>
                        <span class="text-[10px] text-red-400 font-medium">Bermasalah</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Nonaktif -->
        <div
            class="flex-1 min-w-0 group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-gray-900">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-900 group-hover:bg-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Nonaktif</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-gray-900">{{ $totalDeactivated }}</span>
                        <span class="text-[10px] text-gray-400 font-medium">Off</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Total Transaksi -->
        <div
            class="flex-1 min-w-0 group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Order</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-gray-900">{{ $totalOrderCount }}</span>
                        <span class="text-[10px] text-gray-400 font-medium">Invoice</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Omzet -->
        <div
            class="flex-1 min-w-0 group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-emerald-500">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16v2a2 2 0 01-2 2H9a2 2 0 01-2-2v-2m3-12V4a1 1 0 011-1h2a1 1 0 011 1v2m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Omzet</p>
                    <span class="text-xl font-black text-emerald-600">Rp
                        {{ number_format($totalOmzetAmount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Quick-Filters -->
    <div class="flex flex-wrap items-center gap-2 mb-4" data-aos="fade-up" data-aos-delay="75">
        <a href="{{ route('superadmin.laporan_pembayaran', array_merge(request()->except('page'), ['status' => ''])) }}"
            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ !$selectedStatus ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
            Semua
        </a>
        <a href="{{ route('superadmin.laporan_pembayaran', array_merge(request()->except('page'), ['status' => 'active'])) }}"
            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $selectedStatus == 'active' ? 'bg-[#36B2B2] text-white shadow-lg shadow-[#36B2B2]/20' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
            Masa Aktif
        </a>
        <a href="{{ route('superadmin.laporan_pembayaran', array_merge(request()->except('page'), ['status' => 'grace'])) }}"
            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $selectedStatus == 'grace' ? 'bg-amber-500 text-white shadow-lg shadow-amber-100' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
            Masa Tenggang
        </a>
        <a href="{{ route('superadmin.laporan_pembayaran', array_merge(request()->except('page'), ['status' => 'inactive'])) }}"
            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $selectedStatus == 'inactive' ? 'bg-red-500 text-white shadow-lg shadow-red-100' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
            Mati
        </a>
        <a href="{{ route('superadmin.laporan_pembayaran', array_merge(request()->except('page'), ['status' => 'deactivated'])) }}"
            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $selectedStatus == 'deactivated' ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
            Nonaktif
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden" data-aos="fade-up"
        data-aos-delay="100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-8 py-5">Nama Member</th>
                        <th class="px-6 py-5">Paket Langganan</th>
                        <th class="px-6 py-5">Tgl Pembelian</th>
                        <th class="px-6 py-5">Sisa Durasi</th>
                        <th class="px-6 py-5">Jatuh Tempo</th>
                        <th class="px-8 py-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="font-black text-gray-900">{{ $sub->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $sub->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <span
                                    class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-black uppercase tracking-tighter">
                                    {{ $sub->jenis_langganan->nama }}
                                </span>
                                <div class="text-[10px] text-green-600 font-bold mt-1">Rp
                                    {{ number_format($sub->jenis_langganan->harga, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-5 text-[11px] font-bold text-gray-600 uppercase">
                                {{ \Carbon\Carbon::parse($sub->tanggal_pembayaran)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-5">
                                @if($sub->computed_status == 'active')
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div>
                                        <span class="text-sm font-black text-blue-600">{{ $sub->days_remaining }} Hari</span>
                                    </div>
                                @elseif($sub->computed_status == 'grace')
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-amber-600 uppercase italic">Masa Tenggang</span>
                                        <span class="text-[9px] text-amber-500 font-bold leading-none mt-0.5">
                                            @if($sub->grace_days_remaining > 0)
                                                Sisa {{ $sub->grace_days_remaining }} Hari Lagi
                                            @else
                                                Hari Terakhir
                                            @endif
                                        </span>
                                    </div>
                                @elseif($sub->computed_status == 'deactivated')
                                    <span
                                        class="text-[10px] font-black text-gray-400 italic uppercase bg-gray-50 px-2 py-0.5 rounded border border-gray-100">Pasif
                                        / Nonaktif</span>
                                @else
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-red-500 uppercase italic">Akun Mati</span>
                                        <span class="text-[9px] text-red-400 font-bold leading-none mt-0.5">
                                            Mati {{ $sub->inactive_days_count }} Hari
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-[11px] font-black text-gray-900 uppercase">
                                {{ $sub->expiry_date->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($sub->computed_status == 'active')
                                    <span
                                        class="px-4 py-1.5 bg-green-500 text-white rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg shadow-green-100">AKTIF</span>
                                @elseif($sub->computed_status == 'grace')
                                    <span
                                        class="px-4 py-1.5 bg-amber-500 text-white rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg shadow-amber-100">TENGGANG</span>
                                @elseif($sub->computed_status == 'deactivated')
                                    <span
                                        class="px-4 py-1.5 bg-gray-900 text-white rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg shadow-gray-200">NONAKTIF</span>
                                @else
                                    <div class="flex flex-col items-center gap-2">
                                        <span
                                            class="px-4 py-1.5 bg-red-600 text-white rounded-full text-[9px] font-black uppercase tracking-widest animate-pulse">MATI</span>
                                        <form action="{{ route('superadmin.user.deactivate', $sub->user->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan member ini?')">
                                            @csrf
                                            <button type="submit"
                                                class="text-[8px] font-black text-red-500 hover:text-red-700 uppercase tracking-tighter border-b border-red-200">Nonaktifkan
                                                Akun</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-gray-50 mb-4">
                                    <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-400 italic font-mono">DATA TIDAK DITEMUKAN</h3>
                                <p class="text-[10px] text-gray-300 mt-1">Coba sesuaikan filter tahun, bulan, atau paket Anda.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 px-8 pb-8">
            {{ $subscriptions->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
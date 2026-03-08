@extends('layouts.dashboard')

@section('dashboard-content')
@php
    $filter      = $filter      ?? 'semua';
    $aduans      = $aduans      ?? collect();
    $belumDibaca = $belumDibaca ?? 0;
    $sudahDibaca = $sudahDibaca ?? 0;
@endphp
<div x-data="{ filter: '{{ $filter }}' }" class="pb-12 text-gray-800">

    {{-- Header Content --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-10" data-aos="fade-up">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-[10px] font-black uppercase tracking-widest mb-4 border border-[#36B2B2]/20">
                <span class="relative flex h-2 w-2">
                    @if($belumDibaca > 0)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    @else
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#36B2B2]"></span>
                    @endif
                </span>
                Pusat Bantuan & Layanan
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 leading-[1.1] tracking-tight mb-4">
                Aduan & <span class="text-[#36B2B2]">Permintaan</span>
            </h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium leading-relaxed">
                Kelola keluhan kerusakan fasilitas dan permintaan penambahan layanan dari penyewa kos Anda secara terpusat.
            </p>
        </div>

        {{-- Main Stats Dashboard --}}
        <div class="flex items-center gap-4 bg-white p-2 rounded-[2rem] border border-gray-100 shadow-sm">
            <div class="bg-rose-50 rounded-2xl px-6 py-4 text-center min-w-[120px] border border-rose-100/50">
                <p class="text-3xl font-black text-rose-600 leading-none mb-1">{{ $belumDibaca }}</p>
                <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Belum Dibaca</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl px-6 py-4 text-center min-w-[120px] border border-emerald-100/50">
                <p class="text-3xl font-black text-emerald-600 leading-none mb-1">{{ $sudahDibaca }}</p>
                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Sudah Selesai</p>
            </div>
        </div>
    </div>

    {{-- Summary Cards (Aduan & Ajuan) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12" data-aos="fade-up">
        <!-- Card Aduan -->
        <a href="{{ route('admin.pesan_aduan', ['kategori' => $kategori === 'fasilitas' ? 'semua' : 'fasilitas', 'filter' => $filter]) }}"
            class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 border-2 transition-all duration-500 group
            {{ $kategori === 'fasilitas' ? 'border-rose-500 bg-rose-50 shadow-xl shadow-rose-500/10' : 'border-rose-50 shadow-sm hover:shadow-2xl hover:-translate-y-1' }}">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-rose-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative flex items-center gap-8">
                <div class="w-20 h-20 bg-rose-50 rounded-[1.5rem] flex items-center justify-center text-rose-500 group-hover:rotate-12 transition-transform duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-[11px] font-black text-rose-400 uppercase tracking-[0.25em] mb-2">Aduan Fasilitas</h4>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-gray-900 leading-none">{{ $countAduan ?? 0 }}</h3>
                        <span class="text-xs font-bold text-gray-400">Kasus Masuk</span>
                    </div>
                </div>
            </div>
            @if($kategori === 'fasilitas')
                <div class="absolute bottom-4 right-8 flex items-center gap-2 text-rose-500 text-[9px] font-black uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span>
                    Filter Aktif
                </div>
            @endif
        </a>

        <!-- Card Ajuan -->
        <a href="{{ route('admin.pesan_aduan', ['kategori' => $kategori === 'tambah' ? 'semua' : 'tambah', 'filter' => $filter]) }}"
            class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 border-2 transition-all duration-500 group
            {{ $kategori === 'tambah' ? 'border-[#36B2B2] bg-[#36B2B2]/5 shadow-xl shadow-[#36B2B2]/10' : 'border-[#36B2B2]/5 shadow-sm hover:shadow-2xl hover:-translate-y-1' }}">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-[#36B2B2]/5 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative flex items-center gap-8">
                <div class="w-20 h-20 bg-[#36B2B2]/5 rounded-[1.5rem] flex items-center justify-center text-[#36B2B2] group-hover:rotate-12 transition-transform duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-[11px] font-black text-[#36B2B2]/60 uppercase tracking-[0.25em] mb-2">Ajuan Fasilitas</h4>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-gray-900 leading-none">{{ $countTambah ?? 0 }}</h3>
                        <span class="text-xs font-bold text-gray-400">Permintaan Baru</span>
                    </div>
                </div>
            </div>
            @if($kategori === 'tambah')
                <div class="absolute bottom-4 right-8 flex items-center gap-2 text-[#36B2B2] text-[9px] font-black uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 bg-[#36B2B2] rounded-full animate-pulse"></span>
                    Filter Aktif
                </div>
            @endif
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8" data-aos="fade-up">
        <div class="flex bg-gray-100/80 backdrop-blur-md p-1.5 rounded-2xl w-fit border border-gray-200/50 shadow-inner">
            @foreach([
                ['val' => 'semua',        'label' => 'Semua',        'active' => 'bg-white text-gray-900 shadow-sm border border-gray-100'],
                ['val' => 'belum_dibaca', 'label' => 'Belum Dibaca', 'active' => 'bg-rose-500 text-white shadow-lg shadow-rose-200'],
                ['val' => 'sudah_dibaca', 'label' => 'Sudah Dibaca', 'active' => 'bg-[#36B2B2] text-white shadow-lg shadow-[#36B2B2]/20'],
            ] as $tab)
                <a href="{{ route('admin.pesan_aduan', ['filter' => $tab['val']]) }}"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300
                    {{ $filter === $tab['val'] ? $tab['active'] : 'text-gray-500 hover:text-gray-800' }}">
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
            Menampilkan <span class="text-gray-900">{{ $aduans->count() }}</span> dari <span class="text-gray-900">{{ $aduans->total() ?? $aduans->count() }}</span> Data
        </div>
    </div>

    {{-- Aduan Cards --}}
    @if($aduans->isEmpty())
        <div class="bg-white rounded-[3rem] p-24 text-center border-2 border-dashed border-gray-100 flex flex-col items-center justify-center" data-aos="fade-up">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-8 shadow-inner">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">
                {{ $filter === 'belum_dibaca' ? 'Inbox Bersih!' : ($filter === 'sudah_dibaca' ? 'Belum Ada Riwayat' : 'Kosong') }}
            </h3>
            <p class="text-gray-500 max-w-sm mx-auto font-medium">
                {{ $filter !== 'semua' ? 'Coba ganti filter untuk melihat data lainnya.' : 'Semua keluhan telah ditangani. Operasional kos Anda berjalan dengan sempurna.' }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6" data-aos="fade-up">
            @foreach($aduans as $index => $aduan)
                <div class="group relative bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 overflow-hidden" 
                    data-aos="fade-up" data-aos-delay="{{ min($index * 50, 400) }}">
                    
                    {{-- Status Indicator Bar --}}
                    <div class="absolute inset-y-0 left-0 w-1.5 {{ $aduan->status === 'belum_dibaca' ? 'bg-rose-500' : 'bg-gray-200' }}"></div>

                    <div class="p-8 sm:p-10">
                        <div class="flex flex-col lg:flex-row gap-8">
                            
                            {{-- User Info Section --}}
                            <div class="flex flex-row lg:flex-col items-center lg:items-center gap-4 lg:w-32 shrink-0">
                                <div class="relative">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner
                                        {{ $aduan->status === 'belum_dibaca' ? 'bg-gradient-to-br from-rose-50 to-rose-100 text-rose-600' : 'bg-gray-50 text-gray-400' }}">
                                        {{ strtoupper(substr($aduan->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    @if($aduan->status === 'belum_dibaca')
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 border-2 border-white rounded-full"></div>
                                    @endif
                                </div>
                                <div class="text-left lg:text-center min-w-0 flex flex-col items-start lg:items-center gap-1">
                                    <p class="font-black text-gray-900 text-sm truncate w-full">{{ explode(' ', $aduan->user->name ?? 'User')[0] }}</p>
                                    
                                    <div class="flex flex-col gap-1 w-full lg:items-center">
                                        {{-- WhatsApp --}}
                                        @if($aduan->user->nomor_wa)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $aduan->user->nomor_wa) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-md text-[9px] font-bold hover:bg-emerald-100 transition-colors">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                                Chat WA
                                            </a>
                                        @endif

                                        {{-- Room --}}
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 rounded-md text-[9px] font-bold">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            Kamar {{ $aduan->user->kamar->nomor_kamar ?? '-' }}
                                        </div>
                                    </div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mt-1">{{ $aduan->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            {{-- Message Content Section --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                    <h3 class="font-black text-gray-900 text-lg leading-tight">{{ $aduan->judul }}</h3>
                                    
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 text-[9px] font-black uppercase tracking-[0.15em] rounded-full border
                                            {{ $aduan->kategori === 'tambah' ? 'bg-[#36B2B2]/5 text-[#36B2B2] border-[#36B2B2]/10' : 'bg-rose-50 text-rose-500 border-rose-100' }}">
                                            {{ $aduan->kategori === 'tambah' ? 'Ajuan Fasilitas' : 'Aduan Fasilitas' }}
                                        </span>
                                        @if($aduan->status === 'sudah_dibaca')
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-[0.15em] rounded-full border border-emerald-100/50">Selesai</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="relative mb-6">
                                    <div class="absolute -left-3 top-0 bottom-0 w-1 bg-gray-100 rounded-full"></div>
                                    <p class="text-gray-600 font-medium leading-[1.6] pl-4 italic">
                                        "{{ $aduan->pesan }}"
                                    </p>
                                </div>

                                {{-- Meta Info --}}
                                <div class="flex items-center gap-6 mb-8 text-[11px] font-bold text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $aduan->created_at->format('d M Y, H:i') }}
                                    </div>
                                    @if($aduan->dibaca_at)
                                        <div class="flex items-center gap-1.5 text-emerald-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Ditanggapi {{ $aduan->dibaca_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Footer Actions --}}
                                <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-gray-50">
                                    <div class="flex items-center gap-3">
                                        @if($aduan->status === 'belum_dibaca')
                                            <form action="{{ route('admin.aduan.read', $aduan->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="group/btn flex items-center gap-2.5 px-6 py-3 bg-gray-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-[#36B2B2] transition-all duration-300 shadow-xl shadow-gray-200">
                                                    <svg class="w-4 h-4 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Tandai Selesai
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.aduan.unread', $aduan->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="flex items-center gap-2.5 px-6 py-3 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    Buka Kembali
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <form action="{{ route('admin.aduan.destroy', $aduan->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus pesan ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center gap-2 px-4 py-3 text-rose-500 hover:text-rose-600 text-[10px] font-black uppercase tracking-widest transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10 flex justify-center">
            {{ $aduans->links() }}
        </div>
    @endif

    {{-- Success / Error Flash --}}
    @if(session('success'))
        <div class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl shadow-emerald-500/30 font-black text-sm flex items-center gap-3"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

</div>
@endsection

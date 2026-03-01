@extends('layouts.dashboard')

@section('dashboard-content')
    @php
        $user = auth()->user();
        // Get the first kos for this user
        $kos = $user->kos()->with('kamars')->first();

        $minHarga = $kos ? ($kos->kamars->min('harga') ?? 0) : 0;
        $maxHarga = $kos ? ($kos->kamars->max('harga') ?? 0) : 0;

        $totalKamar = $kos ? $kos->kamars->count() : 0;

        // Count unique users with active status linked to this kos
        $totalPenyewa = 0;
        if ($kos) {
            $totalPenyewa = \App\Models\User::where('id_kos', $kos->id)
                ->where('status', 'active')
                ->count();
        }

        // Logic for Room Quota (Per Kamar Plans)
        $activeSubscription = $user->langganans()->where('status', 'active')->latest()->first();
        $isPerKamar = in_array($user->id_plans, [4, 5]);
        $limitKamar = ($isPerKamar && $activeSubscription) ? $activeSubscription->jumlah_kamar : 0;
        $sisaKuota = $limitKamar - $totalKamar;
    @endphp

    <div x-data="{ 
                showEditModal: {{ $errors->any() ? 'true' : 'false' }}, 
                editField: '{{ old('editField', '') }}',
                formData: {
                    nama_kos: '{{ old('nama_kos', $kos->nama_kos ?? '') }}',
                    alamat: '{{ old('alamat', $kos->alamat ?? '') }}',
                    kode_kos: '{{ old('kode_kos', $kos->kode_kos ?? '') }}',
                    kategori: '{{ old('kategori', $kos->kategori ?? 'putra') }}',
                    harga_batch: '{{ old('harga_batch', $minHarga) }}'
                }
            }">
        <!-- Section Title -->
        <div class="flex items-center justify-between mb-8" data-aos="fade-up">
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight Level 1">Statistik & Detail Properti
                </h2>
                <p class="text-gray-500 text-sm mt-1 font-medium">Pantau data hunian dan kelola informasi properti Anda
                    secara digital.</p>
            </div>
            @if(session('success'))
                <div
                    class="px-4 py-2 bg-[#36B2B2]/10 text-[#36B2B2] rounded-xl text-xs font-bold border border-[#36B2B2]/20 animate-bounce">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        @if(!$kos)
            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-12 text-center shadow-sm" data-aos="fade-up">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 tracking-tight">Belum Ada Data Kos</h3>
                <p class="text-gray-500 mb-8">Data properti Anda akan muncul di sini setelah ditambahkan.</p>
                <a href="{{ route('admin.cabang_kos') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-[#36B2B2] text-white font-black rounded-2xl hover:bg-[#2b8f8f] transition-all shadow-lg shadow-[#36B2B2]/20">
                    <span>Tambah Kos Sekarang</span>
                </a>
            </div>
        @else
            <!-- Information Grid: 6 Cards (Multi-Variant Design) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">

                <!-- Card 1: Nama Kos (Variant: Teal) -->
                <div class="relative overflow-hidden bg-white border-2 border-gray-100 rounded-[2.5rem] p-6 transition-all duration-500 hover:border-[#36B2B2]/30 hover:shadow-xl hover:shadow-[#36B2B2]/5 hover:-translate-y-2 group"
                    data-aos="fade-up" data-aos-delay="100">
                    <button @click="showEditModal = true; editField = 'nama_kos'"
                        class="absolute top-6 left-6 p-2 bg-gray-50 rounded-xl text-gray-400 group-hover:bg-[#36B2B2] group-hover:text-white transition-all shadow-sm border border-gray-100 group-hover:border-[#36B2B2] z-20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </button>

                    <div class="relative z-10 pl-10">
                        <div
                            class="w-10 h-10 mb-4 bg-[#36B2B2]/5 rounded-xl flex items-center justify-center text-[#36B2B2] border border-[#36B2B2]/10 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[10px] text-[#36B2B2] font-black uppercase tracking-[0.2em] mb-1 opacity-80">Nama
                            Properti</p>
                        <h3
                            class="text-base font-black text-gray-900 leading-[1.3] line-clamp-2 tracking-tight transition-colors group-hover:text-[#36B2B2]">
                            {{ $kos->nama_kos }}
                        </h3>
                    </div>
                </div>

                <!-- Card 2: Lokasi Kos (Variant: Indigo) -->
                <div class="relative overflow-hidden bg-white border-2 border-gray-100 rounded-[2.5rem] p-6 transition-all duration-500 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-500/5 hover:-translate-y-2 group"
                    data-aos="fade-up" data-aos-delay="200">
                    <button @click="showEditModal = true; editField = 'alamat'"
                        class="absolute top-6 left-6 p-2 bg-gray-50 rounded-xl text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm border border-gray-100 group-hover:border-indigo-600 z-20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </button>

                    <div class="relative z-10 pl-10">
                        <div
                            class="w-10 h-10 mb-4 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 border border-indigo-100 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[10px] text-indigo-600 font-black uppercase tracking-[0.2em] mb-1 opacity-80">Lokasi
                            Properti</p>
                        <p
                            class="text-[11px] text-gray-500 leading-relaxed line-clamp-2 italic font-medium group-hover:text-gray-700">
                            {{ $kos->alamat ?? 'Lokasi belum dilengkapi' }}
                        </p>
                    </div>
                </div>

                <!-- Card 3: Rentan Harga (Variant: Amber) -->
                <div class="relative overflow-hidden bg-white border-2 border-gray-100 rounded-[2.5rem] p-6 transition-all duration-500 hover:border-amber-200 hover:shadow-xl hover:shadow-amber-500/5 hover:-translate-y-2 group"
                    data-aos="fade-up" data-aos-delay="300">
                    <div class="relative z-10">
                        <div
                            class="w-10 h-10 mb-4 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 border border-amber-100 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[10px] text-amber-600 font-black uppercase tracking-[0.2em] mb-1 opacity-80">Rentan Harga
                        </p>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-[9px] font-black font-mono text-gray-400 uppercase">Rp</span>
                            <span
                                class="text-lg font-black tracking-tight text-gray-900 group-hover:text-amber-600 transition-colors">
                                @if($minHarga > 0)
                                    {{ number_format($minHarga / 1000, 0, ',', '.') }}k-{{ number_format($maxHarga / 1000, 0, ',', '.') }}k
                                @else
                                    <span class="text-gray-300 italic text-xs">N/A</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Kode Kos (Variant: Rose) -->
                <div class="relative overflow-hidden bg-white border-2 border-gray-100 rounded-[2.5rem] p-6 transition-all duration-500 hover:border-rose-200 hover:shadow-xl hover:shadow-rose-500/5 hover:-translate-y-2 group"
                    data-aos="fade-up" data-aos-delay="400">
                    @if(!$kos->is_kode_kos_edited)
                        <button @click="showEditModal = true; editField = 'kode_kos'"
                            class="absolute top-6 left-6 p-2 bg-gray-50 rounded-xl text-gray-400 group-hover:bg-rose-500 group-hover:text-white transition-all shadow-sm border border-gray-100 group-hover:border-rose-500 z-20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                </path>
                            </svg>
                        </button>
                    @endif

                    <div class="relative z-10 pl-10">
                        <div
                            class="w-10 h-10 mb-4 bg-rose-50 rounded-xl flex items-center justify-center text-rose-500 border border-rose-100 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[10px] text-rose-500 font-black uppercase tracking-[0.2em] mb-1 opacity-80">Kode Properti
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black text-gray-900 tracking-wider font-mono">#{{ $kos->kode_kos }}</span>
                            @if($kos->is_kode_kos_edited)
                                <span
                                    class="px-1.5 py-0.5 bg-gray-50 text-[7px] text-gray-400 border border-gray-100 rounded-md font-bold uppercase">Lock</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card 5: Total Kamar (Variant: Violet) -->
                <div class="relative overflow-hidden bg-white border-2 border-gray-100 rounded-[2.5rem] p-6 transition-all duration-500 hover:border-violet-200 hover:shadow-xl hover:shadow-violet-500/5 hover:-translate-y-2 group"
                    data-aos="fade-up" data-aos-delay="500">
                    <div class="relative z-10">
                        <div
                            class="w-10 h-10 mb-4 bg-violet-50 rounded-xl flex items-center justify-center text-violet-600 border border-violet-100 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[10px] text-violet-600 font-black uppercase tracking-[0.2em] mb-1 opacity-80">Total Kamar
                        </p>
                        <div class="flex items-baseline gap-1.5">
                            <span
                                class="text-3xl font-black text-gray-900 group-hover:text-violet-600 transition-colors">{{ $totalKamar }}</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Unit</span>
                        </div>

                        @if($isPerKamar)
                            <div class="mt-2 pt-2 border-t border-gray-50">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Sisa Kuota</span>
                                    <span
                                        class="px-2 py-0.5 bg-violet-50 text-violet-600 text-[10px] font-black rounded-lg border border-violet-100">
                                        {{ $sisaKuota }} / {{ $limitKamar }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card 6: Total Penyewa (Variant: Emerald) -->
                <div class="relative overflow-hidden bg-white border-2 border-gray-100 rounded-[2.5rem] p-6 transition-all duration-500 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-500/5 hover:-translate-y-2 group"
                    data-aos="fade-up" data-aos-delay="600">
                    <div class="relative z-10">
                        <div
                            class="w-10 h-10 mb-4 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[10px] text-emerald-600 font-black uppercase tracking-[0.2em] mb-1 opacity-80">Total
                            Penyewa</p>
                        <div class="flex items-baseline gap-1.5">
                            <span
                                class="text-3xl font-black text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $totalPenyewa }}</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jiwa</span>
                        </div>
                    </div>
                </div>

                <!-- Card 7: Kategori (Variant: Teal) -->
                <div class="relative overflow-hidden bg-white border-2 border-gray-100 rounded-[2.5rem] p-6 transition-all duration-500 hover:border-teal-200 hover:shadow-xl hover:shadow-teal-500/5 hover:-translate-y-2 group"
                    data-aos="fade-up" data-aos-delay="700">
                    <button @click="showEditModal = true; editField = 'kategori'"
                        class="absolute top-6 left-6 p-2 bg-gray-50 rounded-xl text-gray-400 group-hover:bg-teal-500 group-hover:text-white transition-all shadow-sm border border-gray-100 group-hover:border-teal-500 z-20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </button>

                    <div class="relative z-10 pl-10">
                        <div
                            class="w-10 h-10 mb-4 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600 border border-teal-100 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p class="text-[10px] text-teal-600 font-black uppercase tracking-[0.2em] mb-1 opacity-80">Kategori Kos
                        </p>
                        <span
                            class="px-3 py-1 bg-teal-50 text-teal-600 text-[10px] font-black rounded-lg border border-teal-100 uppercase tracking-widest">
                            {{ ucfirst($kos->kategori ?? 'campur') }}
                        </span>
                    </div>
                </div>

            </div>

            <!-- Edit Modal (Top Layer - Ensured Solid) -->
            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                style="display: none;" @keydown.escape.window="showEditModal = false">

                <div @click.away="showEditModal = false" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white rounded-[3rem] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.3)] w-full max-w-md overflow-hidden relative border-none opacity-100">

                    <div class="p-10">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Perbarui Data</h3>
                                <div class="h-1 w-12 bg-[#36B2B2] mt-2 rounded-full"></div>
                            </div>
                            <button @click="showEditModal = false" class="text-gray-300 hover:text-red-500 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('admin.kos.update', $kos->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="editField" :value="editField">

                            <div class="space-y-6">
                                <div x-show="editField === 'nama_kos'">
                                    <label
                                        class="block text-[11px] font-black uppercase tracking-widest text-[#36B2B2] mb-3 ml-1">Nama
                                        Properti</label>
                                    <input type="text" name="nama_kos" x-model="formData.nama_kos"
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-[#36B2B2]/10 focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-800 text-lg">
                                </div>

                                <div x-show="editField === 'alamat'">
                                    <label
                                        class="block text-[11px] font-black uppercase tracking-widest text-indigo-600 mb-3 ml-1">Lokasi
                                        Lengkap</label>
                                    <textarea name="alamat" x-model="formData.alamat" rows="3"
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 outline-none transition-all font-medium text-gray-600 italic leading-relaxed"></textarea>
                                </div>

                                <div x-show="editField === 'kode_kos' && !@json($kos->is_kode_kos_edited)">
                                    <label
                                        class="block text-[11px] font-black uppercase tracking-widest text-rose-500 mb-3 ml-1">Kode
                                        Identitas (Max 4 Angka)</label>
                                    <input type="number" name="kode_kos" x-model="formData.kode_kos" max="9999"
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-rose-100 focus:border-rose-500 outline-none transition-all font-mono font-black text-2xl text-gray-800 tracking-widest">
                                    <p class="mt-2 text-[10px] text-amber-600 font-bold italic">*Perhatian: Maksimal 4 angka dan
                                        hanya bisa diubah 1x.</p>
                                </div>

                                <div x-show="editField === 'kategori'">
                                    <label
                                        class="block text-[11px] font-black uppercase tracking-widest text-teal-600 mb-3 ml-1">Kategori
                                        Kos</label>
                                    <select name="kategori" x-model="formData.kategori"
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-teal-100 focus:border-teal-600 outline-none transition-all font-bold text-gray-800">
                                        <option value="putra">Putra</option>
                                        <option value="putri">Putri</option>
                                        <option value="campur">Campur</option>
                                    </select>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-2xl">
                                    <ul class="list-disc list-inside text-xs text-red-600 font-bold space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mt-10 flex gap-4">
                                <button type="button" @click="showEditModal = false"
                                    class="flex-1 px-8 py-5 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition-all border border-gray-100">Batal</button>
                                <button type="submit"
                                    class="flex-[1.5] px-8 py-5 bg-[#36B2B2] text-white font-black rounded-2xl hover:bg-[#2b8f8f] hover:translate-y-[-2px] active:translate-y-0 transition-all shadow-xl shadow-[#36B2B2]/20">Simpan
                                    Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
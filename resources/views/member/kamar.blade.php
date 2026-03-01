@extends('layouts.dashboard')

@section('dashboard-content')
    <div x-data="{ 
                        showAddModal: false, 
                        showEditModal: false,
                        showFasilitasModal: false,
                        activeKamar: null,
                        search: '',
                        filterStatus: 'all',
                        formData: {
                            nomor_kamar: '',
                            harga: '',
                            fasilitas: ['']
                        },
                        editFormData: {
                            nomor_kamar: '',
                            harga: ''
                        },
                        fasilitasData: {
                            items: []
                        },
                        formatCurrency(value) {
                            if (!value) return '';
                            let val = value.toString().replace(/\D/g, '');
                            return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        },
                        updateHarga(e, target) {
                            let rawValue = e.target.value.replace(/\D/g, '');
                            this[target].harga = this.formatCurrency(rawValue);
                        },
                        openEditModal(kamar) {
                            this.activeKamar = kamar;
                            this.editFormData = {
                                nomor_kamar: kamar.nomor_kamar,
                                harga: this.formatCurrency(Math.round(kamar.harga))
                            };
                            this.showEditModal = true;
                        },
                        openFasilitasModal(kamar) {
                            this.activeKamar = kamar;
                            this.fasilitasData.items = kamar.fasilitas.length > 0 
                                ? kamar.fasilitas.map(f => f.nama_fasilitas) 
                                : [''];
                            this.showFasilitasModal = true;
                        },
                        addFasilitasRow() {
                            this.fasilitasData.items.push('');
                        },
                        removeFasilitasRow(index) {
                            this.fasilitasData.items.splice(index, 1);
                            if (this.fasilitasData.items.length === 0) {
                                this.fasilitasData.items.push('');
                            }
                        },
                        addFasilitasRowNew() {
                            this.formData.fasilitas.push('');
                        },
                        removeFasilitasRowNew(index) {
                            this.formData.fasilitas.splice(index, 1);
                            if (this.formData.fasilitas.length === 0) {
                                this.formData.fasilitas.push('');
                            }
                        },
                        currentPage: 1,
                        itemsPerPage: 10,
                        get filteredKamars() {
                            if (!window.existingKamars) return [];
                            return window.existingKamars.filter(kamar => {
                                const matchSearch = this.search === '' || 
                                                   kamar.nomor_kamar.toLowerCase().includes(this.search.toLowerCase());
                                const matchStatus = this.filterStatus === 'all' || kamar.status === this.filterStatus;

                                return matchSearch && matchStatus;
                            });
                        },
                        get pagedKamars() {
                            const start = (this.currentPage - 1) * this.itemsPerPage;
                            return this.filteredKamars.slice(start, start + this.itemsPerPage);
                        },
                        get totalPages() {
                            return Math.max(1, Math.ceil(this.filteredKamars.length / this.itemsPerPage));
                        },
                        get isNomorKamarDuplicate() {
                            if (!this.formData.nomor_kamar) return false;
                            return window.existingKamars.some(k => k.nomor_kamar.toLowerCase() === this.formData.nomor_kamar.toLowerCase());
                        }
                    }" x-init="$watch('search', () => currentPage = 1); $watch('filterStatus', () => currentPage = 1);"
        class="pb-12 text-gray-800">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10" data-aos="fade-up">
            <div class="flex-1">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-[10px] font-black uppercase tracking-widest mb-4 border border-[#36B2B2]/20">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#36B2B2] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#36B2B2]"></span>
                    </span>
                    Manajemen Aset
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 leading-none tracking-tight Level 1">
                    Kamar & Unit <span class="text-[#36B2B2]">Inventaris</span>
                </h1>
                <p class="text-gray-500 mt-3 font-medium max-w-xl line-clamp-2">
                    Kelola ketersediaan, harga, dan fasilitas hunian kamar kos Anda dalam satu dasbor terpadu.
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if($isPerKamar)
                    <div class="hidden sm:flex flex-col items-end mr-4">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Kuota
                            Kamar</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-xl font-black text-gray-900 leading-none">{{ $kamars->count() }}</span>
                            <span class="text-xs font-bold text-gray-400">/ {{ $limitKamar }}</span>
                        </div>
                    </div>
                @endif
                <button @click="showAddModal = true"
                    class="flex items-center gap-3 px-8 py-4 bg-[#36B2B2] text-white font-black rounded-2xl hover:bg-[#2b8f8f] transition-all shadow-xl shadow-[#36B2B2]/20 group">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Kamar</span>
                </button>
            </div>
        </div>

        <!-- System Alerts -->
        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500"
                data-aos="fade-up">
                <div
                    class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-black text-emerald-900 text-sm">Berhasil!</p>
                    <p class="text-emerald-600 text-xs font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-4" data-aos="fade-up">
                <div
                    class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-rose-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="font-black text-rose-900 text-sm">Oops! Terjadi Kesalahan</p>
                    <p class="text-rose-600 text-xs font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-4" data-aos="fade-up">
                <div
                    class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-rose-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="font-black text-rose-900 text-sm">Validasi Gagal</p>
                    <ul class="list-disc list-inside text-rose-600 text-xs font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- List Content -->
        @if($kamars->isEmpty())
            <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-gray-100" data-aos="fade-up">
                <div
                    class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-8 border border-gray-100 group">
                    <svg class="w-12 h-12 text-gray-300 group-hover:text-[#36B2B2] transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Kamar Masih Kosong</h3>
                <p class="text-gray-500 mb-10 max-w-sm mx-auto font-medium">Anda belum mendaftarkan unit kamar apapun. Klik
                    tombol di bawah untuk mulai mengelola kos Anda.</p>
                <button @click="showAddModal = true"
                    class="px-10 py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-gray-800 transition-all shadow-xl shadow-gray-200">
                    Input Kamar Pertama
                </button>
            </div>
        @else
            <!-- Filters & Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8" data-aos="fade-up">
                <div class="lg:col-span-2 flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1">
                        <input type="text" x-model="search" placeholder="Cari nomor kamar (Contoh: 101, A1)..."
                            class="w-full px-8 py-5 bg-white border-2 border-gray-100 rounded-3xl focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-700 shadow-sm shadow-gray-100/50">
                    </div>

                    <!-- Status Filter Chips -->
                    <div class="flex bg-gray-100/50 p-1.5 rounded-3xl items-center border border-gray-100">
                        <button @click="filterStatus = 'all'"
                            :class="filterStatus === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">Semua</button>
                        <button @click="filterStatus = 'tersedia'"
                            :class="filterStatus === 'tersedia' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-emerald-600'"
                            class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">Kosong</button>
                        <button @click="filterStatus = 'disewa'"
                            :class="filterStatus === 'disewa' ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/20' : 'text-gray-400 hover:text-rose-600'"
                            class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">Disewa</button>
                    </div>
                </div>
                <div @click="filterStatus = 'tersedia'"
                    class="bg-emerald-50 border border-emerald-100 rounded-3xl p-5 flex items-center justify-between cursor-pointer hover:scale-[1.02] transition-transform">
                    <div>
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Kosong</p>
                        <p class="text-2xl font-black text-emerald-900 leading-none">
                            {{ $kamars->where('status', 'tersedia')->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div @click="filterStatus = 'terisi'"
                    class="bg-rose-50 border border-rose-100 rounded-3xl p-5 flex items-center justify-between cursor-pointer hover:scale-[1.02] transition-transform">
                    <div>
                        <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Terisi</p>
                        <p class="text-2xl font-black text-rose-900 leading-none">
                            {{ $kamars->where('status', 'disewa')->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-rose-500/10 rounded-xl flex items-center justify-center text-rose-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white border-2 border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm" data-aos="fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">No. Kamar
                                </th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Harga /
                                    Bulan</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Fasilitas
                                </th>
                                <th
                                    class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Status</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(kamar, index) in pagedKamars" :key="kamar.id">
                                <tr class="hover:bg-gray-50/80 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-500 font-black group-hover:bg-[#36B2B2] group-hover:text-white transition-all duration-300 shrink-0"
                                                x-text="kamar.nomor_kamar">
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900" x-text="'Unit ' + kamar.nomor_kamar"></p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight"
                                                    x-text="'ID: #UNIT-0' + (index + 1 + (currentPage - 1) * itemsPerPage)"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-[10px] font-black text-gray-400">Rp</span>
                                            <span class="text-lg font-black text-gray-900 tracking-tight"
                                                x-text="formatCurrency(kamar.harga)"></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 max-w-[200px]">
                                        <div @click="openFasilitasModal(kamar)"
                                            class="cursor-pointer group/fas hover:bg-white p-2 rounded-xl transition-all border border-transparent hover:border-gray-100">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="fas in kamar.fasilitas" :key="fas.id">
                                                    <span
                                                        class="px-2 py-0.5 bg-gray-50 text-[9px] font-bold text-gray-500 rounded-md border border-gray-100"
                                                        x-text="fas.nama_fasilitas"></span>
                                                </template>
                                                <template x-if="!kamar.fasilitas || kamar.fasilitas.length === 0">
                                                    <span class="text-[10px] text-gray-300 italic">Belum ada fasilitas...</span>
                                                </template>
                                            </div>
                                            <div
                                                class="mt-1 opacity-0 group-hover/fas:opacity-100 transition-opacity flex items-center gap-1">
                                                <span
                                                    class="text-[8px] font-black text-[#36B2B2] uppercase tracking-tighter">Klik
                                                    untuk edit</span>
                                                <svg class="w-2 h-2 text-[#36B2B2]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                                        stroke-width="3"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex justify-center">
                                            <template x-if="kamar.status === 'tersedia'">
                                                <span
                                                    class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">Kosong</span>
                                            </template>
                                            <template x-if="kamar.status === 'disewa'">
                                                <span
                                                    class="px-4 py-1.5 bg-rose-50 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-100">Disewa</span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="openEditModal(kamar)"
                                                class="p-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100/50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <form :action="`/admin/kamar/${kamar.id}`" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kamar ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-3 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all border border-rose-100/50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION CONTROLS -->
                <div
                    class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs font-bold text-gray-400 tracking-tight uppercase">
                        Menampilkan <span class="text-gray-900" x-text="pagedKamars.length"></span> dari <span
                            class="text-gray-900" x-text="filteredKamars.length"></span> unit
                    </p>
                    <div class="flex items-center gap-2">
                        <button @click="currentPage--" :disabled="currentPage === 1"
                            class="p-2 border-2 border-gray-100 rounded-xl hover:border-[#36B2B2] disabled:opacity-30 disabled:hover:border-gray-100 transition-all text-[#36B2B2]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>

                        <div class="flex items-center gap-1">
                            <template x-for="page in totalPages" :key="page">
                                <button @click="currentPage = page"
                                    :class="currentPage === page ? 'bg-[#36B2B2] text-white border-[#36B2B2] shadow-lg shadow-[#36B2B2]/20' : 'bg-white text-gray-400 border-gray-100 hover:border-[#36B2B2] hover:text-[#36B2B2]'"
                                    class="w-10 h-10 border-2 rounded-xl text-xs font-black transition-all"
                                    x-text="page"></button>
                            </template>
                        </div>

                        <button @click="currentPage++" :disabled="currentPage === totalPages"
                            class="p-2 border-2 border-gray-100 rounded-xl hover:border-[#36B2B2] disabled:opacity-30 disabled:hover:border-gray-100 transition-all text-[#36B2B2]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL: Tambah Kamar -->
        <div x-show="showAddModal"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md"
            style="display: none;">
            <div @click.away="showAddModal = false"
                class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-in zoom-in duration-300 border border-white">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight Level 2">Tambah Data Kamar
                            </h3>
                            <div class="h-1.5 w-16 bg-[#36B2B2] mt-3 rounded-full"></div>
                        </div>
                        <button @click="showAddModal = false" class="text-gray-300 hover:text-rose-500 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.kamar.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label
                                class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-1">Nomor
                                Kamar</label>
                            <input type="text" name="nomor_kamar" x-model="formData.nomor_kamar" required
                                placeholder="Contoh: 101, A1, VIP"
                                :class="isNomorKamarDuplicate ? 'border-rose-500 bg-rose-50' : 'border-gray-100 bg-gray-50'"
                                class="w-full px-6 py-4 border-2 rounded-2xl focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-800">
                            <template x-if="isNomorKamarDuplicate">
                                <p
                                    class="text-[10px] font-black text-rose-500 mt-2 ml-1 uppercase tracking-widest animate-in fade-in slide-in-from-top-1">
                                    Nomor kamar sudah ada!</p>
                            </template>
                        </div>

                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Harga
                            Sewa / Bulan</label>
                        <div class="relative">
                            <input type="text" name="harga" x-model="formData.harga"
                                @input="updateHarga($event, 'formData')" required placeholder="Misal: 800.000"
                                class="w-full px-6 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[#36B2B2] outline-none transition-all font-black text-gray-800 text-base">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3 ml-1">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400">Daftar
                                    Fasilitas</label>
                                <button type="button" @click="addFasilitasRowNew"
                                    class="text-[10px] font-black text-[#36B2B2] uppercase tracking-widest hover:underline">+
                                    Tambah</button>
                            </div>
                            <div class="space-y-3 max-h-[160px] overflow-y-auto pr-2 custom-scrollbar">
                                <template x-for="(item, index) in formData.fasilitas" :key="index">
                                    <div class="flex items-center gap-2 animate-in slide-in-from-left-2 duration-200">
                                        <div class="relative flex-1">
                                            <input type="text" name="fasilitas[]" x-model="formData.fasilitas[index]"
                                                placeholder="Misal: AC"
                                                class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-700 text-sm">
                                        </div>
                                        <button type="button" @click="removeFasilitasRowNew(index)"
                                            class="p-3 text-gray-300 hover:text-rose-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="pt-2 flex gap-3">
                            <button type="button" @click="showAddModal = false"
                                class="flex-1 px-6 py-4 bg-gray-50 text-gray-500 font-bold rounded-xl hover:bg-gray-100 transition-all border border-gray-100">Batal</button>
                            <button type="submit" :disabled="isNomorKamarDuplicate"
                                :class="isNomorKamarDuplicate ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-[#36B2B2] text-white hover:bg-[#2b8f8f] hover:-translate-y-1 shadow-xl shadow-[#36B2B2]/20 shadow-[#36B2B2]/20'"
                                class="flex-[1.5] px-6 py-4 font-black rounded-xl transition-all text-sm">Daftarkan
                                Kamar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Edit Kamar -->
        <div x-show="showEditModal"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md"
            style="display: none;">
            <div @click.away="showEditModal = false"
                class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-in zoom-in duration-300 border border-white">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight Level 2">Ubah Data Kamar
                            </h3>
                            <div class="h-1.5 w-16 bg-indigo-600 mt-3 rounded-full"></div>
                        </div>
                        <button @click="showEditModal = false" class="text-gray-300 hover:text-rose-500 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form :action="activeKamar ? `/admin/kamar/${activeKamar.id}` : '#'" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div>
                            <label
                                class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-1">Nomor
                                Kamar</label>
                            <input type="text" name="nomor_kamar" x-model="editFormData.nomor_kamar" required
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-600 outline-none transition-all font-bold text-gray-800">
                        </div>

                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-1">Harga
                            Sewa / Bulan</label>
                        <div class="relative">
                            <input type="text" name="harga" x-model="editFormData.harga"
                                @input="updateHarga($event, 'editFormData')" required
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-indigo-600 outline-none transition-all font-black text-gray-800 text-lg">
                        </div>

                        <div class="pt-2 flex gap-3">
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-6 py-4 bg-gray-50 text-gray-500 font-bold rounded-xl hover:bg-gray-100 transition-all border border-gray-100">Batal</button>
                            <button type="submit"
                                class="flex-[1.5] px-6 py-4 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 hover:-translate-y-1 transition-all shadow-xl shadow-indigo-500/20 text-sm">Update
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Quick Edit Fasilitas -->
        <div x-show="showFasilitasModal"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md"
            style="display: none;">
            <div @click.away="showFasilitasModal = false"
                class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-in zoom-in duration-300 border border-white">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight Level 2">Edit Fasilitas
                                Kamar</h3>
                            <div class="h-1.5 w-16 bg-[#36B2B2] mt-3 rounded-full"></div>
                        </div>
                        <button @click="showFasilitasModal = false"
                            class="text-gray-300 hover:text-rose-500 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form :action="activeKamar ? `/admin/kamar/${activeKamar.id}/fasilitas` : '#'" method="POST"
                        class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <div class="flex items-center justify-between mb-4 ml-1">
                                <label class="text-[11px] font-black uppercase tracking-widest text-[#36B2B2]">Daftar
                                    Fasilitas Hunian</label>
                                <button type="button" @click="addFasilitasRow"
                                    class="text-[10px] font-black text-[#36B2B2] border border-[#36B2B2]/20 bg-[#36B2B2]/5 px-3 py-1 rounded-lg uppercase tracking-widest hover:bg-[#36B2B2] hover:text-white transition-all">+
                                    Baris Baru</button>
                            </div>

                            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-3 custom-scrollbar">
                                <template x-for="(item, index) in fasilitasData.items" :key="index">
                                    <div class="flex items-center gap-3 animate-in slide-in-from-right-2 duration-300">
                                        <div class="flex-1 relative group">
                                            <input type="text" name="fasilitas[]" x-model="fasilitasData.items[index]"
                                                placeholder="Misal: AC"
                                                class="w-full px-5 py-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-700 text-sm">
                                            <div
                                                class="absolute left-0 bottom-0 h-0.5 w-0 bg-[#36B2B2] group-focus-within:w-full transition-all duration-500 rounded-b-xl">
                                            </div>
                                        </div>
                                        <button type="button" @click="removeFasilitasRow(index)"
                                            class="p-3 text-gray-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all border border-transparent hover:border-rose-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showFasilitasModal = false"
                                class="flex-1 px-6 py-4 bg-gray-50 text-gray-500 font-bold rounded-xl hover:bg-gray-100 transition-all border border-gray-100">Batal</button>
                            <button type="submit"
                                class="flex-[1.5] px-6 py-4 bg-[#36B2B2] text-white font-black rounded-xl hover:bg-[#2b8f8f] hover:-translate-y-1 transition-all shadow-xl shadow-[#36B2B2]/20 text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            window.existingKamars = {{ Js::from($kamars) }};
        </script>

        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #36B2B2;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #2b8f8f;
            }
        </style>

    </div>
@endsection
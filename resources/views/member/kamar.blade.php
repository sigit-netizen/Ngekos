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
                                                                                                                                        foto: '',
                                                                                                                                        addPreviewUrl: null,
                                                                                                                                        fasilitas: ['']
                                                                                                                                    },
                                                                                                                                    editFormData: {
                                                                                                                                        nomor_kamar: '',
                                                                                                                                        harga: '',
                                                                                                                                        foto: '',
                                                                                                                                        editPreviewUrl: null
                                                                                                                                    },
                                                                                                                                    fasilitasData: {
                                                                                                                                        items: []
                                                                                                                                    },
                                                                                                                                    init() {
                                                                                                                                        this.$watch('showAddModal', value => this.toggleScroll(value));
                                                                                                                                        this.$watch('showEditModal', value => this.toggleScroll(value));
                                                                                                                                        this.$watch('showFasilitasModal', value => this.toggleScroll(value));
                                                                                                                                    },
                                                                                                                                    toggleScroll(isEnabled) {
                                                                                                                                        if (isEnabled) {
                                                                                                                                            document.body.style.overflow = 'hidden';
                                                                                                                                        } else {
                                                                                                                                            // Only restore if all modals are closed
                                                                                                                                            if (!this.showAddModal && !this.showEditModal && !this.showFasilitasModal) {
                                                                                                                                                document.body.style.overflow = '';
                                                                                                                                            }
                                                                                                                                        }
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
                                                                                                                                            harga: this.formatCurrency(Math.round(kamar.harga)),
                                                                                                                                            foto: kamar.foto || '',
                                                                                                                                            editPreviewUrl: null
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
                                                                                                                                }"
        x-init="$watch('search', () => currentPage = 1); $watch('filterStatus', () => currentPage = 1);"
        class="pb-12 text-gray-800">

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-8 md:mb-10" data-aos="fade-up">
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
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 leading-tight tracking-tight">
                    Kamar & Unit <span class="text-[#36B2B2]">Inventaris</span>
                </h1>
                <p class="text-gray-500 mt-2 md:mt-3 text-sm md:text-base font-medium max-w-xl line-clamp-2">
                    Kelola ketersediaan, harga, dan fasilitas hunian kamar kos Anda dalam satu dasbor terpadu.
                </p>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
                @if($isPerKamar)
                    <div class="flex flex-col items-start sm:items-end mr-2 md:mr-4">
                        <span
                            class="text-[9px] md:text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Kuota
                            Kamar</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-lg md:text-xl font-black text-gray-900 leading-none">{{ $kamars->count() }}</span>
                            <span class="text-[10px] md:text-xs font-bold text-gray-400">/ {{ $limitKamar }}</span>
                        </div>
                    </div>
                @endif
                <button @click="showAddModal = true"
                    class="flex items-center gap-2 md:gap-3 px-6 md:px-8 py-3.5 md:py-4 bg-[#36B2B2] text-white font-black rounded-xl md:rounded-2xl hover:bg-[#2b8f8f] transition-all shadow-xl shadow-[#36B2B2]/20 group text-xs md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Kamar</span>
                </button>
            </div>
        </div>


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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8" data-aos="fade-up">
                <div class="sm:col-span-2 flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1">
                        <input type="text" x-model="search" placeholder="Cari nomor kamar..."
                            class="w-full px-6 md:px-8 py-4 md:py-5 bg-white border-2 border-gray-100 rounded-2xl md:rounded-3xl focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-700 shadow-sm shadow-gray-100/50 text-sm md:text-base">
                    </div>

                    <!-- Status Filter Chips -->
                    <div
                        class="flex bg-gray-200/50 p-1 rounded-2xl md:rounded-3xl items-center border border-gray-200 shadow-sm overflow-x-auto no-scrollbar">
                        <button @click="filterStatus = 'all'"
                            :class="filterStatus === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 md:px-6 py-2.5 md:py-3 rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap">Semua</button>
                        <button @click="filterStatus = 'tersedia'"
                            :class="filterStatus === 'tersedia' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'text-gray-500 hover:text-emerald-600'"
                            class="flex-1 px-4 md:px-6 py-2.5 md:py-3 rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap">Kosong</button>
                        <button @click="filterStatus = 'terisi'"
                            :class="filterStatus === 'terisi' ? 'bg-red-500 text-white shadow-lg shadow-rose-500/30' : 'text-gray-500 hover:text-red-600'"
                            class="flex-1 px-4 md:px-6 py-2.5 md:py-3 rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap">Terisi</button>
                    </div>
                </div>
                <div @click="filterStatus = 'tersedia'"
                    class="bg-emerald-100/40 border-2 border-emerald-200/60 rounded-2xl md:rounded-3xl p-4 md:p-5 flex items-center justify-between cursor-pointer hover:scale-[1.02] transition-transform shadow-sm hover:shadow-emerald-100">
                    <div>
                        <p class="text-[9px] md:text-[10px] font-black text-emerald-700 uppercase tracking-widest mb-1">Kosong
                        </p>
                        <p class="text-xl md:text-2xl font-black text-emerald-900 leading-none">
                            {{ $kamars->where('status', 'tersedia')->count() }}
                        </p>
                    </div>
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 bg-emerald-500/20 rounded-lg md:rounded-xl flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div @click="filterStatus = 'terisi'"
                    class="bg-rose-100/40 border-2 border-rose-200/60 rounded-2xl md:rounded-3xl p-4 md:p-5 flex items-center justify-between cursor-pointer hover:scale-[1.02] transition-transform shadow-sm hover:shadow-rose-100">
                    <div>
                        <p class="text-[9px] md:text-[10px] font-black text-rose-700 uppercase tracking-widest mb-1">Terisi</p>
                        <p class="text-xl md:text-2xl font-black text-rose-900 leading-none">
                            {{ $kamars->where('status', 'terisi')->count() }}
                        </p>
                    </div>
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 bg-rose-500/20 rounded-lg md:rounded-xl flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- List Container -->
                    <div class="space-y-6" data-aos="fade-up">
                        <!-- Desktop Table View -->
                        <div class="hidden md:block bg-white border-2 border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm">
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
                                                        <template x-if="kamar.status === 'terisi'">
                                                            <span
                                                                class="px-4 py-1.5 bg-rose-50 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-100">Terisi</span>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <div class="flex items-center justify-end gap-2 text-right">
                                                        <button @click="openEditModal(kamar)"
                                                            class="p-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100/50">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                        <form :action="`/admin/kamar/${kamar.id}`" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                @click="window.swalConfirm('Hapus Kamar?', 'Data kamar akan dihapus permanen.', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
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
                        </div>

                        <!-- Mobile Card View -->
                        <div class="grid grid-cols-1 gap-4 md:hidden">
                            <template x-for="(kamar, index) in pagedKamars" :key="kamar.id">
                                <div class="bg-white rounded-3xl p-5 border-2 border-gray-100 shadow-sm relative group active:scale-[0.98] transition-transform">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-[#36B2B2]/10 rounded-2xl flex items-center justify-center text-[#36B2B2] font-black shrink-0"
                                                x-text="kamar.nomor_kamar">
                                            </div>
                                            <div>
                                                <h4 class="font-black text-gray-900" x-text="'Unit ' + kamar.nomor_kamar"></h4>
                                                <template x-if="kamar.status === 'tersedia'">
                                                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Kosong</span>
                                                </template>
                                                <template x-if="kamar.status === 'terisi'">
                                                    <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest">Terisi</span>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="openEditModal(kamar)"
                                                class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <form :action="`/admin/kamar/${kamar.id}`" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    @click="window.swalConfirm('Hapus?', 'Hapus unit ini?', 'warning').then(res => res.isConfirmed && $el.closest('form').submit())"
                                                    class="p-2.5 bg-rose-50 text-rose-500 rounded-xl border border-rose-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mb-4 bg-gray-50/50 p-3 rounded-2xl border border-gray-100">
                                        <div>
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Harga Sewa</span>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-[10px] font-black text-[#36B2B2]">Rp</span>
                                                <span class="text-base font-black text-gray-900" x-text="formatCurrency(kamar.harga)"></span>
                                                <span class="text-[10px] font-bold text-gray-400">/Bulan</span>
                                            </div>
                                        </div>
                                        <div @click="openFasilitasModal(kamar)" class="text-right cursor-pointer">
                                            <span class="text-[9px] font-black text-[#36B2B2] uppercase tracking-widest block mb-1">Fasilitas</span>
                                            <div class="flex -space-x-2 justify-end">
                                                <template x-for="(fas, i) in (kamar.fasilitas || []).slice(0, 3)" :key="i">
                                                    <div class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-sm">
                                                        <span class="text-[8px] font-black text-gray-600" x-text="fas.nama_fasilitas.substring(0,1)"></span>
                                                    </div>
                                                </template>
                                                <template x-if="kamar.fasilitas && kamar.fasilitas.length > 3">
                                                    <div class="w-6 h-6 rounded-full bg-[#36B2B2] text-white border border-white flex items-center justify-center shadow-sm">
                                                        <span class="text-[8px] font-black" x-text="'+' + (kamar.fasilitas.length - 3)"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!kamar.fasilitas || kamar.fasilitas.length === 0">
                                                    <span class="text-[10px] text-gray-300 italic">Belum ada</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <button @click="openFasilitasModal(kamar)" class="w-full py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-[10px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-100 transition-colors">
                                        Kelola Fasilitas Unit
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- PAGINATION CONTROLS -->
                        <div class="px-6 md:px-8 py-6 bg-white border-2 border-gray-100 md:border-t rounded-[2rem] md:rounded-none md:rounded-b-[2.5rem] flex flex-col sm:flex-row items-center justify-between gap-6 shadow-sm shadow-gray-100/50">
                            <p class="text-[10px] md:text-xs font-bold text-gray-400 tracking-tight uppercase order-2 sm:order-1">
                                Menampilkan <span class="text-gray-900" x-text="pagedKamars.length"></span> dari <span
                                    class="text-gray-900" x-text="filteredKamars.length"></span> unit
                            </p>
                            <div class="flex items-center gap-2 order-1 sm:order-2">
                                <button @click="currentPage--" :disabled="currentPage === 1"
                                    class="p-2 border-2 border-gray-100 rounded-xl hover:border-[#36B2B2] disabled:opacity-30 disabled:hover:border-gray-100 transition-all text-[#36B2B2]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7">
                                        </path>
                                    </svg>
                                </button>

                                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar max-w-[150px] sm:max-w-none px-1">
                                    <template x-for="page in totalPages" :key="page">
                                        <button @click="currentPage = page"
                                            :class="currentPage === page ? 'bg-[#36B2B2] text-white border-[#36B2B2] shadow-lg shadow-[#36B2B2]/20' : 'bg-white text-gray-400 border-gray-100 hover:border-[#36B2B2] hover:text-[#36B2B2]'"
                                            class="w-10 h-10 border-2 rounded-xl text-[10px] font-black transition-all shrink-0"
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
                class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md overflow-hidden"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                <form @click.away="showAddModal = false" action="{{ route('admin.kamar.store') }}" method="POST"
                    enctype="multipart/form-data"
                    class="bg-white rounded-[1.5rem] shadow-2xl w-full max-w-md flex flex-col overflow-hidden animate-in zoom-in duration-300 border border-white"
                    style="max-height: 82vh;">
                    @csrf

                    <!-- Header (Fixed) -->
                    <div class="px-5 pt-4 pb-2 shrink-0 border-b border-gray-50/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Tambah Data
                                    Kamar</h3>
                                <div class="h-1 w-10 bg-[#36B2B2] mt-1.5 rounded-full"></div>
                            </div>
                            <button type="button" @click="showAddModal = false"
                                class="text-gray-300 hover:text-rose-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Scrollable Area -->
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5 space-y-4">
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Nomor
                                Kamar</label>
                            <input type="text" name="nomor_kamar" x-model="formData.nomor_kamar" required
                                placeholder="Contoh: 101, A1, VIP"
                                :class="isNomorKamarDuplicate ? 'border-rose-500 bg-rose-50' : 'border-gray-100 bg-gray-50'"
                                class="w-full px-5 py-2.5 border-2 rounded-xl focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-800 text-sm">
                            <template x-if="isNomorKamarDuplicate">
                                <p
                                    class="text-[9px] font-black text-rose-500 mt-1.5 ml-1 uppercase tracking-widest animate-in fade-in slide-in-from-top-1">
                                    Nomor kamar sudah ada!</p>
                            </template>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Harga
                                Sewa / Bulan</label>
                            <div class="relative">
                                <input type="text" name="harga" x-model="formData.harga"
                                    @input="updateHarga($event, 'formData')" required placeholder="Misal: 800.000"
                                    class="w-full px-5 py-2.5 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-[#36B2B2] outline-none transition-all font-black text-gray-800 text-sm">
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-4 ml-1 text-center">Pilih
                                Cara Unggah Foto Kamar</label>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Camera Button -->
                                <button type="button" @click="document.getElementById('cameraInputAdd').click()"
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-100 hover:border-emerald-500 transition-all group">
                                    <div
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-700">Kamera</span>
                                </button>

                                <!-- Gallery Button -->
                                <button type="button" @click="document.getElementById('galleryInputAdd').click()"
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-blue-50 border-2 border-blue-100 hover:border-blue-500 transition-all group">
                                    <div
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-700">Galeri</span>
                                </button>
                            </div>

                            <!-- Hidden Inputs -->
                            <input type="file" id="cameraInputAdd" name="foto_camera" accept="image/*" capture="environment"
                                class="hidden"
                                @change="const file = $event.target.files[0]; if(file) { formData.addPreviewUrl = URL.createObjectURL(file); document.getElementById('galleryInputAdd').value = ''; }">
                            <input type="file" id="galleryInputAdd" name="foto_gallery" accept="image/*" class="hidden"
                                @change="const file = $event.target.files[0]; if(file) { formData.addPreviewUrl = URL.createObjectURL(file); document.getElementById('cameraInputAdd').value = ''; }">

                            <div x-show="formData.addPreviewUrl"
                                class="mt-3 p-1.5 bg-gray-50 rounded-xl border-2 border-dashed border-gray-100 transition-all relative group"
                                x-cloak>
                                <img :src="formData.addPreviewUrl"
                                    class="w-full h-auto max-h-[100px] object-cover rounded-lg shadow-sm bg-gray-100">
                                <button type="button"
                                    @click="formData.addPreviewUrl = null; document.getElementById('cameraInputAdd').value = ''; document.getElementById('galleryInputAdd').value = '';"
                                    class="absolute top-0 right-0 p-2 bg-rose-500 text-white rounded-full shadow-lg hover:bg-rose-600 transition-all hover:scale-110 transform translate-x-1/4 -translate-y-1/4 z-10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <p class="text-[9px] text-center text-[#36B2B2] font-black mt-3 uppercase tracking-widest">
                                    FOTO SIAP DIUNGGAH ✨</p>
                            </div>
                            <p class="text-[9px] text-gray-400 mt-3 text-center font-medium">Format: JPG, PNG, WebP. Max:
                                10MB</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3 ml-1">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400">Daftar
                                    Fasilitas</label>
                                <button type="button" @click="addFasilitasRowNew"
                                    class="text-[10px] font-black text-[#36B2B2] uppercase tracking-widest hover:underline">+
                                    Tambah</button>
                            </div>
                            <div class="space-y-3">
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

                    </div>

                    <!-- Footer Buttons -->
                    <div
                        class="px-5 py-4 border-t border-gray-50 flex gap-3 shrink-0 bg-white shadow-[0_-10px_30px_rgba(0,0,0,0.02)]">
                        <button type="button" @click="showAddModal = false"
                            class="flex-1 px-5 py-3.5 bg-gray-50 text-gray-400 font-bold rounded-xl hover:bg-gray-100 transition-all border border-gray-100 text-xs uppercase tracking-widest">Batal</button>
                        <button type="submit" :disabled="isNomorKamarDuplicate"
                            :class="isNomorKamarDuplicate ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-[#36B2B2] text-white hover:bg-[#2b8f8f] transition-all shadow-lg shadow-[#36B2B2]/20'"
                            class="flex-[1.5] px-5 py-3.5 font-black rounded-xl text-xs uppercase tracking-widest">Simpan
                            Kamar</button>
                    </div>
                </form>
            </div>

            <!-- MODAL: Edit Kamar -->
            <div x-show="showEditModal"
                class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md overflow-hidden"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                <form @click.away="showEditModal = false" :action="activeKamar ? `/admin/kamar/${activeKamar.id}` : '#'"
                    method="POST" enctype="multipart/form-data"
                    class="bg-white rounded-[1.5rem] shadow-2xl w-full max-w-md flex flex-col overflow-hidden animate-in zoom-in duration-300 border border-white"
                    style="max-height: 82vh;">
                    @csrf
                    @method('PUT')

                    <!-- Header (Fixed) -->
                    <div class="px-5 pt-4 pb-2 shrink-0 border-b border-gray-50/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Ubah Data
                                    Kamar</h3>
                                <div class="h-1 w-10 bg-indigo-600 mt-1.5 rounded-full"></div>
                            </div>
                            <button type="button" @click="showEditModal = false"
                                class="text-gray-300 hover:text-rose-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Scrollable Area -->
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5 space-y-4">

                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Nomor
                                Kamar</label>
                            <input type="text" name="nomor_kamar" x-model="editFormData.nomor_kamar" required
                                class="w-full px-5 py-2.5 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-600 outline-none transition-all font-bold text-gray-800 text-sm">
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Harga
                                Sewa / Bulan</label>
                            <div class="relative">
                                <input type="text" name="harga" x-model="editFormData.harga"
                                    @input="updateHarga($event, 'editFormData')" required
                                    class="w-full px-5 py-2.5 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-600 outline-none transition-all font-black text-gray-800 text-sm">
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-1 text-center">Ganti
                                Foto Kamar (Opsional)</label>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="document.getElementById('cameraInputEdit').click()"
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-indigo-50 border-2 border-indigo-100 hover:border-indigo-500 transition-all group">
                                    <div
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-indigo-700">Kamera</span>
                                </button>

                                <button type="button" @click="document.getElementById('galleryInputEdit').click()"
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-blue-50 border-2 border-blue-100 hover:border-blue-500 transition-all group">
                                    <div
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-700">Galeri</span>
                                </button>
                            </div>

                            <input type="file" id="cameraInputEdit" name="foto_camera" accept="image/*" capture="environment"
                                class="hidden"
                                @change="const file = $event.target.files[0]; if(file) { editFormData.editPreviewUrl = URL.createObjectURL(file); document.getElementById('galleryInputEdit').value = ''; }">
                            <input type="file" id="galleryInputEdit" name="foto_gallery" accept="image/*" class="hidden"
                                @change="const file = $event.target.files[0]; if(file) { editFormData.editPreviewUrl = URL.createObjectURL(file); document.getElementById('cameraInputEdit').value = ''; }">

                            <div x-show="editFormData.editPreviewUrl"
                                class="mt-3 p-1.5 bg-gray-50 rounded-xl border-2 border-dashed border-gray-100 transition-all relative group"
                                x-cloak>
                                <img :src="editFormData.editPreviewUrl"
                                    class="w-full h-auto max-h-[100px] object-cover rounded-lg shadow-sm bg-gray-100">
                                <button type="button"
                                    @click="editFormData.editPreviewUrl = null; document.getElementById('cameraInputEdit').value = ''; document.getElementById('galleryInputEdit').value = '';"
                                    class="absolute top-0 right-0 p-2 bg-rose-500 text-white rounded-full shadow-lg hover:bg-rose-600 transition-all hover:scale-110 transform translate-x-1/4 -translate-y-1/4 z-10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <p class="text-[9px] text-center text-indigo-600 font-black mt-3 uppercase tracking-widest">
                                    FOTO BARU SIAP DIUNGGAH ✨</p>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Buttons (Fixed) -->
                    <div
                        class="px-5 py-4 border-t border-gray-50 flex gap-3 shrink-0 bg-white shadow-[0_-10px_30px_rgba(0,0,0,0.02)]">
                        <button type="button" @click="showEditModal = false"
                            class="flex-1 px-5 py-3.5 bg-gray-50 text-gray-400 font-bold rounded-xl hover:bg-gray-100 transition-all border border-gray-100 text-xs uppercase tracking-widest">Batal</button>
                        <button type="submit"
                            class="flex-[1.5] px-5 py-3.5 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 text-xs uppercase tracking-widest">Update
                            Data</button>
                    </div>
                </form>
            </div>

            <!-- MODAL: Quick Edit Fasilitas -->
            <div x-show="showFasilitasModal"
                class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md overflow-hidden"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                <form @click.away="showFasilitasModal = false"
                    :action="activeKamar ? `/admin/kamar/${activeKamar.id}/fasilitas` : '#'" method="POST"
                    class="bg-white rounded-[1.5rem] shadow-2xl w-full max-w-md flex flex-col overflow-hidden animate-in zoom-in duration-300 border border-white"
                    style="max-height: 82vh;">
                    @csrf
                    @method('PUT')

                    <!-- Header (Fixed) -->
                    <div class="px-5 pt-4 pb-2 shrink-0 border-b border-gray-50/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Edit
                                    Fasilitas Kamar</h3>
                                <div class="h-1 w-10 bg-[#36B2B2] mt-1.5 rounded-full"></div>
                            </div>
                            <button type="button" @click="showFasilitasModal = false"
                                class="text-gray-300 hover:text-rose-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Scrollable Area -->
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5">
                        <div>
                            <div class="flex items-center justify-between mb-3 ml-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-[#36B2B2]">Daftar
                                    Fasilitas Hunian</label>
                                <button type="button" @click="addFasilitasRow"
                                    class="text-[9px] font-black text-[#36B2B2] border border-[#36B2B2]/20 bg-[#36B2B2]/5 px-2.5 py-1 rounded-lg uppercase tracking-widest hover:bg-[#36B2B2] hover:text-white transition-all">+
                                    Baris Baru</button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(item, index) in fasilitasData.items" :key="index">
                                    <div class="flex items-center gap-2 animate-in slide-in-from-right-2 duration-300">
                                        <div class="flex-1 relative group">
                                            <input type="text" name="fasilitas[]" x-model="fasilitasData.items[index]"
                                                placeholder="Misal: AC"
                                                class="w-full px-4 py-2 bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-[#36B2B2] outline-none transition-all font-bold text-gray-700 text-xs">
                                        </div>
                                        <button type="button" @click="removeFasilitasRow(index)"
                                            class="p-2 text-gray-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all border border-transparent hover:border-rose-100">
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
                    </div>

                    <!-- Footer Buttons (Fixed) -->
                    <div
                        class="px-5 py-4 border-t border-gray-50 flex gap-3 shrink-0 bg-white shadow-[0_-10px_30px_rgba(0,0,0,0.02)]">
                        <button type="button" @click="showFasilitasModal = false"
                            class="flex-1 px-5 py-3.5 bg-gray-50 text-gray-400 font-bold rounded-xl hover:bg-gray-100 transition-all border border-gray-100 text-xs uppercase tracking-widest">Batal</button>
                        <button type="submit"
                            class="flex-[1.5] px-5 py-3.5 bg-[#36B2B2] text-white font-black rounded-xl hover:bg-[#2b8f8f] transition-all shadow-lg shadow-[#36B2B2]/20 text-xs uppercase tracking-widest">Simpan</button>
                    </div>
                </form>
            </div>

            <script>
                window.existingKamars = {{ Js::from($kamars) }};
            </script>

            <style>
                .custom-scrollbar {
                    scrollbar-width: thin;
                    scrollbar-color: #36B2B2 #f1f1f1;
                }

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
                    border: 1px solid #f1f1f1;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #2b8f8f;
                }
            </style>

        </div>
@endsection
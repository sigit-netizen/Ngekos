@extends('layouts.dashboard')

@section('dashboard-content')
    <div x-data="aduanPublik()" class="space-y-6">
        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50" data-aos="fade-up">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Aduan Publik 📢</h1>
                    <p class="text-gray-500">Kelola aduan dan masukan dari publik (pengunjung website).</p>
                </div>
                <div class="flex items-center gap-4">
                    <button x-show="selectedIds.length > 0" @click="hapusTerpilih()"
                        class="bg-red-50 text-red-600 text-[10px] font-black px-4 py-2 rounded-xl border border-red-100 hover:bg-red-100 transition-all flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                    </button>
                    <span
                        class="bg-orange-50 text-orange-600 text-[10px] font-black px-3 py-1.5 rounded-full border border-orange-100"
                        x-text="items.length + ' Aduan'"></span>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" data-aos="fade-up"
            data-aos-delay="100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                            <th class="px-6 py-4">
                                <input type="checkbox" @click="toggleSelectAll()" :checked="allSelected"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            </th>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email / Akun Google</th>
                            <th class="px-6 py-4">Subjek</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, idx) in paginatedItems" :key="item.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" :value="item.id" x-model="selectedIds"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 font-bold"
                                    x-text="idx + 1 + (currentPage - 1) * perPage"></td>
                                <td class="px-6 py-4 font-bold text-gray-800 text-sm" x-text="item.nama"></td>
                                <td class="px-6 py-4 text-xs text-gray-500" x-text="item.email"></td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate" x-text="item.subjek">
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400" x-text="item.tanggal"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                        :class="item.status === 'baru' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-green-50 text-green-600 border border-green-100'"
                                        x-text="item.status === 'baru' ? 'Baru' : 'Dibaca'"></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openDetail(item)"
                                            class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button @click="hapus(item.id)"
                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="paginatedItems.length === 0">
                            <td colspan="8" class="px-6 py-16 text-center text-gray-400 italic text-sm">Tidak ada aduan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination Controls --}}
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                <div class="text-xs text-gray-500">
                    Menampilkan <span class="font-bold text-gray-900" x-text="paginatedItems.length"></span> dari <span
                        class="font-bold text-gray-900" x-text="items.length"></span> data
                </div>
                <div class="flex items-center gap-2">
                    <button @click="currentPage--" :disabled="currentPage === 1"
                        class="p-2 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-xs font-bold transition-all"
                                :class="currentPage === p ? 'bg-[#36B2B2] text-white shadow-lg shadow-[#36B2B2]/20' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                                x-text="p"></button>
                        </template>
                    </div>
                    <button @click="currentPage++" :disabled="currentPage === totalPages"
                        class="p-2 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div x-show="showDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="showDetail" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" @click="showDetail = false"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div x-show="showDetail" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-orange-50/50">
                    <h3 class="text-lg font-black text-gray-900">Detail Aduan Publik</h3>
                    <p class="text-xs text-gray-500 mt-1">Aduan dari <span class="font-bold text-orange-600"
                            x-text="detail?.nama"></span></p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Nama</p>
                            <p class="text-sm font-bold text-gray-800" x-text="detail?.nama"></p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Email</p>
                            <p class="text-sm font-bold text-gray-800" x-text="detail?.email"></p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Tanggal</p>
                            <p class="text-sm font-bold text-gray-800" x-text="detail?.tanggal"></p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Status</p>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
                                :class="detail?.status === 'baru' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'"
                                x-text="detail?.status === 'baru' ? 'Baru' : 'Dibaca'"></span>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Subjek</p>
                        <p class="text-sm font-bold text-gray-800" x-text="detail?.subjek"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase mb-2">Isi Pesan</p>
                        <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed" x-text="detail?.pesan">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button @click="showDetail = false"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl text-xs font-bold hover:bg-gray-300 transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function aduanPublik() {
            return {
                showDetail: false,
                detail: null,
                currentPage: 1,
                perPage: 10,
                selectedIds: [],
                items: [
                    { id: 1, nama: 'Ahmad Pengunjung', email: 'ahmad.visit@gmail.com', subjek: 'Harga kos tidak update', tanggal: '28 Feb 2026', status: 'baru', pesan: 'Saya melihat iklan kos di website dengan harga Rp 800.000/bulan tapi saat menghubungi pemilik ternyata harganya sudah Rp 1.200.000. Mohon data harga diperbarui.' },
                    { id: 2, nama: 'Linda Pencari Kos', email: 'linda.cari@gmail.com', subjek: 'Foto kos tidak sesuai', tanggal: '26 Feb 2026', status: 'baru', pesan: 'Foto kamar kos yang ditampilkan di website sangat berbeda dengan kondisi aslinya. Kamar jauh lebih kecil dan tidak seterang di foto. Ini menyesatkan.' },
                    { id: 3, nama: 'Rudi Wisatawan', email: 'rudi.travel@gmail.com', subjek: 'Lokasi kos salah di peta', tanggal: '24 Feb 2026', status: 'dibaca', pesan: 'Pin lokasi kos "Kos Melati Indah" di peta tidak sesuai. Saya sudah ke lokasi yang ditunjuk tapi ternyata kosnya ada di jalan lain. Mohon diperbaiki.' },
                    { id: 4, nama: 'Siti Aminah', email: 'siti@test.com', subjek: 'Cara daftar akun baru', tanggal: '23 Feb 2026', status: 'baru', pesan: 'Saya ingin mendaftar sebagai pemilik kos tapi bingung langkah-langkahnya.' },
                    { id: 5, nama: 'Budi Raharjo', email: 'budi@test.com', subjek: 'Tanya promo', tanggal: '22 Feb 2026', status: 'dibaca', pesan: 'Apakah ada promo untuk langganan Pro selama 1 tahun?' },
                    { id: 6, nama: 'Nina Kartika', email: 'nina@test.com', subjek: 'Fitur map error', tanggal: '21 Feb 2026', status: 'baru', pesan: 'Peta tidak muncul di browser Safari versi lama saya.' },
                    { id: 7, nama: 'Dedi Kurniawan', email: 'dedi@test.com', subjek: 'Request aplikasi mobile', tanggal: '20 Feb 2026', status: 'dibaca', pesan: 'Kapan aplikasi mobile Android/iOS tersedia? Akan sangat memudahkan.' },
                    { id: 8, nama: 'Ani Fitriani', email: 'ani@test.com', subjek: 'Email verifikasi lama', tanggal: '19 Feb 2026', status: 'baru', pesan: 'Saya menunggu email verifikasi sudah 1 jam belum masuk juga.' },
                    { id: 9, nama: 'Eko Santoso', email: 'eko@test.com', subjek: 'Lupa password', tanggal: '18 Feb 2026', status: 'dibaca', pesan: 'Link reset password tidak bisa diklik.' },
                    { id: 10, nama: 'Rina Wijaya', email: 'rina@test.com', subjek: 'Saran tampilan', tanggal: '17 Feb 2026', status: 'baru', pesan: 'Tampilan website sudah bagus tapi fontnya agak kekecilan di HP.' },
                    { id: 11, nama: 'Tono Saputra', email: 'tono@test.com', subjek: 'Kerjasama iklan', tanggal: '16 Feb 2026', status: 'dibaca', pesan: 'Saya ingin memasang iklan banner di website ini, hubungi siapa ya?' },
                ],
                get paginatedItems() {
                    let start = (this.currentPage - 1) * this.perPage;
                    let end = start + this.perPage;
                    return this.items.slice(start, end);
                },
                get totalPages() {
                    return Math.ceil(this.items.length / this.perPage) || 1;
                },
                get allSelected() {
                    return this.paginatedItems.length > 0 && this.paginatedItems.every(item => this.selectedIds.includes(item.id));
                },
                toggleSelectAll() {
                    let currentPageIds = this.paginatedItems.map(item => item.id);
                    if (this.allSelected) {
                        this.selectedIds = this.selectedIds.filter(id => !currentPageIds.includes(id));
                    } else {
                        currentPageIds.forEach(id => {
                            if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                        });
                    }
                },
                openDetail(item) {
                    this.detail = item;
                    item.status = 'dibaca';
                    this.showDetail = true;
                },
                hapus(id) {
                    if (confirm('Yakin ingin menghapus aduan ini?')) {
                        this.items = this.items.filter(i => i.id !== id);
                        this.selectedIds = this.selectedIds.filter(i => i !== id);
                        if (this.currentPage > this.totalPages) this.currentPage = this.totalPages;
                    }
                },
                hapusTerpilih() {
                    if (this.selectedIds.length === 0) return;
                    if (confirm(`Yakin ingin menghapus ${this.selectedIds.length} aduan terpilih?`)) {
                        this.items = this.items.filter(i => !this.selectedIds.includes(i.id));
                        this.selectedIds = [];
                        if (this.currentPage > this.totalPages) this.currentPage = this.totalPages;
                    }
                }
            }
        }
    </script>
@endsection
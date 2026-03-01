@extends('layouts.dashboard')

@section('dashboard-content')
    <div x-data="aduanUser()" class="space-y-6">
        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-white/50" data-aos="fade-up">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Aduan User 💬</h1>
                    <p class="text-gray-500">Kelola aduan dan masukan dari user (penyewa kos).</p>
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
                        class="bg-purple-50 text-purple-600 text-[10px] font-black px-3 py-1.5 rounded-full border border-purple-100"
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
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
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
                                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
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
                <div class="p-6 border-b border-gray-100 bg-purple-50/50">
                    <h3 class="text-lg font-black text-gray-900">Detail Aduan User</h3>
                    <p class="text-xs text-gray-500 mt-1">Aduan dari <span class="font-bold text-purple-600"
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
        function aduanUser() {
            return {
                showDetail: false,
                detail: null,
                currentPage: 1,
                perPage: 10,
                selectedIds: [],
                items: [
                    { id: 1, nama: 'Rina Mahasiswi', email: 'rina.mhs@gmail.com', subjek: 'Kamar bocor saat hujan', tanggal: '28 Feb 2026', status: 'baru', pesan: 'Kamar saya di lantai 2 bocor setiap kali hujan deras. Sudah melapor ke pemilik kos tapi belum ada tindakan. Mohon bantuan dari pihak platform.' },
                    { id: 2, nama: 'Doni Pekerja', email: 'doni.work@gmail.com', subjek: 'WiFi sering mati', tanggal: '27 Feb 2026', status: 'baru', pesan: 'Internet di kos sering mati terutama malam hari. Ini sangat mengganggu pekerjaan saya karena saya WFH. Pemilik kos tidak responsif.' },
                    { id: 3, nama: 'Maya Karyawati', email: 'maya.office@gmail.com', subjek: 'Deposit tidak dikembalikan', tanggal: '25 Feb 2026', status: 'baru', pesan: 'Saya sudah keluar dari kos sejak 2 minggu yang lalu tapi deposit Rp 500.000 belum dikembalikan. Pemilik kos sulit dihubungi. Tolong mediasi.' },
                    { id: 4, nama: 'Fajar Mahasiswa', email: 'fajar.univ@gmail.com', subjek: 'AC tidak dingin', tanggal: '24 Feb 2026', status: 'dibaca', pesan: 'AC di kamar saya sudah tidak dingin sejak 1 bulan lalu. Pemilik kos bilang akan diperbaiki tapi sampai sekarang belum ada teknisi yang datang.' },
                    { id: 5, nama: 'Lina Perantau', email: 'lina.rantau@gmail.com', subjek: 'Penyewa lain berisik', tanggal: '23 Feb 2026', status: 'dibaca', pesan: 'Penghuni kamar sebelah sering memutar musik keras sampai larut malam. Sudah ditegur langsung tapi tidak berubah. Mohon pemilik kos diberi tahu.' },
                    { id: 6, nama: 'Budi Santoso', email: 'budi@test.com', subjek: 'Tagihan tidak muncul', tanggal: '22 Feb 2026', status: 'baru', pesan: 'Saya sudah bayar tapi tagihan untuk bulan depan belum muncul di aplikasi.' },
                    { id: 7, nama: 'Santi Putri', email: 'santi@test.com', subjek: 'Kunci kamar rusak', tanggal: '21 Feb 2026', status: 'dibaca', pesan: 'Kunci kamar saya doll, mohon segera diganti demi keamanan.' },
                    { id: 8, nama: 'Rian Pratama', email: 'rian@test.com', subjek: 'Air mati', tanggal: '20 Feb 2026', status: 'baru', pesan: 'Sudah dari pagi air di kos mati total. Mohon dicek pompanya.' },
                    { id: 9, nama: 'Dewi Lestari', email: 'dewi@test.com', subjek: 'Sampah menumpuk', tanggal: '19 Feb 2026', status: 'dibaca', pesan: 'Petugas sampah sudah 3 hari tidak datang, bau mulai menyengat.' },
                    { id: 10, nama: 'Andik Jaya', email: 'andik@test.com', subjek: 'Parkir penuh', tanggal: '18 Feb 2026', status: 'baru', pesan: 'Area parkir motor sangat penuh, susah untuk keluar masuk.' },
                    { id: 11, nama: 'Yulia Sari', email: 'yulia@test.com', subjek: 'Lampu koridor mati', tanggal: '17 Feb 2026', status: 'dibaca', pesan: 'Lampu di depan kamar saya mati, jadi gelap kalau malam.' },
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
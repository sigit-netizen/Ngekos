@extends('layouts.dashboard')

@section('dashboard-content')
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 sm:p-10 shadow-sm border border-white/50 mb-8"
        data-aos="fade-up">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-[#36B2B2]/10 rounded-xl flex items-center justify-center text-[#36B2B2]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Manajemen Fasilitas Kos 🛋️
                    </h1>
                </div>
                <p class="text-gray-500 font-medium">Kelola daftar fasilitas tambahan yang dapat dipilih oleh penyewa kos
                    Anda.</p>
            </div>
            <button onclick="window.showAddModal()"
                class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-[#36B2B2] text-white text-sm font-bold rounded-2xl hover:bg-[#2D8E8E] transition-all duration-300 shadow-lg shadow-[#36B2B2]/20 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Tambah Fasilitas
            </button>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden mb-10" data-aos="fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-16">No</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Nama Fasilitas
                        </th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Harga Tambahan
                            (Bulan)</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($fasilitas as $index => $item)
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <span class="text-xs font-black text-gray-400">#{{ $index + 1 }}</span>
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-gray-800">
                                {{ $item->nama_fasilitas }}
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-black text-[#36B2B2]">Rp
                                    {{ number_format($item->harga_tambahan, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="window.showEditModal({{ json_encode($item) }})"
                                        class="p-2.5 text-blue-500 bg-blue-50 rounded-xl hover:bg-blue-500 hover:text-white transition-all duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.fasilitas.destroy', $item->id) }}" method="POST"
                                        id="delete-form-{{ $item->id }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="window.confirmDelete({{ $item->id }})"
                                            class="p-2.5 text-rose-500 bg-rose-50 rounded-xl hover:bg-rose-500 hover:text-white transition-all duration-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-2">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest leading-loose">Belum
                                        ada fasilitas tambahan</p>
                                    <button onclick="window.showAddModal()"
                                        class="text-[#36B2B2] text-xs font-black uppercase tracking-widest hover:underline">+
                                        Tambah Fasilitas Pertama</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modals --}}
    <div id="fasilitasModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none">
        <div
            class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl relative transform transition-all duration-300 scale-95 opacity-0">
            <div class="bg-gradient-to-r from-[#36B2B2] to-[#2D8E8E] p-8 text-white">
                <button onclick="window.closeModal()"
                    class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
                <h3 id="modalTitle" class="text-2xl font-black mb-1">Tambah Fasilitas ✨</h3>
                <p class="text-white/70 text-sm font-medium">Input nama fasilitas dan biaya tambahan per bulannya.</p>
            </div>

            <form id="fasilitasForm" action="{{ route('admin.fasilitas.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Nama
                            Fasilitas</label>
                        <input type="text" name="nama_fasilitas" id="input_nama" required
                            placeholder="Mis: Kulkas Mini, AC 1/2 PK"
                            class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#36B2B2]/20 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Harga
                            Tambahan (Bulanan)</label>
                        <div class="relative">
                            <input type="text" name="harga_tambahan_display" id="input_harga_display" required
                                placeholder="Rp 0" oninput="window.formatIDR(this)"
                                class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#36B2B2]/20 transition-all outline-none">
                            <input type="hidden" name="harga_tambahan" id="input_harga_hidden">
                        </div>
                    </div>
                </div>

                <div class="p-8 pt-0 flex gap-4">
                    <button type="button" onclick="window.closeModal()"
                        class="flex-1 px-8 py-4 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] px-8 py-4 bg-[#36B2B2] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-[#2D8E8E] transition-all shadow-lg shadow-[#36B2B2]/20">
                        Simpan Perubahan →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('fasilitasModal');
        const modalContent = modal.querySelector('.transform');
        const form = document.getElementById('fasilitasForm');
        const title = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');
        const inputNama = document.getElementById('input_nama');
        const inputHargaDisplay = document.getElementById('input_harga_display');
        const inputHargaHidden = document.getElementById('input_harga_hidden');

        window.formatIDR = (input) => {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value === "") {
                input.value = "";
                inputHargaHidden.value = "";
                return;
            }

            inputHargaHidden.value = value;
            input.value = "Rp " + new Intl.NumberFormat('id-ID').format(value);
        }

        window.showAddModal = () => {
            title.innerText = 'Tambah Fasilitas ✨';
            form.action = "{{ route('admin.fasilitas.store') }}";
            methodField.innerHTML = '';
            inputNama.value = '';
            inputHargaDisplay.value = '';
            inputHargaHidden.value = '';
            open();
        }

        window.showEditModal = (item) => {
            title.innerText = 'Edit Fasilitas 📝';
            form.action = `/admin/fasilitas/${item.id}`;
            methodField.innerHTML = '@method("PUT")';
            inputNama.value = item.nama_fasilitas;

            // Set initial value for editing
            inputHargaHidden.value = item.harga_tambahan;
            inputHargaDisplay.value = "Rp " + new Intl.NumberFormat('id-ID').format(item.harga_tambahan);

            open();
        }

        window.closeModal = () => {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        const open = () => {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modalContent.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        window.confirmDelete = (id) => {
            if (window.swalConfirm) {
                window.swalConfirm('Hapus Fasilitas?', 'Penyewa tidak akan bisa melihat pilihan fasilitas ini lagi.', 'warning')
                    .then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
            } else {
                if (confirm('Yakin ingin menghapus fasilitas ini?')) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            }
        }
    </script>
@endsection
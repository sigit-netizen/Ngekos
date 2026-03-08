@extends('layouts.dashboard')

@section('dashboard-content')
    @php
        $room = $room ?? null;
        $kosId = $kosId ?? null;
        $facilities = $facilities ?? collect();
        $aduans = $aduans ?? collect();
    @endphp

    <div x-data="{
                    activeFilter: 'all',
                    showAduanModal: false,
                    showTambahModal: false,
                    isSubmitting: false,

                    async submitForm(e, type) {
                        this.isSubmitting = true;
                        const form = e.target;
                        const formData = new FormData(form);

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                if (window.swalToast) {
                                    window.swalToast(data.message, 'success');
                                } else {
                                    alert(data.message);
                                }

                                // Reset and close
                                form.reset();
                                if (type === 'aduan') this.showAduanModal = false;
                                if (type === 'tambah') this.showTambahModal = false;

                                // Reload page after a short delay to show the new history item
                                // Alternatively, we could prepend the item to the table via JS for true SPA feel
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                throw new Error(data.message || 'Terjadi kesalahan');
                            }
                        } catch (error) {
                            if (window.swalToast) {
                                window.swalToast(error.message, 'error');
                            } else {
                                alert(error.message);
                            }
                        } finally {
                            this.isSubmitting = false;
                        }
                    }
                }" x-init="
                    $watch('showAduanModal',  v => v ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden'));
                    $watch('showTambahModal', v => v ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden'));
                ">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 font-bold text-sm"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show=false, 4000)"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div
                class="mb-6 bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3 font-bold text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01" />
                </svg>
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        {{-- Header Summary Card --}}
        <div class="bg-gradient-to-br from-[#36B2B2] to-[#2D8E8E] rounded-[2.5rem] p-8 sm:p-12 shadow-2xl shadow-[#36B2B2]/20 mb-10 overflow-hidden relative group"
            data-aos="fade-up">
            <div
                class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700">
            </div>
            <div
                class="absolute -bottom-24 -left-24 w-64 h-64 bg-black/5 rounded-full blur-3xl group-hover:bg-black/10 transition-all duration-700">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3 tracking-tight">Fasilitas Kos 🛋️</h1>
                    <p class="text-white/80 text-lg font-medium max-w-md leading-relaxed">
                        @if ($room)
                            Kelola fasilitas di <strong>Kamar {{ $room->nomor_kamar }}</strong>. Ajukan aduan atau permintaan
                            tambahan dengan mudah.
                        @else
                            Ajukan aduan atau permintaan fasilitas tambahan ke pengelola kos Anda.
                        @endif
                    </p>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    <div class="bg-white/20 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/30 text-center">
                        <p class="text-2xl font-black text-white">{{ count($aduans->where('kategori', 'fasilitas')) }}</p>
                        <p class="text-[10px] font-black text-white/70 uppercase tracking-widest">Aduan</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/30 text-center">
                        <p class="text-2xl font-black text-white">{{ count($aduans->where('kategori', 'tambah')) }}</p>
                        <p class="text-[10px] font-black text-white/70 uppercase tracking-widest">Tambahan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            {{-- Aduan Fasilitas --}}
            <div class="group relative bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden"
                data-aos="fade-right">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-bl-[5rem] -mr-10 -mt-10 transition-all group-hover:bg-rose-100 duration-500">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-16 h-16 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 mb-8 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-3 leading-tight tracking-tight">Aduan Fasilitas</h2>
                    <p class="text-gray-500 font-medium mb-8 leading-relaxed max-w-[280px]">Laporkan kendala fasilitas kamar
                        Anda untuk penanganan cepat oleh pengelola.</p>
                    @if($kosId)
                        <button @click="showAduanModal = true"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-500 hover:text-white transition-all duration-300 shadow-lg shadow-rose-500/10">
                            Buat Aduan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                    @else
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tidak ada kamar
                            aktif</span>
                    @endif
                </div>
            </div>

            {{-- Tambah Fasilitas --}}
            <div class="group relative bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden"
                data-aos="fade-left">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-[#36B2B2]/5 rounded-bl-[5rem] -mr-10 -mt-10 transition-all group-hover:bg-[#36B2B2]/10 duration-500">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-16 h-16 bg-[#36B2B2]/10 rounded-2xl flex items-center justify-center text-[#36B2B2] mb-8 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-3 leading-tight tracking-tight">Tambah Fasilitas</h2>
                    <p class="text-gray-500 font-medium mb-8 leading-relaxed max-w-[280px]">Ingin fasilitas tambahan? Ajukan
                        di sini untuk kenyamanan ekstra hunian Anda.</p>
                    @if($kosId)
                        <button @click="showTambahModal = true"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-[#36B2B2]/5 text-[#36B2B2] text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-[#36B2B2] hover:text-white transition-all duration-300 shadow-lg shadow-[#36B2B2]/10">
                            Ajukan Tambahan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                    @else
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tidak ada kamar
                            aktif</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- History Section --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden mb-10" data-aos="fade-up">
            <div class="px-8 py-8 border-b border-gray-50 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-900 mb-1">Riwayat Layanan</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pantau status pengajuan Anda
                    </p>
                </div>
                <div class="flex items-center bg-gray-50 p-1.5 rounded-2xl gap-1">
                    <button @click="activeFilter = 'all'"
                        :class="activeFilter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-5 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300">Semua</button>
                    <button @click="activeFilter = 'aduan'"
                        :class="activeFilter === 'aduan' ? 'bg-white text-rose-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-5 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300">Aduan</button>
                    <button @click="activeFilter = 'tambah'"
                        :class="activeFilter === 'tambah' ? 'bg-white text-[#36B2B2] shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-5 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300">Tambah</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kategori
                            </th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Judul</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pesan</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tanggal
                            </th>
                            <th
                                class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($aduans as $aduan)
                            <tr x-show="activeFilter === 'all' || activeFilter === '{{ $aduan->kategori }}'" x-transition
                                class="group hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-6">
                                    <span
                                        class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest
                                                            {{ $aduan->kategori === 'fasilitas' ? 'bg-rose-50 text-rose-500' : 'bg-[#36B2B2]/10 text-[#36B2B2]' }}">
                                        {{ $aduan->kategori === 'fasilitas' ? 'Aduan' : 'Tambah' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-gray-800 max-w-[180px] truncate">{{ $aduan->judul }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <p
                                        class="text-sm font-bold text-gray-500 max-w-xs truncate group-hover:text-gray-700 transition-colors">
                                        "{{ $aduan->pesan }}"</p>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="text-xs font-bold text-gray-400">{{ $aduan->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($aduan->status === 'sudah_dibaca')
                                        <span
                                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600">Dibaca</span>
                                    @else
                                        <span
                                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600">Menunggu</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Belum ada riwayat
                                        pengajuan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="bg-[#36B2B2]/5 rounded-[2rem] p-6 border border-[#36B2B2]/10 flex items-center gap-4"
            data-aos="fade-up">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#36B2B2] shadow-sm shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-[10px] font-black text-gray-600 tracking-wide uppercase">Setiap aduan akan diproses maksimal
                dalam 2x24 jam kerja oleh pengelola kos.</p>
        </div>

        {{-- ===== ADUAN MODAL ===== --}}
        <template x-teleport="body">
            <div x-show="showAduanModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                style="display:none">

                <div @click.away="!isSubmitting && (showAduanModal = false)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl relative">

                    <div class="bg-gradient-to-r from-rose-500 to-rose-600 p-8 text-white relative">
                        <button @click="showAduanModal = false"
                            class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <h3 class="text-2xl font-black mb-1">Buat Aduan Baru 💬</h3>
                        <p class="text-white/70 text-sm font-medium">Sampaikan keluhan fasilitas Anda untuk segera kami
                            tangani.</p>
                    </div>

                    <form @submit.prevent="submitForm($event, 'aduan')" action="{{ route('user.fasilitas.aduan') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="id_kos" value="{{ $kosId }}">

                        <div class="p-8 space-y-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Pilih
                                    Fasilitas Bermasalah</label>
                                <select name="judul" required
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-rose-500/20 transition-all outline-none">
                                    <option value="" disabled selected>-- Pilih Fasilitas --</option>
                                    @forelse($facilities as $facility)
                                        <option value="Kendala {{ $facility->nama_fasilitas }}">{{ $facility->nama_fasilitas }}
                                        </option>
                                    @empty
                                        <option value="Kendala Fasilitas Umum">Kamar / Fasilitas Umum</option>
                                    @endforelse
                                    <option value="Kendala Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Detail
                                    Keluhan</label>
                                <textarea name="pesan" rows="4" required
                                    placeholder="Jelaskan masalah fasilitas secara detail di sini..."
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-rose-500/20 transition-all outline-none resize-none"></textarea>
                            </div>
                        </div>

                        <div class="p-8 pt-0 flex gap-4">
                            <button type="button" @click="showAduanModal = false"
                                class="flex-1 px-8 py-4 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmitting"
                                class="flex-[2] px-8 py-4 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-600 transition-all active:scale-95 shadow-lg shadow-rose-500/20 disabled:opacity-50">
                                <span x-show="!isSubmitting">Kirim Aduan →</span>
                                <span x-show="isSubmitting" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                    </svg>
                                    Mengirim...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ===== TAMBAH FASILITAS MODAL ===== --}}
        <template x-teleport="body">
            <div x-show="showTambahModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                style="display:none">

                <div @click.away="!isSubmitting && (showTambahModal = false)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl relative">

                    <div class="bg-gradient-to-r from-[#36B2B2] to-[#2D8E8E] p-8 text-white relative">
                        <button @click="showTambahModal = false"
                            class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <h3 class="text-2xl font-black mb-1">Ajukan Tambah Fasilitas ✨</h3>
                        <p class="text-white/70 text-sm font-medium">Sampaikan kebutuhan fasilitas tambahan untuk kenyamanan
                            Anda.</p>
                    </div>

                    <form @submit.prevent="submitForm($event, 'tambah')" action="{{ route('user.fasilitas.tambah') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="id_kos" value="{{ $kosId }}">

                        <div class="p-8 space-y-6">
                            <div>
                                <div class="space-y-4">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Fasilitas</label>
                                    <select name="judul" required
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all outline-none appearance-none cursor-pointer">
                                        <option value="" disabled selected>Pilih fasilitas yang Anda butuhkan...</option>
                                        @forelse($availableAddons as $addon)
                                            <option value="{{ $addon->nama_fasilitas }}">
                                                {{ $addon->nama_fasilitas }} (Rp
                                                {{ number_format($addon->harga_tambahan, 0, ',', '.') }}/bulan)
                                            </option>
                                        @empty
                                            <option value="" disabled>Belum ada fasilitas tambahan tersedia</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Alasan
                                    & Detail</label>
                                <textarea name="pesan" rows="4" required
                                    placeholder="Jelaskan mengapa Anda memerlukan fasilitas ini..."
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#36B2B2]/20 transition-all outline-none resize-none"></textarea>
                            </div>
                        </div>

                        <div class="p-8 pt-0 flex gap-4">
                            <button type="button" @click="showTambahModal = false"
                                class="flex-1 px-8 py-4 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmitting"
                                class="flex-[2] px-8 py-4 bg-[#36B2B2] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-[#2D8E8E] transition-all active:scale-95 shadow-lg shadow-[#36B2B2]/20 disabled:opacity-50">
                                <span x-show="!isSubmitting">Kirim Permintaan →</span>
                                <span x-show="isSubmitting" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                    </svg>
                                    Mengirim...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
@endsection
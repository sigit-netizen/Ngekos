@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="pb-12 text-gray-800">
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
                    Database Penghuni
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 leading-none tracking-tight">
                    Data <span class="text-[#36B2B2]">Penyewa</span> {{ $status === 'rejected' ? 'Ditolak' : 'Aktif' }}
                </h1>
                <p class="text-gray-500 mt-3 font-medium max-w-xl line-clamp-2">
                    {{ $status === 'rejected' 
                        ? 'Daftar calon penghuni yang pendaftarannya telah ditolak atau dibatalkan.' 
                        : 'Daftar seluruh penghuni yang saat ini menempati unit kamar di ' . ($kos->nama_kos ?? 'Kos Anda') . '.' }}
                </p>
            </div>
        </div>

        <!-- Status Tabs -->
        <div class="flex items-center gap-4 mb-8 bg-gray-100/50 p-1.5 rounded-[1.5rem] w-fit border border-gray-100" data-aos="fade-up">
            <a href="{{ route('admin.data_penyewa', ['status' => 'active']) }}" 
                class="px-6 py-2.5 rounded-[1.2rem] text-xs font-black uppercase tracking-widest transition-all duration-300 {{ $status === 'active' ? 'bg-white text-[#36B2B2] shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                Penyewa Aktif
            </a>
            <a href="{{ route('admin.data_penyewa', ['status' => 'rejected']) }}" 
                class="px-6 py-2.5 rounded-[1.2rem] text-xs font-black uppercase tracking-widest transition-all duration-300 {{ $status === 'rejected' ? 'bg-white text-red-500 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                Penyewa Ditolak
            </a>
        </div>

        @if($penyewas->isEmpty())
            <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-gray-100" data-aos="fade-up">
                <div
                    class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-8 border border-gray-100 group">
                    <svg class="w-12 h-12 text-gray-300 group-hover:text-[#36B2B2] transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Belum Ada Penyewa</h3>
                <p class="text-gray-500 mb-10 max-w-sm mx-auto font-medium">Saat ini belum ada penyewa yang terdaftar aktif di kos Anda.</p>
            </div>
        @else
            {{-- Desktop Table View --}}
            <div class="hidden md:block bg-white border-2 border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm mb-6" data-aos="fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Nama Penyewa</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Kontak & WA</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Unit Kamar</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Mulai Sewa</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($penyewas as $penyewa)
                                <tr class="hover:bg-gray-50/80 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-[#36B2B2]/10 text-[#36B2B2] flex items-center justify-center font-black text-lg border border-[#36B2B2]/20">
                                                {{ substr($penyewa->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 leading-tight">{{ $penyewa->name }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight mt-1">{{ $penyewa->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <span class="text-xs font-black text-gray-700">{{ $penyewa->nomor_wa }}</span>
                                            </div>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $penyewa->nomor_wa) }}" target="_blank"
                                                class="text-[10px] font-black text-[#36B2B2] hover:underline uppercase tracking-tighter">
                                                Hubungi WhatsApp →
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @if($penyewa->kamar)
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100/50">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <span class="text-xs font-black">Kamar {{ $penyewa->kamar->nomor_kamar }}</span>
                                            </div>
                                        @else
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest italic flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-200"></span>
                                                Tanpa Kamar
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-xs font-black text-gray-600 tracking-tight">{{ $penyewa->created_at->format('d M Y') }}</p>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{ $penyewa->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($status === 'rejected')
                                            <span class="px-5 py-2 bg-red-500 text-white rounded-xl text-[9px] font-black uppercase tracking-[0.2em] shadow-lg shadow-red-100 border border-red-400/20">DITOLAK</span>
                                        @else
                                            <span class="px-5 py-2 bg-emerald-500 text-white rounded-xl text-[9px] font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-100 border border-emerald-400/20">AKTIF</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile Card View --}}
            <div class="grid grid-cols-1 gap-5 md:hidden mb-10" data-aos="fade-up">
                @foreach($penyewas as $penyewa)
                    <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                        <!-- Background Accent -->
                        <div class="absolute top-0 right-0 w-32 h-32 {{ $status === 'rejected' ? 'bg-red-500/5' : 'bg-[#36B2B2]/5' }} rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                        
                        <div class="relative flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-2xl {{ $status === 'rejected' ? 'bg-red-50 text-red-500 border-red-100' : 'bg-[#36B2B2]/10 text-[#36B2B2] border-[#36B2B2]/20' }} flex items-center justify-center font-black text-xl border">
                                {{ substr($penyewa->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-black text-gray-900 text-lg leading-tight truncate">{{ $penyewa->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="w-2 h-2 rounded-full {{ $status === 'rejected' ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                                    <span class="text-[10px] font-black {{ $status === 'rejected' ? 'text-red-500' : 'text-emerald-600' }} uppercase tracking-widest">
                                        Penyewa {{ $status === 'rejected' ? 'Ditolak' : 'Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">No. WhatsApp</p>
                                <p class="text-xs font-black text-gray-800">{{ $penyewa->nomor_wa }}</p>
                            </div>
                            <div class="{{ $status === 'rejected' ? 'bg-red-50/50 border-red-100/50' : 'bg-indigo-50/50 border-indigo-100/50' }} rounded-2xl p-4 border">
                                <p class="text-[9px] font-black {{ $status === 'rejected' ? 'text-red-400' : 'text-indigo-400' }} uppercase tracking-widest mb-1.5">
                                    {{ $status === 'rejected' ? 'Alasan Ditolak' : 'Unit Kamar' }}
                                </p>
                                <p class="text-xs font-black {{ $status === 'rejected' ? 'text-red-600' : 'text-indigo-600' }}">
                                    @if($status === 'rejected')
                                        {{ $penyewa->keterangan ?? 'Data tidak sesuai' }}
                                    @else
                                        {{ property_exists($penyewa, 'kamar') && $penyewa->kamar ? 'KM ' . $penyewa->kamar->nomor_kamar : '-' }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3 bg-gray-50/50 rounded-2xl p-5 border border-gray-100">
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="font-black text-gray-400 uppercase tracking-widest">Email</span>
                                <span class="font-bold text-gray-700 truncate max-w-[180px]">{{ $penyewa->email }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="font-black text-gray-400 uppercase tracking-widest">
                                    {{ $status === 'rejected' ? 'Waktu Tolak' : 'Bergabung' }}
                                </span>
                                <span class="font-bold text-gray-700">
                                    {{ $penyewa->updated_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $penyewa->nomor_wa) }}" target="_blank"
                                class="w-full flex items-center justify-center gap-2 py-4 {{ $status === 'rejected' ? 'bg-gray-800' : 'bg-emerald-500' }} text-white font-black rounded-2xl shadow-lg active:scale-95 transition-all text-xs uppercase tracking-widest">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.395 0 .01 5.385.006 12.037c0 2.125.556 4.2 1.611 6.037L0 24l6.105-1.602a11.834 11.834 0 005.937 1.604h.005c6.654 0 12.04-5.385 12.044-12.037a11.823 11.823 0 00-3.489-8.412z"/>
                                </svg>
                                WhatsApp Penyewa
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($penyewas->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 rounded-b-[2.5rem]">
                    {{ $penyewas->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection

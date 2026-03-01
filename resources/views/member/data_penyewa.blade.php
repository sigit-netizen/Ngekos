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
                    Data <span class="text-[#36B2B2]">Penyewa</span> Aktif
                </h1>
                <p class="text-gray-500 mt-3 font-medium max-w-xl line-clamp-2">
                    Daftar seluruh penghuni yang saat ini menempati unit kamar di {{ $kos->nama_kos ?? 'Kos Anda' }}.
                </p>
            </div>
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
            <div class="bg-white border-2 border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm" data-aos="fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Nama Penyewa</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">No. WhatsApp</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Kamar</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Tanggal Bergabung</th>
                                <th class="px-8 py-6 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($penyewas as $penyewa)
                                <tr class="hover:bg-gray-50/80 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-[#36B2B2]/10 text-[#36B2B2] flex items-center justify-center font-black text-lg">
                                                {{ substr($penyewa->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900">{{ $penyewa->name }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $penyewa->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.395 0 .01 5.385.006 12.037c0 2.125.556 4.2 1.611 6.037L0 24l6.105-1.602a11.834 11.834 0 005.937 1.604h.005c6.654 0 12.04-5.385 12.044-12.037a11.823 11.823 0 00-3.489-8.412z"/>
                                                </svg>
                                            </span>
                                            <span class="font-bold text-gray-700">{{ $penyewa->nomor_wa }}</span>
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
                                            <span class="text-xs text-gray-400 italic">Belum assign kamar</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-xs font-bold text-gray-600 tracking-tight">{{ $penyewa->created_at->format('d M Y') }}</p>
                                        <p class="text-[9px] text-gray-400 font-medium">{{ $penyewa->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">Aktif</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($penyewas->hasPages())
                    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                        {{ $penyewas->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-sm border border-white/50 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6"
        data-aos="fade-up">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Manajemen Verifikasi & Order 📦</h1>
            <p class="text-gray-500 text-sm">Kelola verifikasi akun pemilik kos, akun penyewa, dan transaksi paket
                langganan.</p>
        </div>
    </div>

    @php
        $tab = request('tab', 'pending_member');
        $statusFilter = $statusFilter ?? 'active';
    @endphp

    <div x-data="{ activeTab: '{{ $tab }}', currentStatus: '{{ $statusFilter }}' }">
        <!-- Stats Cards (Tabs) - 3 cards per row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- 1. Verifikasi Akun Member (Admin) -->
            <button @click="activeTab = 'pending_member'; window.location.href = '?tab=pending_member'"
                class="relative p-6 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'pending_member' 
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' 
                    : 'bg-white border-gray-50 hover:border-[#36B2B2]/30 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'pending_member' ? 'bg-white/20 text-white rotate-12' : 'bg-[#36B2B2]/10 text-[#36B2B2] group-hover:rotate-12'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    @if($pendingMemberCount > 0)
                        <span class="flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-4 w-4 rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white shadow-sm"></span>
                        </span>
                    @endif
                </div>
                <h3 class="text-4xl font-black mb-1 transition-colors duration-500"
                    :class="activeTab === 'pending_member' ? 'text-white' : 'text-gray-900'">{{ $pendingMemberCount }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors duration-500"
                    :class="activeTab === 'pending_member' ? 'text-white/90' : 'text-[#36B2B2]'">Verif Akun Member</p>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            </button>

            <!-- 2. Verifikasi Akun User (Anak Kos) -->
            <button @click="activeTab = 'pending_user'; window.location.href = '?tab=pending_user'"
                class="relative p-6 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'pending_user' 
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' 
                    : 'bg-white border-gray-50 hover:border-[#36B2B2]/30 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'pending_user' ? 'bg-white/20 text-white rotate-12' : 'bg-[#36B2B2]/10 text-[#36B2B2] group-hover:rotate-12'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    @if($pendingUserCount > 0)
                        <span class="flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-4 w-4 rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white shadow-sm"></span>
                        </span>
                    @endif
                </div>
                <h3 class="text-4xl font-black mb-1 transition-colors duration-500" :class="activeTab === 'pending_user' ? 'text-white' : 'text-gray-900'">
                    {{ $pendingUserCount }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors duration-500"
                    :class="activeTab === 'pending_user' ? 'text-white/90' : 'text-[#36B2B2]'">Verifikasi Akun User</p>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            </button>


            <!-- 3. Verifikasi Paket -->
            <button @click="activeTab = 'pending_paket'; window.location.href = '?tab=pending_paket'"
                class="relative p-6 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'pending_paket' 
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' 
                    : 'bg-white border-gray-50 hover:border-[#36B2B2]/30 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'pending_paket' ? 'bg-white/20 text-white rotate-12' : 'bg-[#36B2B2]/10 text-[#36B2B2] group-hover:rotate-12'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    @if($pendingPacketCount > 0)
                        <span class="flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-4 w-4 rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white shadow-sm"></span>
                        </span>
                    @endif
                </div>
                <h3 class="text-4xl font-black mb-1 transition-colors duration-500"
                    :class="activeTab === 'pending_paket' ? 'text-white' : 'text-gray-900'">{{ $pendingPacketCount }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors duration-500"
                    :class="activeTab === 'pending_paket' ? 'text-white/90' : 'text-[#36B2B2]'">Verifikasi Paket</p>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            </button>

            <!-- 4. Akun Member Status (Active/Rejected) -->
            <button @click="activeTab = 'active_member'; window.location.href = '?tab=active_member&status=' + currentStatus"
                class="relative p-6 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'active_member' 
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' 
                    : 'bg-white border-gray-50 hover:border-[#36B2B2]/30 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'active_member' ? 'bg-white/20 text-white rotate-12' : 'bg-[#36B2B2]/10 text-[#36B2B2] group-hover:rotate-12'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 19c-4.418 0-8-3.582-8-8a8.001 8.001 0 0115.902-1.248l1.716 1.716a2 2 0 010 2.828l-4.243 4.243a2 2 0 01-2.828 0l-1.414-1.414">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-4xl font-black mb-1 transition-colors duration-500"
                    :class="activeTab === 'active_member' ? 'text-white' : 'text-gray-900'">
                    {{ $statusFilter === 'active' ? $activeMemberCount : $rejectedMemberCount }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors duration-500"
                    :class="activeTab === 'active_member' ? 'text-white/90' : 'text-[#36B2B2]'">Member
                    {{ $statusFilter === 'active' ? 'Terverifikasi' : 'Ditolak' }}</p>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            </button>


            <!-- 5. Akun User Status -->
            <button @click="activeTab = 'active_user'; window.location.href = '?tab=active_user&status=' + currentStatus"
                class="relative p-6 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'active_user' 
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' 
                    : 'bg-white border-gray-50 hover:border-[#36B2B2]/30 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'active_user' ? 'bg-white/20 text-white rotate-12' : 'bg-[#36B2B2]/10 text-[#36B2B2] group-hover:rotate-12'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-4xl font-black mb-1 transition-colors duration-500" :class="activeTab === 'active_user' ? 'text-white' : 'text-gray-900'">
                    {{ $statusFilter === 'active' ? $activeUserCount : $rejectedUserCount }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors duration-500"
                    :class="activeTab === 'active_user' ? 'text-white/90' : 'text-[#36B2B2]'">User
                    {{ $statusFilter === 'active' ? 'Terverifikasi' : 'Ditolak' }}</p>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            </button>

            <!-- 6. Paket Status -->
            <button @click="activeTab = 'active_paket'; window.location.href = '?tab=active_paket&status=' + currentStatus"
                class="relative p-6 rounded-3xl border-2 transition-all duration-500 text-left group overflow-hidden"
                :class="activeTab === 'active_paket' 
                    ? 'bg-[#36B2B2] border-[#2D8E8E] shadow-xl shadow-[#36B2B2]/40 -translate-y-1' 
                    : 'bg-white border-gray-50 hover:border-[#36B2B2]/30 shadow-md shadow-gray-200/50'">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-2xl transition-all duration-500"
                        :class="activeTab === 'active_paket' ? 'bg-white/20 text-white rotate-12' : 'bg-[#36B2B2]/10 text-[#36B2B2] group-hover:rotate-12'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-4xl font-black mb-1 transition-colors duration-500" :class="activeTab === 'active_paket' ? 'text-white' : 'text-gray-900'">
                    {{ $statusFilter === 'active' ? $activePacketCount : $rejectedPacketCount }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors duration-500"
                    :class="activeTab === 'active_paket' ? 'text-white/90' : 'text-[#36B2B2]'">Paket
                    {{ $statusFilter === 'active' ? 'Terverifikasi' : 'Ditolak' }}</p>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            </button>
        </div>

        <!-- Content Area -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden min-h-[500px]" data-aos="fade-up"
            data-aos-delay="100">

            <!-- Tab Header with Filter -->
            <div
                class="px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
                <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-6 bg-green-600 rounded-full"></span>
                    <span x-text="
                            activeTab === 'pending_member' ? 'Verifikasi Akun Pemilik Kos' :
                            activeTab === 'active_member' ? (currentStatus === 'active' ? 'Daftar Member Aktif' : 'Daftar Member Ditolak') :
                            activeTab === 'pending_user' ? 'Verifikasi Akun Penyewa (Anak Kos)' :
                            activeTab === 'active_user' ? (currentStatus === 'active' ? 'Daftar User Aktif' : 'Daftar User Ditolak') :
                            activeTab === 'pending_paket' ? 'Antrian Verifikasi Paket' :
                            (currentStatus === 'active' ? 'Riwayat Paket Berhasil' : 'Riwayat Paket Ditolak')
                        "></span>
                </h3>

                <!-- Status Filter for Verified Tabs -->
                <template x-if="['active_member', 'active_user', 'active_paket'].includes(activeTab)">
                    <div class="flex items-center p-1.5 bg-gray-100/50 backdrop-blur-md border border-gray-200 rounded-2xl shadow-inner">
                        <button
                            @click="currentStatus = 'active'; window.location.href = '?tab=' + activeTab + '&status=active'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300 transform active:scale-95"
                            :class="currentStatus === 'active' ? 'bg-green-500 text-white shadow-lg ' : 'text-gray-400 hover:text-green-600 hover:bg-white/50'">
                            DISETUJUI
                        </button>
                        <button
                            @click="currentStatus = 'rejected'; window.location.href = '?tab=' + activeTab + '&status=rejected'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300 transform active:scale-95"
                            :class="currentStatus === 'rejected' ? 'bg-red-500 text-white shadow-lg ' : 'text-gray-400 hover:text-red-600 hover:bg-white/50'">
                            DITOLA
                        </button>
                    </div>
                </template>
            </div>

            <!-- Tables Section -->
            <div class="p-0">
                <!-- 1. Pending Members Table -->
                <div x-show="activeTab === 'pending_member'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">Nama Pemilik</th>
                                    <th class="px-8 py-5">Kontak & NIK</th>
                                    <th class="px-8 py-5">Paket</th>
                                    <th class="px-8 py-5 text-center">Aksi Pemrosesan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($pendingMembers as $m)
                                    <tr class="group hover:bg-[#36B2B2]/5 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-black">
                                                    {{ substr($m->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ $m->name }}</div>
                                                    <div class="text-[10px] text-gray-400 font-medium">DAFTAR SEBAGAI PEMILIK
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="text-xs font-bold text-gray-600">{{ $m->email }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold">NIK: {{ $m->nik }}</div>
                                        </td>
                                        <td class="px-8 py-6">
                                            @php
                                                $planMap = [
                                                    'pro' => 'MEMBER PRO',
                                                    'premium' => 'MEMBER PREMIUM',
                                                    'pro_perkamar' => 'PER KAMAR PRO',
                                                    'premium_perkamar' => 'PER KAMAR PREMIUM'
                                                ];
                                                $planName = $planMap[$m->plan_type] ?? strtoupper($m->plan_type);
                                            @endphp
                                            <span class="px-3 py-1 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-[10px] font-black uppercase tracking-tighter">
                                                {{ $planName }} {{ $m->package_type ? '(' . $m->package_type . ')' : '' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center justify-center gap-3">
                                                <form action="{{ route('superadmin.order.user.verify', $m->id) }}" method="POST"
                                                    class="inline"> @csrf
                                                    <button type="submit"
                                                        class="px-5 py-2.5 bg-green-50 text-green-700 rounded-xl text-xs font-black border border-green-200 hover:bg-green-600 hover:text-white transition-all hover:scale-105 active:scale-95 shadow-sm shadow-green-600/10">
                                                        SETUJUI
                                                    </button>
                                                </form>
                                                <button type="button"
                                                    @click="$dispatch('open-reject-modal', { id: {{ $m->id }}, name: '{{ $m->name }}', nik: '{{ $m->nik }}' })"
                                                    class="px-5 py-2.5 bg-red-50 hover:bg-red-500 text-red-500 hover:text-white rounded-xl text-xs font-black border border-red-100 transition-all hover:-translate-y-0.5">
                                                    TOLAK
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-20 text-center text-gray-400 text-sm font-medium">Bagus!
                                            Tidak ada antrian verifikasi member.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $pendingMembers->links() }}</div>
                </div>

                <!-- 2. Active/Rejected Members Table -->
                <div x-show="activeTab === 'active_member'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">Nama Member</th>
                                    <th class="px-8 py-5">Kontak</th>
                                    <th class="px-8 py-5">Plan</th>
                                    <th class="px-8 py-5 text-center">{{ $statusFilter === 'rejected' ? 'Alasan Penolakan' : 'Status Keanggotaan' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($activeMembers as $m)
                                    <tr class="group hover:bg-emerald-50 transition-colors">
                                        <td class="px-8 py-6 font-black text-gray-900">{{ $m->name }}</td>
                                        <td class="px-8 py-6 text-xs font-bold text-gray-500">{{ $m->email }}</td>
                                        <td class="px-8 py-6">
                                            @if($statusFilter === 'rejected')
                                                @php
                                                    $planMap = ['pro' => 'MEMBER PRO', 'premium' => 'MEMBER PREMIUM', 'pro_perkamar' => 'PER KAMAR PRO', 'premium_perkamar' => 'PER KAMAR PREMIUM'];
                                                @endphp
                                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black">{{ $planMap[$m->plan_type] ?? '-' }}</span>
                                            @else
                                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black">{{ $m->getPlanName() }}</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @if($statusFilter === 'rejected')
                                                <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-red-100 text-red-600 block max-w-xs mx-auto truncate" title="{{ $m->rejection_reason }}">
                                                    {{ $m->rejection_reason ?? 'REJECTED' }}
                                                </span>
                                            @else
                                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">
                                                    APPROVED
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-20 text-center text-gray-400 text-sm italic">Data tidak
                                            ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $activeMembers->links() }}</div>
                </div>

                <!-- 3. Pending Users Table (Anak Kos) -->
                <div x-show="activeTab === 'pending_user'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">Nama Penyewa</th>
                                    <th class="px-8 py-5">Kontak</th>
                                    <th class="px-8 py-5 text-center">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($pendingUsers as $u)
                                    <tr class="group hover:bg-blue-50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="font-black text-gray-900 uppercase tracking-tighter">{{ $u->name }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 font-bold text-gray-400 text-xs">{{ $u->email }}</td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('superadmin.order.user.verify', $u->id) }}"
                                                    method="POST"> @csrf
                                                    <button type="submit"
                                                        class="px-6 py-2.5 bg-green-50 text-green-700 rounded-xl text-xs font-black border border-green-200 hover:bg-green-600 hover:text-white transition-all hover:scale-105 active:scale-95 shadow-sm shadow-green-600/10">SETUJUI</button>
                                                </form>
                                                <button type="button"
                                                    @click="$dispatch('open-reject-modal', { id: {{ $u->id }}, name: '{{ $u->name }}', nik: '{{ $u->nik }}' })"
                                                    class="px-6 py-2.5 bg-red-50 text-red-500 rounded-xl text-xs font-black border border-red-100 hover:bg-red-500 hover:text-white transition-all">TOLAK</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-8 py-20 text-center text-gray-400">Semua penyewa sudah
                                            terdata.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
                        @if($pendingUsers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $pendingUsers->links() }}
                        @endif
                    </div>
                </div>

                <!-- 4. Active/Rejected User Table -->
                <div x-show="activeTab === 'active_user'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">Nama Penyewa</th>
                                    <th class="px-8 py-5">Kontak</th>
                                    <th class="px-8 py-5 text-center">{{ $statusFilter === 'rejected' ? 'Alasan Penolakan' : 'Status' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($activeUsers as $u)
                                    <tr class="group hover:bg-emerald-50 transition-colors">
                                        <td class="px-8 py-6 font-black text-gray-900">{{ $u->name }}</td>
                                        <td class="px-8 py-6 text-xs text-gray-500 font-bold">{{ $u->email }}</td>
                                        <td class="px-8 py-6 text-center">
                                            @if($statusFilter === 'rejected')
                                                <span class="px-3 py-1 rounded-full text-[9px] font-black bg-red-100 text-red-600 block max-w-xs mx-auto truncate" title="{{ $u->rejection_reason }}">
                                                    {{ $u->rejection_reason ?? 'REJECTED' }}
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-600">
                                                    VERIFIED
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-8 py-20 text-center text-gray-400">Tidak ada data untuk status
                                            ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $activeUsers->links() }}</div>
                </div>

                <!-- 5. Pending Packets Table -->
                <div x-show="activeTab === 'pending_paket'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">Pemilik Kos</th>
                                    <th class="px-8 py-5">Paket Yang Dipilih</th>
                                    <th class="px-8 py-5">Jumlah Kamar</th>
                                    <th class="px-8 py-5 text-center">Aksi Token</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($pendingPackets as $sub)
                                    <tr class="group hover:bg-purple-50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="font-black text-gray-900">{{ $sub->user->name ?? 'N/A' }}</div>
                                            <div class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">
                                                {{ $sub->user->email ?? '' }}</div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                                <span
                                                    class="font-black text-purple-700 uppercase tracking-tighter text-xs">{{ $sub->jenis_langganan->nama ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 font-bold text-gray-600 text-xs">
                                            {{ $sub->jumlah_kamar ? $sub->jumlah_kamar . ' Kamar' : '-' }}</td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('superadmin.order.verify', $sub->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-6 py-2.5 bg-green-50 text-green-700 rounded-xl text-xs font-black border border-green-200 hover:bg-green-600 hover:text-white transition-all active:scale-95 hover:scale-105 shadow-sm shadow-green-600/10">AKTIFKAN</button>
                                                </form>
                                                <form action="{{ route('superadmin.order.reject', $sub->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-6 py-2.5 bg-red-50 text-red-500 rounded-xl text-xs font-black border border-red-50 hover:bg-red-500 hover:text-white transition-all">TOLAK</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-20 text-center text-gray-400">Pembayaran paket sudah
                                            diverifikasi semua.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
                        @if($pendingPackets instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $pendingPackets->links() }}
                        @endif
                    </div>
                </div>

                <!-- 6. Active/Rejected Packet Table -->
                <div x-show="activeTab === 'active_paket'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50/50">
                                    <th class="px-8 py-5">Pemilik Kos</th>
                                    <th class="px-8 py-5">Paket</th>
                                    <th class="px-8 py-5">Status Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($activePackets as $sub)
                                    <tr class="group hover:bg-emerald-50 transition-colors">
                                        <td class="px-8 py-6 font-black text-gray-900">{{ $sub->user->name ?? 'N/A' }}</td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="text-xs font-bold text-gray-500 uppercase">{{ $sub->jenis_langganan->nama ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span
                                                class="px-3 py-1 rounded-lg text-[9px] font-black tracking-[0.1em] {{ $sub->status === 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                                {{ $sub->status === 'active' ? 'SUCCESS' : 'DECLINED' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-8 py-20 text-center text-gray-400 font-medium">Data kosong.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">{{ $activePackets->links() }}</div>
                </div>

            </div>
        </div>
    </div>

    {{-- Rejection Reason Modal --}}
    <div x-data="{ open: false, rejectId: null, rejectName: '', rejectNik: '' }"
         @open-reject-modal.window="open = true; rejectId = $event.detail.id; rejectName = $event.detail.name; rejectNik = $event.detail.nik"
         x-show="open" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="open = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        {{-- Modal Content --}}
        <div x-show="open" x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">

            {{-- Header --}}
            <div class="p-6 border-b border-gray-100 bg-red-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Tolak Pendaftaran</h3>
                        <p class="text-xs text-gray-500">Berikan alasan penolakan untuk <span class="font-bold text-red-600" x-text="rejectName"></span></p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <form :action="`{{ url('superadmin/order/user') }}/` + rejectId + `/reject`" method="POST" class="p-6 space-y-4">
                @csrf

                {{-- Info --}}
                <div class="bg-gray-50 rounded-2xl p-4 space-y-2">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-400 font-bold">Nama:</span>
                        <span class="text-gray-700 font-black" x-text="rejectName"></span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-400 font-bold">NIK:</span>
                        <span class="text-gray-700 font-black" x-text="rejectNik"></span>
                    </div>
                </div>

                {{-- Rejection Reason --}}
                <div>
                    <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-wider">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <select name="rejection_reason" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm text-gray-800 focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-all appearance-none cursor-pointer"
                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.25rem;">
                        <option value="" disabled selected>-- Pilih Alasan --</option>
                        <option value="Data NIK tidak valid / tidak sesuai">Data NIK tidak valid / tidak sesuai</option>
                        <option value="Nama tidak sesuai dengan KTP">Nama tidak sesuai dengan KTP</option>
                        <option value="Nomor WhatsApp tidak aktif">Nomor WhatsApp tidak aktif</option>
                        <option value="Data duplikat / sudah terdaftar">Data duplikat / sudah terdaftar</option>
                        <option value="Alamat tidak lengkap atau tidak jelas">Alamat tidak lengkap atau tidak jelas</option>
                        <option value="Dokumen pendukung tidak valid">Dokumen pendukung tidak valid</option>
                        <option value="Paket yang dipilih tidak tersedia">Paket yang dipilih tidak tersedia</option>
                        <option value="Alasan lainnya">Alasan lainnya</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" @click="open = false"
                        class="flex-1 py-3 text-gray-600 font-bold rounded-2xl border border-gray-200 hover:bg-gray-50 transition-all text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-red-500 text-white font-black rounded-2xl hover:bg-red-600 transition-all text-sm shadow-lg shadow-red-500/30 active:scale-95">
                        Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
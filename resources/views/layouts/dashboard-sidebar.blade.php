<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white/90 backdrop-blur-xl border-r border-gray-100 shadow-sm transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col">

    <!-- Logo -->
    <div class="flex items-center justify-center h-20 border-b border-gray-100 shrink-0 px-6">
        <a href="/" class="flex items-center hover:opacity-80 transition-opacity">
            <img src="/images/logo/logo.svg" alt="Logo" class="h-10 w-auto" />
        </a>

        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="lg:hidden ml-auto p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
        @php
            $user = auth()->user();
            $planName = $user->getPlanName();

            // Calculate Status for Admin Sidebar Badge
            $sidebarStatus = 'active';
            $latestSub = \App\Models\Langganan::where('id_user', $user->id)->where('status', 'active')->latest()->first();
            if ($latestSub && !($user->hasRole('superadmin') || $user->id_plans == 6)) {
                $expiryDate = $latestSub->jatuh_tempo ? \Carbon\Carbon::parse($latestSub->jatuh_tempo) : \Carbon\Carbon::parse($latestSub->tanggal_pembayaran)->addDays(30);
                $nowWib = now('Asia/Jakarta')->startOfDay();
                $expiryWib = $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay();
                $diffDays = (int) $nowWib->diffInDays($expiryWib, false);

                if ($diffDays < 0) {
                    $sidebarStatus = ($diffDays >= -3) ? 'grace' : 'inactive';
                }
            }
        @endphp

        <!-- Subscription Badge (Specific for Admin) -->
        @if (($role ?? 'user') == 'admin')
            <div class="px-4 mb-6">
                <div class="p-3 rounded-2xl border transition-all duration-300
                            @if($sidebarStatus == 'active') bg-[#36B2B2]/5 border-[#36B2B2]/10 
                            @elseif($sidebarStatus == 'grace') bg-amber-50 border-amber-100 
                            @else bg-red-50 border-red-100 @endif">

                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-2 h-2 rounded-full 
                                    @if($sidebarStatus == 'active') bg-[#36B2B2] 
                                    @elseif($sidebarStatus == 'grace') bg-amber-500 animate-pulse 
                                    @else bg-red-500 @endif">
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider
                                    @if($sidebarStatus == 'active') text-[#36B2B2] 
                                    @elseif($sidebarStatus == 'grace') text-amber-700 
                                    @else text-red-700 @endif">
                            {{ $planName }}
                            @if($sidebarStatus == 'active') ACTIVE
                            @elseif($sidebarStatus == 'grace') GRACE PERIOD
                            @else MATI @endif
                        </span>
                    </div>
                </div>
            </div>
        @endif

        @if (($role ?? 'user') == 'superadmin')
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Super Admin</p>

            <!-- Dashboard -->
            <a href="{{ route('superadmin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin') || request()->is('superadmin/dashboard') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin') || request()->is('superadmin/dashboard') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                </div>
                Dashboard
            </a>

            <!-- Data Member -->
            <a href="{{ route('superadmin.data_member') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/data-member*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/data-member*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                Data Member
            </a>

            <!-- Data User -->
            <a href="{{ route('superadmin.data_user') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/data-user*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/data-user*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                        </path>
                    </svg>
                </div>
                Data User
            </a>

            <!-- Laporan Pembayaran -->
            @php
                $inactiveCount = \App\Models\Langganan::with(['user.statusUser'])->whereNotNull('tanggal_pembayaran')
                    ->get()
                    ->filter(function ($sub) {
                        // Exclude users already officially deactivated
                        if ($sub->user && $sub->user->statusUser && $sub->user->statusUser->status === 'inactive') {
                            return false;
                        }
                        // Using jatuh_tempo if available, standardized to 30 days fallback
                        $expiryDate = $sub->jatuh_tempo ? \Carbon\Carbon::parse($sub->jatuh_tempo) : \Carbon\Carbon::parse($sub->tanggal_pembayaran)->addDays(30);

                        // WIB Reset Logic
                        $nowWib = now('Asia/Jakarta')->startOfDay();
                        $expiryWib = $expiryDate->copy()->timezone('Asia/Jakarta')->startOfDay();

                        $diff = (int) $nowWib->diffInDays($expiryWib, false);

                        // "Mati" is defined as package expired for more than 3 days
                        return $diff < -3;
                    })->count();
            @endphp
            <a href="{{ route('superadmin.laporan_pembayaran') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/laporan-pembayaran*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/laporan-pembayaran*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    @if($inactiveCount > 0)
                        <span
                            class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">
                            {{ $inactiveCount }}
                        </span>
                    @endif
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Laporan Pembayaran</span>
                    @if($inactiveCount > 0)
                        <span
                            class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 border border-red-100 italic">
                            {{ $inactiveCount }} Mati
                        </span>
                    @endif
                </div>
            </a>

            <!-- Order -->
            @php
                $pendingPackets = \App\Models\Langganan::where('status', 'pending')->count();
                // Add PendingUser counts from staging table (only status=pending)
                $pendingRegistrations = \App\Models\PendingUser::where('status', 'pending')->count();
                $pendingCount = $pendingPackets + $pendingRegistrations;
            @endphp
            <a href="{{ route('superadmin.order') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/order*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/order*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                        </path>
                    </svg>
                    @if($pendingCount > 0)
                        <span
                            class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white animate-bounce">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Order</span>
                    @if($pendingCount > 0)
                        <span
                            class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 border border-red-100 italic">
                            {{ $pendingCount }} Pending
                        </span>
                    @endif
                </div>
            </a>

            <!-- Permission -->
            <a href="{{ route('superadmin.permission') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/permission*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/permission*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                Permission
            </a>

            <!-- Aduan Section -->
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Aduan</p>

            <!-- Aduan Member -->
            <a href="{{ route('superadmin.aduan.member') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/aduan/member*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/aduan/member*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Aduan Member</span>
                    <span
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 border border-red-100">12
                        Baru</span>
                </div>
            </a>

            <!-- Aduan User -->
            <a href="{{ route('superadmin.aduan.user') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/aduan/user*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/aduan/user*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Aduan User</span>
                    <span
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 border border-red-100">11
                        Baru</span>
                </div>
            </a>

            <!-- Aduan Publik -->
            <a href="{{ route('superadmin.aduan.publik') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin/aduan/publik*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin/aduan/publik*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Aduan Publik</span>
                    <span
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 border border-red-100">11
                        Baru</span>
                </div>
            </a>

        @elseif (($role ?? 'user') == 'admin' || Auth::user()->hasRole('nonaktif'))
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Admin Panel</p>

            <!-- Dashboard -->
            @can('menu.dashboard')
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin') || request()->is('admin/dashboard') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin') || request()->is('admin/dashboard') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </div>
                    Dashboard
                </a>
            @endcan

            <!-- Kamar -->
            @can('menu.kamar')
                <a href="{{ route('admin.kamar') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/kamar*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin/kamar*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    Kamar
                </a>
            @endcan
            <!-- Order -->
            @can('menu.order')
                <a href="{{ route('admin.order') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/order*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin/order*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                            </path>
                        </svg>
                    </div>
                    Order
                </a>
            @endcan

            <!-- Tambah Data Penyewa -->
            @can('menu.data_penyewa')
                <a href="{{ route('admin.data_penyewa') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/data-penyewa*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin/data-penyewa*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                            </path>
                        </svg>
                    </div>
                    Data Penyewa
                </a>
            @endcan

            <!-- Cabang Kos -->
            @can('menu.cabang_kos')
                <a href="{{ route('admin.cabang_kos') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/cabang-kos*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin/cabang-kos*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    Cabang Kos
                </a>
            @endcan

            <!-- Aduan -->
            @can('menu.pesan_aduan')
                <a href="{{ route('admin.pesan_aduan') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/pesan-aduan*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin/pesan-aduan*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    Pesan Aduan
                </a>
            @endcan

            <!-- Laporan Pembayaran -->
            @can('menu.laporan_pembayaran')
                <a href="{{ route('admin.laporan_pembayaran') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/laporan-pembayaran*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin/laporan-pembayaran*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    Laporan Pembayaran
                </a>
            @endcan


            <!-- Tagihan App -->
            @can('menu.tagihan_sistem')
                @php
                    $memberSub = \App\Models\Langganan::where('id_user', Auth::id())->latest()->first();
                    $mExpiry = $memberSub?->jatuh_tempo ? \Carbon\Carbon::parse($memberSub->jatuh_tempo) : ($memberSub?->tanggal_pembayaran ? \Carbon\Carbon::parse($memberSub->tanggal_pembayaran)->addDays(30) : null);
                    $mNow = now('Asia/Jakarta')->startOfDay();
                    $mExpWib = $mExpiry ? $mExpiry->copy()->timezone('Asia/Jakarta')->startOfDay() : null;
                    $mDiff = $mExpWib ? (int) $mNow->diffInDays($mExpWib, false) : 0;

                    $memberStatus = 'active';
                    if ($mDiff < 0) {
                        $memberStatus = ($mDiff >= -3) ? 'grace' : 'inactive';
                    }
                @endphp
                <a href="{{ route('admin.tagihan_sistem') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/tagihan-sistem*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('admin/tagihan-sistem*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} transition-colors p-1.5 rounded-lg relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        @if($memberStatus == 'grace')
                            <span
                                class="absolute -top-1 -right-1 w-3 h-3 bg-amber-500 border-2 border-white rounded-full animate-bounce"></span>
                        @elseif($memberStatus == 'inactive')
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                        @endif
                    </div>
                    <div class="flex-1 flex items-center justify-between">
                        <span>Tagihan sistem</span>
                        @if($memberStatus == 'grace')
                            <span
                                class="text-[9px] font-black px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tighter">Masa
                                Tenggang</span>
                        @elseif($memberStatus == 'inactive')
                            <span
                                class="text-[9px] font-black px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 border border-red-100 uppercase tracking-tighter">Mati</span>
                        @endif
                    </div>
                </a>
            @endcan

            <!-- Menu Dinamis Admin Baru -->
            @php
                $adminDynamicPath = resource_path('views/member');
                $dynamicAdminMenus = [];
                if (\Illuminate\Support\Facades\File::exists($adminDynamicPath)) {
                    foreach (\Illuminate\Support\Facades\File::files($adminDynamicPath) as $file) {
                        $name = str_replace('.blade.php', '', $file->getFilename());
                        if (!in_array($name, ['dashboard', 'kamar', 'order', 'data_penyewa', 'cabang_kos', 'pesan_aduan', 'laporan_pembayaran', 'tagihan_sistem'])) {
                            $dynamicAdminMenus[] = $name;
                        }
                    }
                }
            @endphp

            @foreach($dynamicAdminMenus as $menu)
                @can('menu.' . $menu)
                    <a href="{{ url('admin/' . $menu) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/' . $menu . '*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                        <div
                            class="{{ request()->is('admin/' . $menu . '*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 transition-colors relative rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        {{ ucwords(str_replace(['_', '-'], ' ', $menu)) }}
                    </a>
                @endcan
            @endforeach

        @else
            <!-- Dashboard -->
            @can('menu.dashboard')
                <a href="{{ route('user.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('user') || request()->is('user/dashboard') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('user') || request()->is('user/dashboard') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </div>
                    Dashboard
                </a>
            @endcan
            <!-- Order -->
            @can('menu.order')
                <a href="{{ route('user.order') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('user/order*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('user/order*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 transition-colors rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                            </path>
                        </svg>
                    </div>
                    Order
                </a>
            @endcan

            <!-- Jatuh Tempo -->
            @can('menu.jatuh_tempo')
                <a href="{{ route('user.jatuh_tempo') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('user/jatuh-tempo*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('user/jatuh-tempo*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 transition-colors rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Jatuh Tempo
                </a>
            @endcan

            <!-- Aduan Fasilitas -->
            @can('menu.aduan')
                <a href="{{ route('user.aduan') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('user/aduan*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                    <div
                        class="{{ request()->is('user/aduan*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 transition-colors relative rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    Fasilitas
                </a>
            @endcan

            <!-- Menu Dinamis User Baru -->
            @php
                $userDynamicPath = resource_path('views/user');
                $dynamicUserMenus = [];
                if (\Illuminate\Support\Facades\File::exists($userDynamicPath)) {
                    foreach (\Illuminate\Support\Facades\File::files($userDynamicPath) as $file) {
                        $name = str_replace('.blade.php', '', $file->getFilename());
                        if (!in_array($name, ['dashboard', 'order', 'jatuh_tempo', 'aduan', 'pesan'])) {
                            $dynamicUserMenus[] = $name;
                        }
                    }
                }
            @endphp

            @foreach($dynamicUserMenus as $menu)
                @can('menu.' . $menu)
                    <a href="{{ url('user/' . $menu) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('user/' . $menu . '*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                        <div
                            class="{{ request()->is('user/' . $menu . '*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 transition-colors relative rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        {{ ucwords(str_replace(['_', '-'], ' ', $menu)) }}
                    </a>
                @endcan
            @endforeach

        @endif
    </nav>

    <!-- Sidebar Footer / Logout Component -->
    <div class="p-4 border-t border-gray-100 shrink-0 bg-gray-50/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 hover:text-red-700 font-bold transition-all duration-300 group shadow-sm bg-white border border-red-100">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>
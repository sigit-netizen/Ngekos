<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white/90 backdrop-blur-xl border-r border-gray-100 shadow-sm transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col">

    <!-- Logo -->
    <div class="flex items-center justify-center h-20 border-b border-gray-100 shrink-0 px-6">
        <a href="/" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <span class="font-extrabold text-2xl text-gray-900 tracking-tight">
                K<span class="text-[#36B2B2] ml-0.5 font-semibold">Ngekos</span><span
                    class="text-gray-400 font-light text-xl">.id</span>
            </span>
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

        @if (($role ?? 'user') == 'superadmin')
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Super Admin</p>

            <!-- Dashboard -->
            <a href="{{ route('superadmin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('superadmin*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('superadmin*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                </div>
                Dashboard
            </a>

            <!-- Data Member -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                Data Member
            </a>

            <!-- Laporan Pembayaran -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                Laporan Pembayaran
            </a>

            <!-- Permission -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                Permission
            </a>

        @elseif (($role ?? 'user') == 'admin')
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Admin Panel</p>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('admin*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                </div>
                Dashboard
            </a>

            <!-- Kamar -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                Kamar
            </a>

            <!-- Laporan Pembayaran -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                Laporan Pembayaran
            </a>

        @else
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Utama</p>

            <!-- Dashboard -->
            <a href="{{ route('user.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('user*') ? 'bg-[#36B2B2]/10 text-[#36B2B2] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium' }} transition-colors group">
                <div
                    class="{{ request()->is('user*') ? 'bg-[#36B2B2] text-white' : 'text-gray-400 group-hover:text-[#36B2B2]' }} p-1.5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                </div>
                Dashboard
            </a>
            <!-- Pesan -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5 relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                    <span class="absolute top-1 right-0 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </div>
                Pesan
            </a>

            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Aktivitas</p>
            </div>

            <!-- Jatuh Tempo -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                Jatuh Tempo
            </a>

            <!-- Aduan Fasilitas -->
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-[#36B2B2] font-medium transition-colors group">
                <div class="text-gray-400 group-hover:text-[#36B2B2] transition-colors p-1.5 relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                Aduan Fasilitas
            </a>
        @endif
    </nav>

    <!-- Sidebar Footer / Logout Component -->
    <div class="p-4 border-t border-gray-100 shrink-0 bg-gray-50/50">
        <form method="POST" action="#">
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
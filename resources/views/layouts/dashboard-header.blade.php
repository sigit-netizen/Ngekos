<header x-data="{ open: false }"
    class="h-20 bg-white/60 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-20">

    <div class="flex items-center gap-4">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = true"
            class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 focus:outline-none transition-colors border border-gray-200 bg-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <!-- Page Title -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 hidden sm:block">
                {{ $title ?? 'Dashboard' }}
            </h2>
        </div>
    </div>

    <!-- Quick Actions & Profile -->
    <div class="flex items-center gap-4">

        <!-- Profile Dropdown -->
        <div class="relative">
            <button @click="open = !open" @click.outside="open = false"
                class="flex items-center gap-3 p-1.5 pr-3 rounded-full hover:bg-white bg-white/50 border border-gray-100 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#36B2B2]/30">
                <img class="h-8 w-8 rounded-full object-cover border-2 border-white shadow-sm"
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=36B2B2&color=fff"
                    alt="{{ auth()->user()->name ?? 'User' }} Avatar">
                <span
                    class="hidden md:block text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'User' }}</span>
                <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                    </path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute right-0 mt-3 w-64 rounded-2xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden isolate"
                style="display: none;">
                <div class="px-4 py-4 border-b border-gray-100 bg-gradient-to-br from-gray-50 to-white">
                    <p class="text-sm font-black text-gray-900 tracking-tight">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-[10px] text-[#36B2B2] font-bold truncate mt-0.5 uppercase tracking-wider">
                        {{ auth()->user()->email ?? '' }}
                    </p>
                </div>
                <div class="p-2 space-y-1">
                    @can('menu.profil')
                        <button @click="$store.profile.open(); open = false"
                            class="flex w-full items-center gap-3 px-3 py-2.5 text-sm font-bold text-gray-700 rounded-xl hover:bg-[#36B2B2]/10 hover:text-[#36B2B2] transition-colors text-left group">
                            <div
                                class="p-1.5 rounded-lg bg-gray-50 group-hover:bg-[#36B2B2] group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            Profil Saya
                        </button>
                    @endcan

                    <div class="border-t border-gray-100 my-1 mx-2"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-600 rounded-xl hover:bg-red-50 hover:text-red-700 transition-colors text-left group">
                            <div
                                class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-600 group-hover:text-white transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                            </div>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

@push('modals')
    <!-- Global Profile Modal (Root Level) -->
    <div x-cloak x-data="{ 
                                    verifyPassword: '', 
                                    isVerifying: false, 
                                    errorMsg: '',
                                    mode: 'verify', // 'verify' or 'profile'
                                    async handleVerify() {
                                        this.isVerifying = true;
                                        this.errorMsg = '';
                                        try {
                                            const response = await fetch('{{ route('profile.verify-password') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ password: this.verifyPassword })
                                            });
                                            const data = await response.json();
                                            if (data.success) {
                                                this.mode = 'profile';
                                                this.verifyPassword = '';
                                            } else {
                                                this.errorMsg = data.message;
                                            }
                                        } catch (e) {
                                            this.errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                                        } finally {
                                            this.isVerifying = false;
                                        }
                                    },
                                    reset() {
                                        this.mode = 'verify';
                                        this.verifyPassword = '';
                                        this.errorMsg = '';
                                    }
                                }" x-show="$store.profile.isOpen"
        x-init="document.body.appendChild($el); $watch('$store.profile.isOpen', value => { if(!value) reset() })"
        x-effect="document.body.style.overflow = $store.profile.isOpen ? 'hidden' : ''"
        style="position: fixed; z-index: 2147483647;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 overflow-y-auto">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-md"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh;"
            @click="$store.profile.close()"></div>

        <!-- Password Verification Content -->
        <div x-show="mode === 'verify'" class="relative min-h-screen flex items-center justify-center p-4">
            <div x-show="mode === 'verify'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 p-8">

                <div class="text-center mb-6">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#36B2B2]/10 text-[#36B2B2] mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Verifikasi Keamanan</h3>
                    <p class="text-sm text-gray-500 mt-2">Untuk melanjutkan, masukkan kata sandi akun Anda.</p>
                </div>

                <form @submit.prevent="handleVerify()" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Kata Sandi</label>
                        <input type="password" x-model="verifyPassword" required
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400"
                            placeholder="••••••••">
                        <p x-show="errorMsg" x-text="errorMsg" class="text-xs text-red-500 mt-1 ml-1"
                            style="display: none;"></p>
                    </div>

                    <div class="flex flex-col gap-3 pt-2">
                        <button type="submit" :disabled="isVerifying"
                            class="w-full px-6 py-3 rounded-xl text-sm font-semibold text-white bg-[#36B2B2] hover:bg-[#2d9696] shadow-md shadow-[#36B2B2]/20 transition-all flex items-center justify-center gap-2">
                            <span x-show="!isVerifying">Verifikasi Selesaikan</span>
                            <svg x-show="isVerifying" class="animate-spin h-4 w-4 text-white" fill="none"
                                viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-show="isVerifying" style="display: none;">Memverifikasi...</span>
                        </button>
                        <button type="button" @click="$store.profile.close()"
                            class="w-full px-6 py-3 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profile Modal Content -->
        <div x-show="mode === 'profile'" class="relative min-h-screen flex items-center justify-center p-4">
            <div x-show="mode === 'profile'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">

                <!-- Header -->
                <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-white">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Pengaturan Profil</h3>
                        <p class="text-xs font-medium text-gray-500 mt-1">Kelola informasi data diri dan keamanan akun Anda
                        </p>
                    </div>
                    <button type="button" @click="$store.profile.close()"
                        class="p-2 rounded-xl text-gray-400 hover:bg-gray-50 hover:text-gray-700 transition-all focus:outline-none focus:ring-2 focus:ring-[#36B2B2]/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('profile.update') }}" method="POST" class="p-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Nama Lengkap
                            </label>
                            <input type="text" name="name" value="{{ auth()->user()->name }}" required
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-[#36B2B2] focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm @cannot('fitur.edit_profile') opacity-75 cursor-not-allowed @endcannot">
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Alamat Email
                            </label>
                            <input type="email" name="email" value="{{ auth()->user()->email }}" required
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-[#36B2B2] focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm @cannot('fitur.edit_profile') opacity-75 cursor-not-allowed @endcannot">
                        </div>

                        <!-- NIK -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 1 4 3" />
                                </svg>
                                Nomor Induk Kependudukan (NIK)
                            </label>
                            <input type="text" name="nik" value="{{ auth()->user()->nik }}"
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-[#36B2B2] focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm @cannot('fitur.edit_profile') opacity-75 cursor-not-allowed @endcannot"
                                placeholder="16 digit NIK">
                        </div>

                        <!-- WA -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                WhatsApp (Aktif)
                            </label>
                            <input type="text" name="nomor_wa" value="{{ auth()->user()->nomor_wa }}"
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-[#36B2B2] focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm @cannot('fitur.edit_profile') opacity-75 cursor-not-allowed @endcannot"
                                placeholder="0812xxxx">
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#36B2B2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Alamat Domisili
                            </label>
                            <textarea name="alamat" rows="2" @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-[#36B2B2] focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm resize-none @cannot('fitur.edit_profile') opacity-75 cursor-not-allowed @endcannot"
                                placeholder="Masukan alamat lengkap Anda...">{{ auth()->user()->alamat }}</textarea>
                        </div>
                        <br>
                        <!-- Social Media Divider -->
                        <div class="md:col-span-2 flex items-center gap-4 mt-6 sm:mt-8 mb-2">
                            <div class="h-px flex-1 bg-gray-100"></div>
                            <span
                                class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Media
                                Sosial</span>
                            <div class="h-px flex-1 bg-gray-100"></div>
                        </div>
                        <br>
                        <!-- Instagram -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                                Instagram
                            </label>
                            <input type="text" name="instagram" value="{{ auth()->user()->instagram }}"
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-pink-400 focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm"
                                placeholder="@username">
                        </div>

                        <!-- Twitter -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                </svg>
                                Twitter (X)
                            </label>
                            <input type="text" name="twitter" value="{{ auth()->user()->twitter }}"
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-blue-400 focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm"
                                placeholder="@username">
                        </div>

                        <!-- YouTube -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                                YouTube
                            </label>
                            <input type="text" name="youtube" value="{{ auth()->user()->youtube }}"
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-red-500 focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm"
                                placeholder="URL Channel">
                        </div>

                        <!-- TikTok -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.525.02c1.31-.02 2.61-.01 3.91-.01.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-4.17.07-8.33.07-12.5z" />
                                </svg>
                                TikTok
                            </label>
                            <input type="text" name="tiktok" value="{{ auth()->user()->tiktok }}"
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-gray-900 focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm"
                                placeholder="@username">
                        </div>
                        <!-- Security Divider -->
                        <div class="md:col-span-2 flex items-center gap-4 mt-8 mb-2">
                            <div class="h-px flex-1 bg-gray-100"></div>
                            <span
                                class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Keamanan
                                Akun</span>
                            <div class="h-px flex-1 bg-gray-100"></div>
                        </div>
                        <br>
                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Password Baru
                            </label>
                            <input type="password" name="password" @cannot('fitur.edit_profile') disabled @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-[#36B2B2] focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm @cannot('fitur.edit_profile') opacity-75 cursor-not-allowed @endcannot"
                                placeholder="{{ auth()->user()->can('fitur.edit_profile') ? '••••••••' : 'Tidak diizinkan' }}">
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Konfirmasi Password
                            </label>
                            <input type="password" name="password_confirmation" @cannot('fitur.edit_profile') disabled
                                @endcannot
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50/50 border border-gray-200 focus:border-[#36B2B2] focus:outline-none transition-all text-sm font-medium text-gray-800 shadow-sm @cannot('fitur.edit_profile') opacity-75 cursor-not-allowed @endcannot"
                                placeholder="{{ auth()->user()->can('fitur.edit_profile') ? '••••••••' : 'Tidak diizinkan' }}">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 mt-8">
                        <button type="button" @click="$store.profile.close()"
                            class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-all">
                            {{ auth()->user()->can('fitur.edit_profile') ? 'Batal' : 'Tutup' }}
                        </button>

                        @can('fitur.edit_profile')
                            <button type="submit"
                                class="px-8 py-2.5 rounded-xl text-sm font-black text-white bg-[#36B2B2] hover:bg-[#2d9696] shadow-lg shadow-[#36B2B2]/20 hover:shadow-xl hover:shadow-[#36B2B2]/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all">
                                Simpan Perubahan
                            </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
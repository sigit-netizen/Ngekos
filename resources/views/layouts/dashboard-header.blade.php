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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ auth()->user()->name }}" required
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400 hover:border-gray-300 shadow-sm @cannot('fitur.edit_profile') bg-gray-50 opacity-75 cursor-not-allowed hover:border-gray-200 focus:border-gray-200 focus:ring-0 @endcannot">
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Alamat Email</label>
                            <input type="email" name="email" value="{{ auth()->user()->email }}" required
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400 hover:border-gray-300 shadow-sm @cannot('fitur.edit_profile') bg-gray-50 opacity-75 cursor-not-allowed hover:border-gray-200 focus:border-gray-200 focus:ring-0 @endcannot">
                        </div>

                        <!-- NIK -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" name="nik" value="{{ auth()->user()->nik }}" @cannot('fitur.edit_profile')
                                readonly @endcannot
                                class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400 hover:border-gray-300 shadow-sm @cannot('fitur.edit_profile') bg-gray-50 opacity-75 cursor-not-allowed hover:border-gray-200 focus:border-gray-200 focus:ring-0 @endcannot"
                                placeholder="Masukan 16 digit NIK">
                        </div>

                        <!-- WA -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 ml-1">WhatsApp (Aktif)</label>
                            <input type="text" name="nomor_wa" value="{{ auth()->user()->nomor_wa }}"
                                @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400 hover:border-gray-300 shadow-sm @cannot('fitur.edit_profile') bg-gray-50 opacity-75 cursor-not-allowed hover:border-gray-200 focus:border-gray-200 focus:ring-0 @endcannot"
                                placeholder="Contoh: 0812xxxx">
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Alamat Domisili</label>
                            <textarea name="alamat" rows="2" @cannot('fitur.edit_profile') readonly @endcannot
                                class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400 hover:border-gray-300 shadow-sm resize-none @cannot('fitur.edit_profile') bg-gray-50 opacity-75 cursor-not-allowed hover:border-gray-200 focus:border-gray-200 focus:ring-0 @endcannot"
                                placeholder="Masukan alamat lengkap Anda...">{{ auth()->user()->alamat }}</textarea>
                        </div>

                        <!-- Security Divider -->
                        <div class="md:col-span-2 flex items-center gap-4 py-3 mt-2">
                            <div class="h-px flex-1 bg-gray-100"></div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Keamanan Akun</span>
                            <div class="h-px flex-1 bg-gray-100"></div>
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Password Baru</label>
                            <input type="password" name="password" @cannot('fitur.edit_profile') disabled @endcannot
                                class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400 hover:border-gray-300 shadow-sm @cannot('fitur.edit_profile') bg-gray-50 opacity-75 cursor-not-allowed hover:border-gray-200 focus:border-gray-200 focus:ring-0 @endcannot"
                                placeholder="{{ auth()->user()->can('fitur.edit_profile') ? '••••••••' : 'Tidak diizinkan' }}">
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" @cannot('fitur.edit_profile') disabled
                                @endcannot
                                class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-[#36B2B2] focus:ring-4 focus:ring-[#36B2B2]/10 transition-all text-sm font-medium text-gray-800 placeholder-gray-400 hover:border-gray-300 shadow-sm @cannot('fitur.edit_profile') bg-gray-50 opacity-75 cursor-not-allowed hover:border-gray-200 focus:border-gray-200 focus:ring-0 @endcannot"
                                placeholder="{{ auth()->user()->can('fitur.edit_profile') ? '••••••••' : 'Tidak diizinkan' }}">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 mt-8">
                        <button type="button" @click="$store.profile.close()"
                            class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-all focus:outline-none focus:ring-2 focus:ring-gray-200">
                            {{ auth()->user()->can('fitur.edit_profile') ? 'Batal' : 'Tutup' }}
                        </button>

                        @can('fitur.edit_profile')
                            <button type="submit"
                                class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#36B2B2] hover:bg-[#2d9696] shadow-md shadow-[#36B2B2]/20 hover:shadow-lg hover:shadow-[#36B2B2]/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#36B2B2]">
                                Simpan Perubahan
                            </button>
                        @endcan
                    </div>
            </div>
@endpush
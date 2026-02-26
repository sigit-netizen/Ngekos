@extends('layouts.fullscreen-layout')

@section('content')
    <style>
        /* Hilangkan spinner Chrome, Edge, Safari */
        #nomor_wa::-webkit-inner-spin-button,
        #nomor_wa::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hilangkan spinner Firefox */
        #nomor_wa {
            -moz-appearance: textfield;
        }

        /* Hilangkan spinner Chrome, Edge, Safari */
        #nik::-webkit-inner-spin-button,
        #nik::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hilangkan spinner Firefox */
        #nik {
            -moz-appearance: textfield;
        }
    </style>
    <div
        class="relative min-h-screen bg-gradient-to-b from-slate-50/80 to-white overflow-hidden py-10 px-4 sm:px-6 lg:px-8 flex items-center justify-center font-inter">

        <!-- Decorative Background Elements -->
        <div class="absolute top-10 left-10 w-72 h-72 bg-[#36B2B2]/10 rounded-full blur-3xl -z-10 animate-float"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-blue-400/10 rounded-full blur-3xl -z-10 animate-float"
            style="animation-delay: 1.5s;"></div>

        <div class="w-full max-w-[540px] z-10">

            <!-- Logo Section -->
            <div class="text-center mb-8" data-aos="fade-down" data-aos-duration="800">
                <a href="/"
                    class="inline-flex items-center gap-2 cursor-pointer transition-transform hover:scale-105 duration-300">
                    <span class="font-extrabold text-3xl text-gray-900 tracking-tight">
                        K<span class="text-[#36B2B2] ml-1 font-semibold">Ngekos</span><span
                            class="text-gray-400 font-light text-2xl">.id</span>
                    </span>
                </a>
                <p class="mt-3 text-sm font-medium text-gray-500">Mulai temukan kos impianmu sekarang</p>
            </div>

            <!-- Glassmorphism Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-[2rem] shadow-2xl shadow-gray-200/60 border border-gray-100 p-8 sm:p-10 transform transition-all duration-500 hover:shadow-[#36b2b2]/5"
                data-aos="fade-up" data-aos-duration="1000">

                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Buat Akun</h1>
                    <p class="text-sm text-gray-500">
                        Isi data berikut untuk mendaftar!
                    </p>
                </div>
                <form method="POST">
                    @csrf
                    <div class="space-y-5">

                        <!-- Nama Input -->
                        <div class="group">
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Nama Lengkap<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap"
                                class="h-12 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300"
                                required />
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <!-- NIK Input -->
                            <div class="group">
                                <label
                                    class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                    NIK<span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="number" id="nik" name="nik" placeholder="Masukkan NIK"
                                    class="h-12 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300"
                                    required />
                            </div>

                            <!-- Nomor WA Input -->
                            <div class="group">
                                <label
                                    class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                    Nomor WhatsApp<span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="number" id="nomor_wa" name="nomor_wa" placeholder="08..." required
                                    class="h-12 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300" />
                            </div>
                        </div>

                        <!-- Tanggal Lahir Input -->
                        <div class="group">
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Tanggal Lahir
                            </label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                class="h-12 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300" />
                        </div>

                        <!-- Alamat Input -->
                        <div class="group">
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Alamat Domisili
                            </label>
                            <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50/50 p-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300"></textarea>
                        </div>

                        <!-- Role Selection (Dropdown) -->
                        <div class="group" x-data="{ isOpen: false, selectedRole: '', roleText: 'Pilih peran...' }"
                            @click.outside="isOpen = false">
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Mendaftar Sebagai<span class="text-red-500 ml-1">*</span>
                            </label>

                            <div class="relative">
                                <!-- Hidden Input to Store Selected Value -->
                                <input type="hidden" name="id_plans" x-model="selectedRole" required>

                                <!-- Custom Select Button -->
                                <button type="button" @click="isOpen = !isOpen"
                                    class="relative h-12 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 text-left text-sm text-gray-800 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300 shadow-sm"
                                    :class="{ 'text-gray-800': selectedRole, 'text-gray-400': !selectedRole, 'bg-white border-[#36B2B2] ring-4 ring-[#36B2B2]/10': isOpen }">
                                    <span x-text="roleText" class="block truncate"></span>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="h-5 w-5 text-gray-400 transition-transform duration-300"
                                            :class="{ 'rotate-180': isOpen }" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <!-- Dropdown Options -->
                                <ul x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="absolute z-20 mt-1 w-full rounded-xl bg-white/95 backdrop-blur-xl shadow-lg border border-gray-100 py-2 text-sm text-gray-700 max-h-60 overflow-auto focus:outline-none ring-1 ring-black ring-opacity-5"
                                    style="display: none;">

                                    <!-- Options mapping roughly to 'id_plans' conceptually -->
                                    <li @click="selectedRole = '1'; roleText = 'Anak Kos'; isOpen = false"
                                        class="group relative cursor-pointer select-none py-3 pl-4 pr-9 hover:bg-gray-50 hover:text-[#36B2B2] transition-colors"
                                        :class="{ 'bg-gray-50 text-[#36B2B2] font-medium': selectedRole === '1' }">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 rounded-lg bg-[#36B2B2]/10 flex items-center justify-center text-[#36B2B2]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="block font-medium">Anak Kos</span>
                                                <span class="block text-xs text-gray-500 group-hover:text-gray-600">Saya
                                                    ingin mencari dan menyewa kos</span>
                                            </div>
                                        </div>
                                        <span x-show="selectedRole === '1'"
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#36B2B2]">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </li>

                                    <div class="border-t border-gray-100 my-1"></div>

                                    <li @click="selectedRole = '2'; roleText = 'Pemilik Kos'; isOpen = false"
                                        class="group relative cursor-pointer select-none py-3 pl-4 pr-9 hover:bg-gray-50 hover:text-[#36B2B2] transition-colors"
                                        :class="{ 'bg-gray-50 text-[#36B2B2] font-medium': selectedRole === '2' }">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 rounded-lg bg-[#36B2B2]/10 flex items-center justify-center text-[#36B2B2]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="block font-medium">Pemilik Kos</span>
                                                <span class="block text-xs text-gray-500 group-hover:text-gray-600">Saya
                                                    ingin menyewakan dan mengelola kos</span>
                                            </div>
                                        </div>
                                        <span x-show="selectedRole === '2'"
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#36B2B2]">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="group">
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Email<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="email" id="email" name="email" placeholder="contoh@mail.com"
                                class="h-12 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300" />
                        </div>

                        <!-- Password Input -->
                        <div class="group">
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Password<span class="text-red-500 ml-1">*</span>
                            </label>
                            <div x-data="{ showPassword: false }" class="relative">
                                <input :type="showPassword ? 'text' : 'password'" placeholder="Minimal 8 karakter"
                                    class="h-12 w-full rounded-xl border border-gray-200 bg-gray-50/50 py-2.5 pl-4 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#36B2B2] focus:bg-white focus:ring-4 focus:ring-[#36B2B2]/10 focus:outline-none transition-all duration-300" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute top-1/2 right-4 -translate-y-1/2 p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors focus:outline-none">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox -->
                        <div x-data="{ checkboxToggle: false }" class="pt-1">
                            <label for="checkboxLabelOne"
                                class="flex cursor-pointer items-start text-sm font-normal text-gray-600 select-none group">
                                <div class="relative mt-0.5">
                                    <input type="checkbox" id="checkboxLabelOne" class="sr-only"
                                        @change="checkboxToggle = !checkboxToggle" />
                                    <div :class="checkboxToggle ? 'border-[#36B2B2] bg-[#36B2B2]' : 'bg-white border-gray-300 group-hover:border-[#36B2B2]'"
                                        class="mr-3 flex h-5 w-5 items-center justify-center rounded border transition-colors duration-200">
                                        <span :class="checkboxToggle ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"
                                            class="transition-all duration-200 ease-out">
                                            <svg width="12" height="12" viewBox="0 0 14 14" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <p class="leading-relaxed">
                                    Saya menyetujui
                                    <a href="#" class="text-[#36B2B2] font-semibold hover:underline">Syarat & Ketentuan</a>
                                    serta
                                    <a href="#" class="text-[#36B2B2] font-semibold hover:underline">Kebijakan Privasi</a>
                                </p>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button
                                class="w-full relative flex items-center justify-center rounded-xl bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f] px-4 py-4 text-sm font-bold text-white shadow-lg shadow-[#36b2b2]/30 hover:shadow-[#36b2b2]/50 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden group">
                                <span class="relative z-10 flex items-center gap-2">
                                    Daftar Sekarang
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                                <!-- Hover flare effect -->
                                <div
                                    class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1s_forwards] bg-gradient-to-r from-transparent via-white/20 to-transparent">
                                </div>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 text-center pt-6 border-t border-gray-100">
                    <p class="text-sm font-normal text-gray-500">
                        Sudah punya akun?
                        <a href="/signin"
                            class="font-bold text-[#36B2B2] hover:text-[#2b8f8f] transition-colors hover:underline">Masuk
                            disini</a>
                    </p>
                </div>

            </div>

            <!-- Back formatting like welcome section -->
            <div class="mt-8 text-center">
                <a href="/"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white/50 hover:bg-white border border-gray-200 rounded-full text-xs font-semibold text-gray-600 hover:text-[#36B2B2] transition shadow-sm backdrop-blur-sm group">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#36B2B2] group-hover:-translate-x-1 transition"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke beranda
                </a>
            </div>

        </div>
    </div>

    <style>
        /* Floating Animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        /* Shimmer Button Effect */
        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }
    </style>
@endsection
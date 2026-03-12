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
                    class="inline-flex items-center cursor-pointer transition-transform hover:scale-105 duration-300">
                    <img src="/storage/logo/auth-logo.svg" alt="Logo" class="h-12 w-auto" />
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
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="space-y-5" x-data="{ selectedRole: '{{ old('id_plans', '1') }}' }">

                        <!-- Nama Lengkap -->
                        <div class="group">
                            <label class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Nama Lengkap<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap"
                                class="h-12 w-full rounded-xl border {{ $errors->has('name') ? 'border-red-500 bg-red-50/50 focus:ring-0' : 'border-gray-200 bg-gray-50/50 focus:border-[#36B2B2] focus:ring-[#36B2B2]/10' }} px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:bg-white focus:outline-none transition-all duration-300"
                                required />
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role Selection (Radio Buttons) -->
                        <div class="group">
                            <label class="mb-3 block text-sm font-semibold text-gray-700">
                                Mendaftar Sebagai<span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Penyewa / Anak Kos -->
                                <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all duration-300"
                                    :class="selectedRole === '1' ? 'border-[#36B2B2] bg-[#36B2B2]/5 ring-2 ring-[#36B2B2]/10' : 'border-gray-100 bg-gray-50/50 hover:border-gray-200'">
                                    <input type="radio" name="id_plans" value="1" x-model="selectedRole" class="sr-only" required>
                                    <div class="flex w-full items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition-colors"
                                            :class="selectedRole === '1' ? 'bg-[#36B2B2] text-white' : 'bg-gray-200 text-gray-400'">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold" :class="selectedRole === '1' ? 'text-[#36B2B2]' : 'text-gray-700'">Penyewa</p>
                                            <p class="text-[10px] text-gray-500">Cari kos</p>
                                        </div>
                                    </div>
                                </label>

                                <!-- Pemilik Kos -->
                                <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all duration-300"
                                    :class="selectedRole === '2' ? 'border-[#36B2B2] bg-[#36B2B2]/5 ring-2 ring-[#36B2B2]/10' : 'border-gray-100 bg-gray-50/50 hover:border-gray-200'">
                                    <input type="radio" name="id_plans" value="2" x-model="selectedRole" class="sr-only">
                                    <div class="flex w-full items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition-colors"
                                            :class="selectedRole === '2' ? 'bg-[#36B2B2] text-white' : 'bg-gray-200 text-gray-400'">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold" :class="selectedRole === '2' ? 'text-[#36B2B2]' : 'text-gray-700'">Pemilik</p>
                                            <p class="text-[10px] text-gray-500">Kelola kos</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('id_plans')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="group">
                            <label class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Email<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@mail.com" required
                                class="h-12 w-full rounded-xl border {{ $errors->has('email') ? 'border-red-500 bg-red-50/50 focus:ring-0' : 'border-gray-200 bg-gray-50/50 focus:border-[#36B2B2] focus:ring-[#36B2B2]/10' }} px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:bg-white focus:outline-none transition-all duration-300" />
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="group">
                            <label class="mb-2 block text-sm font-semibold text-gray-700 transition-colors group-focus-within:text-[#36B2B2]">
                                Password<span class="text-red-500 ml-1">*</span>
                            </label>
                            <div x-data="{ showPassword: false }" class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                    minlength="8" placeholder="Minimal 8 karakter"
                                    class="h-12 w-full rounded-xl border {{ $errors->has('password') ? 'border-red-500 bg-red-50/50 focus:ring-0' : 'border-gray-200 bg-gray-50/50 focus:border-[#36B2B2] focus:ring-[#36B2B2]/10' }} py-2.5 pl-4 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:bg-white focus:outline-none transition-all duration-300" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute top-1/2 right-4 -translate-y-1/2 p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors focus:outline-none">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Checkbox -->
                        <div x-data="{ checkboxToggle: false }" class="pt-1">
                            <label for="checkboxLabelOne" class="flex cursor-pointer items-start text-sm font-normal text-gray-600 select-none group">
                                <div class="relative mt-0.5">
                                    <input type="checkbox" id="checkboxLabelOne" class="sr-only" required @change="checkboxToggle = !checkboxToggle" />
                                    <div :class="checkboxToggle ? 'border-[#36B2B2] bg-[#36B2B2]' : 'bg-white border-gray-300 group-hover:border-[#36B2B2]'"
                                        class="mr-3 flex h-5 w-5 items-center justify-center rounded border transition-colors duration-200">
                                        <span :class="checkboxToggle ? 'opacity-100 scale-100' : 'opacity-0 scale-50'" class="transition-all duration-200 ease-out">
                                            <svg width="12" height="12" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
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
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                                <!-- Hover flare effect -->
                                <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1s_forwards] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 text-center pt-6 border-t border-gray-100">
                    <p class="text-sm font-normal text-gray-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
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
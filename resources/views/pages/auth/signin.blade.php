@extends('layouts.fullscreen-layout')

@section('content')
    <div
        class="relative min-h-screen bg-gradient-to-b from-slate-50/80 to-white overflow-hidden py-10 px-4 sm:px-6 lg:px-8 flex items-center justify-center font-inter">

        <!-- Decorative Background Elements -->
        <div class="absolute top-10 left-10 w-72 h-72 bg-[#36B2B2]/10 rounded-full blur-3xl -z-10 animate-float"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-blue-400/10 rounded-full blur-3xl -z-10 animate-float"
            style="animation-delay: 1.5s;"></div>

        <div class="w-full max-w-[480px] z-10">

            <!-- Logo Section -->
            <div class="text-center mb-8" data-aos="fade-down" data-aos-duration="800">
                <a href="/"
                    class="inline-flex items-center gap-2 cursor-pointer transition-transform hover:scale-105 duration-300">
                    <span class="font-extrabold text-3xl text-gray-900 tracking-tight">
                        K<span class="text-[#36B2B2] ml-1 font-semibold">Ngekos</span><span
                            class="text-gray-400 font-light text-2xl">.id</span>
                    </span>
                </a>
                <p class="mt-3 text-sm font-medium text-gray-500">Selamat datang kembali di Ngekos</p>
            </div>

            <!-- Glassmorphism Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-[2rem] shadow-2xl shadow-gray-200/60 border border-gray-100 p-8 sm:p-10 transform transition-all duration-500 hover:shadow-[#36b2b2]/5"
                data-aos="fade-up" data-aos-duration="1000">

                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Sign In</h1>
                    <p class="text-sm text-gray-500">
                        Masukkan email dan password Anda untuk masuk!
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 mb-6">
                    <!-- Google Sign In Button -->
                    <button
                        class="flex items-center justify-center gap-2 rounded-xl bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-100 transition-all hover:bg-gray-100 hover:border-gray-200 hover:shadow-sm">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M18.7511 10.1944C18.7511 9.47495 18.6915 8.94995 18.5626 8.40552H10.1797V11.6527H15.1003C15.0011 12.4597 14.4654 13.675 13.2749 14.4916L13.2582 14.6003L15.9087 16.6126L16.0924 16.6305C17.7788 15.1041 18.7511 12.8583 18.7511 10.1944Z"
                                fill="#4285F4" />
                            <path
                                d="M10.1788 18.75C12.5895 18.75 14.6133 17.9722 16.0915 16.6305L13.274 14.4916C12.5201 15.0068 11.5081 15.3666 10.1788 15.3666C7.81773 15.3666 5.81379 13.8402 5.09944 11.7305L4.99473 11.7392L2.23868 13.8295L2.20264 13.9277C3.67087 16.786 6.68674 18.75 10.1788 18.75Z"
                                fill="#34A853" />
                            <path
                                d="M5.10014 11.7305C4.91165 11.186 4.80257 10.6027 4.80257 9.99992C4.80257 9.3971 4.91165 8.81379 5.09022 8.26935L5.08523 8.1534L2.29464 6.02954L2.20333 6.0721C1.5982 7.25823 1.25098 8.5902 1.25098 9.99992C1.25098 11.4096 1.5982 12.7415 2.20333 13.9277L5.10014 11.7305Z"
                                fill="#FBBC05" />
                            <path
                                d="M10.1789 4.63331C11.8554 4.63331 12.9864 5.34303 13.6312 5.93612L16.1511 3.525C14.6035 2.11528 12.5895 1.25 10.1789 1.25C6.68676 1.25 3.67088 3.21387 2.20264 6.07218L5.08953 8.26943C5.81381 6.15972 7.81776 4.63331 10.1789 4.63331Z"
                                fill="#EB4335" />
                        </svg>
                        Google
                    </button>
                    <!-- X (Twitter) Sign In Button -->
                    <button
                        class="flex items-center justify-center gap-2 rounded-xl bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-100 transition-all hover:bg-gray-100 hover:border-gray-200 hover:shadow-sm">
                        <svg width="18" class="fill-current text-gray-900" height="18" viewBox="0 0 21 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.6705 1.875H18.4272L12.4047 8.75833L19.4897 18.125H13.9422L9.59717 12.4442L4.62554 18.125H1.86721L8.30887 10.7625L1.51221 1.875H7.20054L11.128 7.0675L15.6705 1.875ZM14.703 16.475H16.2305L6.37054 3.43833H4.73137L14.703 16.475Z" />
                        </svg>
                        Twitter
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative py-4 mb-2 flex items-center justify-center">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-4 text-gray-400 font-medium text-xs uppercase tracking-wider">Atau gunakan
                            email</span>
                    </div>
                </div>

                <form>
                    <div class="space-y-5">
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
                                <input :type="showPassword ? 'text' : 'password'" placeholder="Masukkan password Anda"
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

                        <!-- Checkbox & Forgot Password -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1">
                            <div x-data="{ checkboxToggle: false }">
                                <label for="checkboxLabelOne"
                                    class="flex cursor-pointer items-center text-sm font-normal text-gray-600 select-none group">
                                    <div class="relative">
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
                                    <span>Ingat saya</span>
                                </label>
                            </div>
                            <a href="/reset-password"
                                class="text-sm font-semibold text-[#36B2B2] hover:text-[#2b8f8f] transition-colors hover:underline">
                                Lupa password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button
                                class="w-full relative flex items-center justify-center rounded-xl bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f] px-4 py-4 text-sm font-bold text-white shadow-lg shadow-[#36b2b2]/30 hover:shadow-[#36b2b2]/50 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden group">
                                <span class="relative z-10 flex items-center gap-2">
                                    Masuk
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
                        Belum punya akun?
                        <a href="/signup"
                            class="font-bold text-[#36B2B2] hover:text-[#2b8f8f] transition-colors hover:underline">Daftar
                            sekarang</a>
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
                    Kembali ke Beranda
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
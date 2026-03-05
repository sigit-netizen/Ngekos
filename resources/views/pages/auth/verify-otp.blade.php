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
                    class="inline-flex items-center cursor-pointer transition-transform hover:scale-105 duration-300">
                    <img src="/storage/logo/auth-logo.svg" alt="Logo" class="h-12 w-auto" />
                </a>
                <p class="mt-3 text-sm font-medium text-gray-500">Verifikasi Keamanan Perangkat</p>
            </div>

            <!-- Glassmorphism Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-[2rem] shadow-2xl shadow-gray-200/60 border border-gray-100 p-8 sm:p-10 transform transition-all duration-500 hover:shadow-[#36b2b2]/5"
                data-aos="fade-up" data-aos-duration="1000">

                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Masukkan Kode OTP</h1>
                    <p class="text-sm text-gray-500">
                        Kode verifikasi telah dikirim ke
                        <span
                            class="font-bold text-gray-700">{{ session('otp_channel') == 'whatsapp' ? 'WhatsApp' : 'Email' }}</span>
                        Anda.
                        Masukkan 6 digit kode tersebut di bawah ini.
                    </p>
                </div>

                <form method="POST" action="{{ route('otp.verify.post') }}">
                    @csrf
                    @if (session('dummy_otp'))
                        <div class="mb-5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 p-4 shadow-sm"
                            x-data="{ show: true }" x-show="show" x-transition.opacity>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1 flex flex-col pt-1">
                                    <p class="text-sm font-bold text-emerald-800 tracking-wide uppercase">
                                        [TESTING MODE] DUMMY OTP:
                                    </p>
                                    <p class="text-3xl font-black text-emerald-900 mt-1 tracking-widest">
                                        {{ session('dummy_otp') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button type="button" @click="show = false"
                                            class="inline-flex rounded-md p-1.5 text-emerald-500 hover:bg-emerald-100 focus:outline-none transition-colors">
                                            <span class="sr-only">Tutup</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-5 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-100 p-4 shadow-sm"
                            x-data="{ show: true }" x-show="show" x-transition.opacity>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1 pt-0.5">
                                    <p class="text-sm font-semibold text-red-800">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div
                            class="mb-5 rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-emerald-800 text-sm font-medium">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-6">
                        <div class="group" x-data="{ otp: '' }">
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Kode OTP<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" name="otp" required maxlength="6" x-model="otp"
                                @input="otp = otp.replace(/[^0-9]/g, '')" placeholder="******"
                                class="h-14 w-full text-center text-2xl font-black tracking-[0.5em] rounded-2xl border border-gray-200 bg-gray-50/50 focus:border-[#36B2B2] focus:ring-[#36B2B2]/10 px-4 placeholder:text-gray-300 focus:bg-white focus:outline-none transition-all duration-300" />
                            @error('otp')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full relative flex items-center justify-center rounded-xl bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f] px-4 py-4 text-sm font-bold text-white shadow-lg shadow-[#36b2b2]/30 hover:shadow-[#36b2b2]/50 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden group">
                                <span class="relative z-10 flex items-center gap-2">
                                    Verifikasi & Masuk
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                                <div
                                    class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1s_forwards] bg-gradient-to-r from-transparent via-white/20 to-transparent">
                                </div>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 text-center pt-6 border-t border-gray-100" x-data="countdownTimer(60)">
                    <p class="text-sm text-gray-500" x-show="seconds > 0">
                        Kirim ulang kode dalam <span class="font-bold text-[#36B2B2]" x-text="seconds + ' detik'"></span>
                    </p>
                    <div x-show="seconds <= 0" x-cloak>
                        <p class="text-sm text-gray-500 mb-2">Belum menerima kode?</p>
                        <form action="{{ route('otp.resend') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="text-sm font-bold text-[#36B2B2] hover:text-[#2b8f8f] transition-colors focus:outline-none hover:underline flex items-center justify-center gap-1 mx-auto group">
                                <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Kirim Ulang Kode
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('otp.select') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white/50 hover:bg-white border border-gray-200 rounded-full text-xs font-semibold text-gray-600 hover:text-[#36B2B2] transition shadow-sm backdrop-blur-sm group">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#36B2B2] group-hover:-translate-x-1 transition"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Pilihan Metode
                </a>
            </div>

        </div>
    </div>

    <script>
        function countdownTimer(initialSeconds) {
            return {
                seconds: initialSeconds,
                init() {
                    let timer = setInterval(() => {
                        if (this.seconds <= 0) {
                            clearInterval(timer);
                        } else {
                            this.seconds--;
                        }
                    }, 1000);
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

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

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }
    </style>
@endsection

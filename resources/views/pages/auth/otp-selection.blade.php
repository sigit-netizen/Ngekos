@extends('layouts.fullscreen-layout')

@section('content')
    <div
        class="relative min-h-screen bg-gradient-to-b from-slate-50/80 to-white overflow-hidden py-10 px-4 sm:px-6 lg:px-8 flex items-center justify-center font-inter">

        <!-- Decorative Background -->
        <div class="absolute top-10 left-10 w-72 h-72 bg-[#36B2B2]/10 rounded-full blur-3xl -z-10 animate-float"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-blue-400/10 rounded-full blur-3xl -z-10 animate-float"
            style="animation-delay: 1.5s;"></div>

        <div class="w-full max-w-[480px] z-10">
            <div class="text-center mb-8" data-aos="fade-down" data-aos-duration="800">
                <a href="/"
                    class="inline-flex items-center cursor-pointer transition-transform hover:scale-105 duration-300">
                    <img src="/storage/logo/auth-logo.svg" alt="Logo" class="h-12 w-auto" />
                </a>
                <p class="mt-3 text-sm font-medium text-gray-500">Verifikasi Keamanan Akun</p>
            </div>

            <div class="bg-white/95 backdrop-blur-md rounded-[2rem] shadow-2xl shadow-gray-200/60 border border-gray-100 p-8 sm:p-10 transform transition-all duration-500 hover:shadow-[#36b2b2]/5"
                data-aos="fade-up" data-aos-duration="1000">

                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Pilih Metode</h1>
                    <p class="text-sm text-gray-500">
                        Pilih saluran untuk menerima kode verifikasi OTP Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('otp.send') }}">
                    @csrf
                    <div class="space-y-4" x-data="{ selectedChannel: 'whatsapp' }">
                        <!-- WhatsApp Option -->
                        <label for="channel_wa"
                            class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-[#36B2B2]/50"
                            :class="selectedChannel === 'whatsapp' ? 'border-[#36B2B2] bg-[#36B2B2]/5' : 'border-gray-100 bg-gray-50/50'">
                            <input type="radio" name="channel" id="channel_wa" value="whatsapp" class="sr-only"
                                x-model="selectedChannel">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72 1.092 3.844 1.704 5.914 1.704h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-bold text-gray-900 tracking-tight">WhatsApp</p>
                                <p class="text-xs text-gray-500">
                                    {{ Str::mask($user->nomor_wa ?? '08**********', '*', 4, 6) }}</p>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                :class="selectedChannel === 'whatsapp' ? 'border-[#36B2B2] bg-[#36B2B2]' : 'border-gray-200'">
                                <div class="w-2 h-2 rounded-full bg-white transition-opacity"
                                    :class="selectedChannel === 'whatsapp' ? 'opacity-100' : 'opacity-0'"></div>
                            </div>
                        </label>

                        <!-- Email Option -->
                        <label for="channel_email"
                            class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-[#36B2B2]/50"
                            :class="selectedChannel === 'email' ? 'border-[#36B2B2] bg-[#36B2B2]/5' : 'border-gray-100 bg-gray-50/50'">
                            <input type="radio" name="channel" id="channel_email" value="email" class="sr-only"
                                x-model="selectedChannel">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-bold text-gray-900 tracking-tight">Email</p>
                                <p class="text-xs text-gray-500">
                                    {{ Str::mask($user->email, '*', 2, strpos($user->email, '@') - 2) }}</p>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                :class="selectedChannel === 'email' ? 'border-[#36B2B2] bg-[#36B2B2]' : 'border-gray-200'">
                                <div class="w-2 h-2 rounded-full bg-white transition-opacity"
                                    :class="selectedChannel === 'email' ? 'opacity-100' : 'opacity-0'"></div>
                            </div>
                        </label>
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                            class="w-full relative flex items-center justify-center rounded-xl bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f] px-4 py-4 text-sm font-bold text-white shadow-lg shadow-[#36b2b2]/30 hover:shadow-[#36b2b2]/50 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden group">
                            <span class="relative z-10 flex items-center gap-2">
                                Kirim Kode OTP
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                            <div
                                class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1s_forwards] bg-gradient-to-r from-transparent via-white/20 to-transparent">
                            </div>
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white/50 hover:bg-white border border-gray-200 rounded-full text-xs font-semibold text-gray-600 hover:text-[#36B2B2] transition shadow-sm backdrop-blur-sm group">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#36B2B2] group-hover:-translate-x-1 transition"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Batal dan Kembali Login
                </a>
            </div>
        </div>
    </div>

    <style>
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

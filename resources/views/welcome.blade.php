<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ngekos - Cari Kos Sesuai Kebutuhanmu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- AOS Animation Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1a202c;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Custom animations for continuous floating elements (Not scroll dependent) */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(54, 178, 178, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(54, 178, 178, 0.6);
            }
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        /* Hide scrollbar untuk Chrome/Safari/Opera */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar untuk IE/Edge/Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(54, 178, 178, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(54, 178, 178, 0.6);
            }
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>

<body class="antialiased font-inter bg-white">

    <!-- Navigation -->
    <nav x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = (window.pageYOffset > 10)"
        :class="{'py-4': !scrolled, 'py-2': scrolled}"
        class="fixed w-full z-50 transition-all duration-300 top-0 left-0 right-0">
        <div class="w-[95%] max-w-[1400px] mx-auto px-4 sm:px-6">
            <div :class="{'bg-white/95 backdrop-blur-md shadow-lg border border-gray-100': scrolled, 'bg-white shadow-sm border border-gray-50': !scrolled}"
                class="flex items-center justify-between px-6 py-3 rounded-full transition-all duration-300">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2.5 cursor-pointer w-48" @click="window.scrollTo(0,0)">
                    <span class="font-extrabold text-2xl text-gray-900 tracking-tight">K<span
                            class="text-[#36B2B2] ml-1 font-semibold">Ngekos</span><span
                            class="text-gray-400 font-light text-xl">.id</span></span>
                </div>

                <!-- Desktop Menu (Centered) -->
                <nav class="hidden md:flex flex-1 items-center justify-center gap-10" style="gap: 2.5rem;">
                    <a href="#solusi"
                        class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">Fitur</a>
                    <a href="#harga"
                        class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">Harga</a>
                    <a href="#testimoni"
                        class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">Testimoni</a>
                    <a href="#"
                        class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">FAQ</a>
                </nav>

                <!-- Right Menu (Auth) -->
                <div class="hidden md:flex items-center justify-end w-56 gap-3" style="gap: 0.75rem;">
                    @auth
                        <a href="{{ url('/admin') }}"
                            class="text-sm font-semibold text-[#36B2B2] hover:text-[#2b8f8f] transition px-4 py-2">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('signin') }}"
                            class="inline-flex justify-center items-center px-6 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#36B2B2] transition">Masuk</a>
                        <a href="{{ route('signup') }}"
                            class="inline-flex justify-center items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full text-white bg-[#36B2B2] hover:bg-[#2b8f8f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#36B2B2] shadow-sm transition whitespace-nowrap">Coba
                            Gratis</a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center w-48 justify-end">
                    <button @click="mobileMenu = !mobileMenu" type="button"
                        class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition">
                        <svg x-show="!mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden absolute top-full left-4 right-4 mt-2 bg-white/95 backdrop-blur-md shadow-xl border border-gray-100 rounded-2xl overflow-hidden"
            x-cloak>
            <div class="px-4 py-4 flex flex-col space-y-1.5">
                <a href="#solusi" @click="mobileMenu = false"
                    class="px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#36B2B2] transition">Solusi</a>
                <a href="#keunggulan" @click="mobileMenu = false"
                    class="px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#36B2B2] transition">Fitur</a>
                <a href="#testimoni" @click="mobileMenu = false"
                    class="px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#36B2B2] transition">Testimoni</a>
                <a href="#harga" @click="mobileMenu = false"
                    class="px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#36B2B2] transition">Harga</a>

                <div class="border-t border-gray-100 pt-4 mt-2 px-4 flex flex-col gap-2.5">
                    @auth
                        <a href="{{ url('/admin') }}"
                            class="w-full text-center px-4 py-3 rounded-xl text-[#36B2B2] border border-[#36B2B2]/30 bg-[#36B2B2]/5 font-semibold hover:bg-[#36B2B2]/10 transition">Dashboard
                            Admin</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full text-center px-4 py-3 rounded-xl text-gray-700 border border-gray-200 font-medium hover:bg-gray-50 transition">Login</a>
                        @if (Route::has('signup'))
                            <a href="{{ route('signup') }}"
                                class="w-full text-center px-4 py-3 rounded-xl bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f] text-white font-semibold hover:shadow-lg transition">Daftar
                                Sekarang</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-28 pb-16 md:pt-36 md:pb-24 overflow-hidden bg-gradient-to-b from-slate-50/80 to-white relative">
        <!-- Decorative Elements -->
        <div class="absolute top-20 right-0 w-72 h-72 bg-[#36B2B2]/10 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-10 w-64 h-64 bg-blue-400/10 rounded-full blur-3xl -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col items-center text-center">

                <!-- Text Content -->
                <div class="w-full max-w-4xl" data-aos="fade-up" data-aos-duration="1000">
                    <!-- Badge -->
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-xs font-semibold tracking-wide mb-6 border border-[#36B2B2]/20 hover:bg-[#36B2B2]/20 transition-colors cursor-default">
                        <span class="w-2 h-2 bg-[#36B2B2] rounded-full animate-pulse"></span>
                        #1 Platform Cari & Manajemen Kos
                    </div>

                    <!-- Headline -->
                    <h1
                        class="text-2xl xs:text-3xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-[1.3] lg:leading-[1.1] tracking-tight mb-6">
                        Cari & Kelola Kos Paling <br class="hidden lg:block" />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f]">Gampang
                            Banget!</span>
                    </h1>

                    <!-- Subheadline -->
                    <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                        Sistem manajemen informasi terintegrasi untuk mengelola data kos—mulai dari pendaftaran,
                        penyewaan kamar, aduan fasilitas, hingga pembayaran.
                    </p>

                    <!-- Search & Recommendations Widget -->
                    <div
                        class="bg-white rounded-[2rem] shadow-2xl shadow-gray-200/60 border border-gray-100 mb-12 overflow-hidden relative z-20 flex flex-col w-screen -mx-4 xs:w-full xs:mx-0 max-w-4xl mx-auto transform hover:scale-[1.005] transition-transform duration-500">

                        <!-- Top Search Bar -->
                        <div class="p-3 border-b border-gray-50 flex flex-col lg:flex-row gap-2">
                            <!-- Location Input -->
                            <div
                                class="w-full lg:flex-[1.5] flex items-center bg-gray-50 rounded-xl px-4 py-3.5 border focus-within:ring-2 focus-within:ring-[#36B2B2]/50 focus-within:border-[#36B2B2] transition border-transparent group">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#36B2B2] mr-2.5 shrink-0 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <input type="text" placeholder="Ketik lokasi atau nama kos..."
                                    class="bg-transparent w-full text-sm outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto flex-1">
                                <!-- Modern Type Selector (Alpine.js) -->
                                <div class="flex-1 relative" x-data="{ 
                                    open: false, 
                                    selected: '', 
                                    label: 'Tipe Kos',
                                    options: [
                                        { value: 'putra', label: 'Putra' },
                                        { value: 'putri', label: 'Putri' },
                                        { value: 'campur', label: 'Campur' }
                                    ],
                                    select(opt) {
                                        this.selected = opt.value;
                                        this.label = opt.label;
                                        this.open = false;
                                    }
                                }">
                                    <button @click="open = !open" type="button"
                                        class="w-full flex items-center bg-gray-50 rounded-xl px-4 py-3.5 border focus:ring-2 focus:ring-[#36B2B2]/50 focus:border-[#36B2B2] transition border-transparent group">
                                        <svg class="w-5 h-5 text-gray-400 group-focus:text-[#36B2B2] mr-2.5 shrink-0 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="text-sm text-gray-700" x-text="label"></span>
                                        <svg class="ml-auto w-4 h-4 text-gray-400 transition-transform duration-300"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <input type="hidden" name="type" x-model="selected">

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-100 py-1 overflow-hidden"
                                        style="display: none;">
                                        <template x-for="opt in options" :key="opt.value">
                                            <button @click="select(opt)" type="button"
                                                class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-[#36B2B2]/5 hover:text-[#36B2B2] transition-colors flex items-center justify-between">
                                                <span x-text="opt.label"></span>
                                                <svg x-show="selected === opt.value" class="w-4 h-4 text-[#36B2B2]"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Modern Price Selector (Alpine.js) -->
                                <div class="flex-1 relative" x-data="{ 
                                    open: false, 
                                    selected: '', 
                                    label: 'Rentang Harga',
                                    options: [
                                        { value: '500k', label: '< 500rb' },
                                        { value: '500k-1m', label: '500rb - 1Jt' },
                                        { value: '1m-2m', label: '1Jt - 2Jt' },
                                        { value: '2m', label: '> 2Jt' }
                                    ],
                                    select(opt) {
                                        this.selected = opt.value;
                                        this.label = opt.label;
                                        this.open = false;
                                    }
                                }">
                                    <button @click="open = !open" type="button"
                                        class="w-full flex items-center bg-gray-50 rounded-xl px-4 py-3.5 border focus:ring-2 focus:ring-[#36B2B2]/50 focus:border-[#36B2B2] transition border-transparent group">
                                        <svg class="w-5 h-5 text-gray-400 group-focus:text-[#36B2B2] mr-2.5 shrink-0 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-700" x-text="label"></span>
                                        <svg class="ml-auto w-4 h-4 text-gray-400 transition-transform duration-300"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <input type="hidden" name="price" x-model="selected">

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-100 py-1 overflow-hidden"
                                        style="display: none;">
                                        <template x-for="opt in options" :key="opt.value">
                                            <button @click="select(opt)" type="button"
                                                class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-[#36B2B2]/5 hover:text-[#36B2B2] transition-colors flex items-center justify-between">
                                                <span x-text="opt.label"></span>
                                                <svg x-show="selected === opt.value" class="w-4 h-4 text-[#36B2B2]"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Button -->
                            <button
                                class="w-full lg:w-48 bg-gradient-to-r from-[#36B2B2] to-[#2b8f8f] text-white px-8 py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-[#36b2b2]/40 hover:-translate-y-0.5 transition-all text-sm whitespace-nowrap flex items-center justify-center gap-2 group/btn">
                                <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Cari Sekarang</span>
                            </button>
                        </div>

                        <!-- Bottom Recommendations (Horizontal Scroll) -->
                        <div class="px-5 pt-4 pb-5 bg-white relative">
                            <div class="flex items-center justify-between mb-4">
                                <h3
                                    class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Rekomendasi Terpopuler
                                </h3>
                                <a href="#"
                                    class="text-[10px] text-[#36B2B2] font-semibold hover:underline bg-[#36B2B2]/10 px-2 py-1 rounded-full whitespace-nowrap">Lihat
                                    Semua →</a>
                            </div>

                            <!-- Horizontal Scrollable Area -->
                            <div class="flex gap-3 overflow-x-auto pb-4 -mx-5 px-5 custom-scrollbar-x relative select-none"
                                x-data="{
                                    isDown: false,
                                    startX: 0,
                                    scrollLeft: 0,
                                    startDrag(e) {
                                        this.isDown = true;
                                        this.$el.classList.add('cursor-grabbing');
                                        this.startX = e.pageX - this.$el.offsetLeft;
                                        this.scrollLeft = this.$el.scrollLeft;
                                    },
                                    stopDrag() {
                                        this.isDown = false;
                                        this.$el.classList.remove('cursor-grabbing');
                                    },
                                    doDrag(e) {
                                        if(!this.isDown) return;
                                        e.preventDefault();
                                        const x = e.pageX - this.$el.offsetLeft;
                                        const walk = (x - this.startX) * 2;
                                        this.$el.scrollLeft = this.scrollLeft - walk;
                                    }
                                }" x-on:mousedown="startDrag($event)" x-on:mouseleave="stopDrag()"
                                x-on:mouseup="stopDrag()" x-on:mousemove="doDrag($event)">
                                <style>
                                    .custom-scrollbar-x::-webkit-scrollbar {
                                        height: 8px;
                                    }

                                    .custom-scrollbar-x::-webkit-scrollbar-track {
                                        background: transparent;
                                        border-radius: 4px;
                                    }

                                    .custom-scrollbar-x::-webkit-scrollbar-thumb {
                                        background: #d1d5db;
                                        border-radius: 4px;
                                    }

                                    .custom-scrollbar-x::-webkit-scrollbar-thumb:hover {
                                        background: #9ca3af;
                                    }
                                </style>

                                <!-- Card 1 -->
                                <div
                                    class="flex-none w-[280px] bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group cursor-pointer overflow-hidden snap-start">
                                    <div class="relative h-[160px] overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                                            alt="Kos" draggable="false"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 select-none">
                                        <div
                                            class="absolute top-2 left-2 bg-white/95 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-[#36B2B2] shadow-sm uppercase tracking-wide">
                                            Putri</div>
                                        <div
                                            class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            4.8
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h4
                                            class="font-bold text-gray-900 text-sm mb-1 group-hover:text-[#36B2B2] transition-colors truncate">
                                            Kos Mawar Indah</h4>
                                        <p class="text-[10px] text-gray-500 flex items-center gap-1 mb-2">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">Setiabudi, Jakarta</span>
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="text-[#36B2B2] font-extrabold text-sm">Rp 1.5Jt<span
                                                    class="text-[9px] text-gray-400 font-normal">/bln</span></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2 -->
                                <div
                                    class="flex-none w-[280px] bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group cursor-pointer overflow-hidden snap-start">
                                    <div class="relative h-[160px] overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1598928506311-c55dd10a5682?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                                            alt="Kos" draggable="false"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 select-none">
                                        <div
                                            class="absolute top-2 left-2 bg-gray-900/95 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm uppercase tracking-wide">
                                            Putra</div>
                                        <div
                                            class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            4.6
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h4
                                            class="font-bold text-gray-900 text-sm mb-1 group-hover:text-[#36B2B2] transition-colors truncate">
                                            Kos Mahasiswa Tipe A</h4>
                                        <p class="text-[10px] text-gray-500 flex items-center gap-1 mb-2">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">Depok, Jabar</span>
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="text-[#36B2B2] font-extrabold text-sm">Rp 900rb<span
                                                    class="text-[9px] text-gray-400 font-normal">/bln</span></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3 -->
                                <div
                                    class="flex-none w-[280px] bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group cursor-pointer overflow-hidden snap-start">
                                    <div class="relative h-[160px] overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1595521634842-7db518da0b16?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                                            alt="Kos" draggable="false"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 select-none">
                                        <div
                                            class="absolute top-2 left-2 bg-blue-500/95 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm uppercase tracking-wide">
                                            Campur</div>
                                        <div
                                            class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            4.9
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h4
                                            class="font-bold text-gray-900 text-sm mb-1 group-hover:text-[#36B2B2] transition-colors truncate">
                                            Kos Eks. Sudirman</h4>
                                        <p class="text-[10px] text-gray-500 flex items-center gap-1 mb-2">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">Jaksel, Jakarta</span>
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="text-[#36B2B2] font-extrabold text-sm">Rp 2.8Jt<span
                                                    class="text-[9px] text-gray-400 font-normal">/bln</span></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 4 -->
                                <div
                                    class="flex-none w-[280px] bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group cursor-pointer overflow-hidden snap-start">
                                    <div class="relative h-[160px] overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                                            alt="Kos" draggable="false"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 select-none">
                                        <div
                                            class="absolute top-2 left-2 bg-white/95 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-[#36B2B2] shadow-sm uppercase tracking-wide">
                                            Putri</div>
                                        <div
                                            class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            4.7
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h4
                                            class="font-bold text-gray-900 text-sm mb-1 group-hover:text-[#36B2B2] transition-colors truncate">
                                            Kos Bunga Desa</h4>
                                        <p class="text-[10px] text-gray-500 flex items-center gap-1 mb-2">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">Sleman, Yogyakarta</span>
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="text-[#36B2B2] font-extrabold text-sm">Rp 800rb<span
                                                    class="text-[9px] text-gray-400 font-normal">/bln</span></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 5 -->
                                <div
                                    class="flex-none w-[280px] bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group cursor-pointer overflow-hidden snap-start">
                                    <div class="relative h-[160px] overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1540518614846-7eded433c457?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                                            alt="Kos" draggable="false"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 select-none">
                                        <div
                                            class="absolute top-2 left-2 bg-gray-900/95 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm uppercase tracking-wide">
                                            Putra</div>
                                        <div
                                            class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            4.5
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h4
                                            class="font-bold text-gray-900 text-sm mb-1 group-hover:text-[#36B2B2] transition-colors truncate">
                                            Kos Putra Jaya</h4>
                                        <p class="text-[10px] text-gray-500 flex items-center gap-1 mb-2">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">Malang, Jatim</span>
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="text-[#36B2B2] font-extrabold text-sm">Rp 1.2Jt<span
                                                    class="text-[9px] text-gray-400 font-normal">/bln</span></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 6 -->
                                <div
                                    class="flex-none w-[280px] bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group cursor-pointer overflow-hidden snap-start">
                                    <div class="relative h-[160px] overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                                            alt="Kos" draggable="false"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 select-none">
                                        <div
                                            class="absolute top-2 left-2 bg-blue-500/95 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm uppercase tracking-wide">
                                            Campur</div>
                                        <div
                                            class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 text-amber-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            4.8
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h4
                                            class="font-bold text-gray-900 text-sm mb-1 group-hover:text-[#36B2B2] transition-colors truncate">
                                            Kos Harmony</h4>
                                        <p class="text-[10px] text-gray-500 flex items-center gap-1 mb-2">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">Bandung, Jabar</span>
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="text-[#36B2B2] font-extrabold text-sm">Rp 2.1Jt<span
                                                    class="text-[9px] text-gray-400 font-normal">/bln</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gradient fade effect untuk indikasi scroll -->
                            <div
                                class="absolute bottom-5 right-5 w-16 h-8 bg-gradient-to-l from-white to-transparent pointer-events-none">
                            </div>
                        </div>
                    </div>

                    <!-- Social Proof -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <div class="flex -space-x-2.5">
                            <img class="w-11 h-11 rounded-full border-4 border-white object-cover shadow-sm"
                                src="https://i.pravatar.cc/100?img=1" alt="User">
                            <img class="w-11 h-11 rounded-full border-4 border-white object-cover shadow-sm"
                                src="https://i.pravatar.cc/100?img=5" alt="User">
                            <img class="w-11 h-11 rounded-full border-4 border-white object-cover shadow-sm"
                                src="https://i.pravatar.cc/100?img=8" alt="User">
                            <img class="w-11 h-11 rounded-full border-4 border-white object-cover shadow-sm"
                                src="https://i.pravatar.cc/100?img=12" alt="User">
                            <div
                                class="w-11 h-11 rounded-full border-4 border-white bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 shadow-sm">
                                5k+</div>
                        </div>
                        <p class="text-base font-medium text-gray-500">Telah digunakan <span
                                class="text-gray-900 font-bold">5.000+</span> penghuni kos di seluruh Indonesia</p>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-10 pt-8 border-t border-gray-100" data-aos="fade-up" data-aos-delay="300">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Setup cuma 2
                            menit •
                            Gratis selamanya</p>
                        <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-gray-500">
                            <span class="flex items-center gap-2 hover:text-[#36B2B2] transition-colors"><svg
                                    class="w-5 h-5 text-[#36B2B2]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg> Tanpa kartu kredit</span>
                            <span class="flex items-center gap-2 hover:text-[#36B2B2] transition-colors"><svg
                                    class="w-5 h-5 text-[#36B2B2]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg> Cancel kapan saja</span>
                            <span class="flex items-center gap-2 hover:text-[#36B2B2] transition-colors"><svg
                                    class="w-5 h-5 text-[#36B2B2]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg> Verifikasi Akurat</span>
                        </div>
                    </div>
                </div>

            </div>
    </section>
    <!-- Logo Cloud -->
    <section class="py-10 bg-white border-y border-gray-100" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs font-semibold text-gray-400 mb-6 uppercase tracking-wider">Dipercaya & Diliput
                Oleh</p>
            <div
                class="flex justify-center gap-8 md:gap-14 items-center flex-wrap opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="text-lg font-black font-serif italic text-gray-700">TechNews</div>
                <div class="text-lg font-extrabold uppercase tracking-tight text-blue-700">KOMPAS<span
                        class="text-blue-500">.com</span></div>
                <div class="flex items-center gap-1 font-bold text-lg">
                    <div class="w-4 h-4 bg-red-500 rounded-sm"></div> DETIK
                </div>
                <div class="text-lg font-bold tracking-wide text-gray-800">TRIBUN</div>
                <div class="text-lg font-bold text-gray-700">CNN<span class="text-red-500">ID</span></div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section id="solusi" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14" data-aos="fade-up">
                <span
                    class="inline-block px-4 py-1.5 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-xs font-semibold tracking-wide mb-4">Layanan
                    Kami</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-4">Solusi Terbaik Untuk
                    Semua Pihak</h2>
                <p class="text-gray-600 text-lg">Didesain khusus untuk memenuhi kebutuhan pencari kos maupun pemilik
                    properti dengan pengalaman terbaik.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                <!-- For Renters Card -->
                <div class="group relative bg-gradient-to-br from-blue-50/80 to-white rounded-3xl p-8 lg:p-10 border border-blue-100 hover:border-[#36B2B2] hover:shadow-xl hover:shadow-[#36b2b2]/10 transition-all duration-300 overflow-hidden"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-[#36B2B2]/10 to-transparent rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-[#36B2B2] to-[#2b8f8f] rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-[#36b2b2]/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Bagi Anak Kos</h3>
                        <p class="text-gray-600 mb-6 max-w-md">Kenyamanan maksimal dengan transparansi aduan perbaikan
                            dan kemudahan bayar.</p>

                        <ul class="space-y-3.5 mb-7">
                            <li class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-[#36B2B2] mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium">Pembayaran fleksibel (digital/manual)</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-[#36B2B2] mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium">Aduan fasilitas dapat dipantau transparan</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-[#36B2B2] mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium">Pengingat tagihan otomatis (H-7) via WA</span>
                            </li>
                        </ul>

                        <a href="{{ route('signup') }}"
                            class="inline-flex items-center gap-2 text-[#36B2B2] font-semibold hover:gap-3 transition-all group/link">
                            Cari Kos Sekarang
                            <svg class="w-4 h-4 group-hover/link:translate-x-1 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- For Owners Card -->
                <div class="group relative bg-gradient-to-br from-gray-50/80 to-white rounded-3xl p-8 lg:p-10 border border-gray-200 hover:border-gray-300 hover:shadow-xl transition-all duration-300 overflow-hidden"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-gray-200/30 to-transparent rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-gray-800 to-gray-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-gray-400/20">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Bagi Pemilik Kos (Member)</h3>
                        <p class="text-gray-600 mb-6 max-w-md">Pantau ketersediaan kamar, uang masuk, dan keluhan
                            penyewa dalam satu dashboard pintar.</p>

                        <ul class="space-y-3.5 mb-7">
                            <li class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-gray-700 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium">Validasi NIK cegah duplikasi data penyewa</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-gray-700 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium">Manajemen aduan & tanggapan fasilitas</span>
                            </li>
                            <li class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-gray-700 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium">Laporan bulanan/tahunan direkap otomatis</span>
                            </li>
                        </ul>

                        <a href="#harga"
                            class="inline-flex items-center gap-2 text-gray-700 font-semibold hover:text-[#36B2B2] hover:gap-3 transition-all group/link">
                            Kelola Kos Anda
                            <svg class="w-4 h-4 group-hover/link:translate-x-1 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="keunggulan" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span
                    class="inline-block px-4 py-1.5 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-xs font-semibold tracking-wide mb-4">Fitur
                    Unggulan</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Dilengkapi Berbagai Fitur Hebat</h2>
                <p class="text-gray-600 text-lg">Bukan sekadar aplikasi pencari tempat tinggal biasa. Ngekos dibangun
                    dengan teknologi modern untuk pengalaman terbaik.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['title' => 'Validasi Data NIK', 'desc' => 'Pendaftaran tervalidasi dengan NIK agar tidak terjadi duplikasi data member maupun penyewa.', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'from-blue-500 to-blue-600'],
                        ['title' => 'Tagihan Otomatis', 'desc' => 'Tagihan diset otomatis saat kamar disewa, dengan notif peringatan jatuh tempo via WA.', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'from-[#36B2B2] to-[#2b8f8f]'],
                        ['title' => 'Aduan Transparan', 'desc' => 'Anak kos bisa mengajukan perbaikan fasilitas dan melihat status penanganannya secara terbuka.', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'color' => 'from-amber-500 to-amber-600'],
                        ['title' => 'Bayar Fleksibel', 'desc' => 'Dukung bayar tagihan digital/manual langsung ke pemilik melalui input validasi nota asli.', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'from-purple-500 to-purple-600'],
                        ['title' => 'Hierarki Pengguna', 'desc' => 'Atur status penyewaan (harian, mingguan, bulanan) dan status keanggotaan pemilik kos.', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'from-pink-500 to-pink-600'],
                        ['title' => 'Laporan Akurat', 'desc' => 'Mempercepat pemonitoran uang masuk melalui rekap laporan bulanan dan tahunan otomatis.', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'from-green-500 to-green-600']
                    ];
                @endphp

                @foreach ($features as $index => $feature)
                    <div class="group bg-white p-7 rounded-2xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-300"
                        data-aos="fade-up" data-aos-delay="{{ 100 * $index }}">
                        <div
                            class="w-12 h-12 bg-gradient-to-br {{ $feature['color'] }} rounded-xl flex items-center justify-center mb-5 text-white shadow-lg shadow-gray-200/50 group-hover:-translate-y-1 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $feature['icon'] }}" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-2.5">{{ $feature['title'] }}</h4>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimoni" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span
                    class="inline-block px-4 py-1.5 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-xs font-semibold tracking-wide mb-4">Testimoni</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Kata Mereka yang Sudah Menggunakan
                </h2>
                <p class="text-gray-600 text-lg">100+ pemilik kos telah merasakan kemudahan mengelola properti dengan
                    Ngekos.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $testimonials = [
                        ['name' => 'Bu Sari', 'role' => '3 Kos di Jakarta', 'text' => 'Dulu stress ngecek pembayaran manual. Sekarang tinggal buka HP, langsung tau siapa yang udah bayar. Hemat waktu banget!', 'avatar' => '1'],
                        ['name' => 'Pak Budi', 'role' => '5 Kos di Bandung', 'text' => 'Awalnya ragu. Tapi begitu coba, ternyata gampang banget. Anak kos juga seneng karena bisa bayar online.', 'avatar' => '2'],
                        ['name' => 'Mbak Rina', 'role' => '2 Kos di Jogja', 'text' => 'Fitur komplain-nya juara! Langsung tau kalau ada yang rusak, gak perlu nunggu chat berkali-kali.', 'avatar' => '3'],
                        ['name' => 'Pak Herman', 'role' => 'Kos Mahasiswa Depok', 'text' => 'Setup cuma 5 menit, langsung bisa pakai. Interface simpel, istri saya yang gaptek aja bisa operasikan.', 'avatar' => '4'],
                        ['name' => 'Bu Dewi', 'role' => 'Kos Putri Surabaya', 'text' => 'Data KTP penghuni tersimpan rapi. Kalau ada apa-apa, tinggal buka aplikasi. Aman dan praktis!', 'avatar' => '5'],
                        ['name' => 'Mas Andi', 'role' => '50+ Kamar di Malang', 'text' => 'Harga terjangkau, fitur lengkap. Dulu pakai aplikasi lain kena charge per kamar, bisa jutaan sebulan.', 'avatar' => '6']
                    ];
                @endphp

                @foreach ($testimonials as $index => $t)
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-[#36B2B2]/30 hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
                        data-aos="fade-up" data-aos-delay="{{ 100 * $index }}">
                        <div class="flex items-center gap-1 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-700 mb-5 leading-relaxed">"{{ $t['text'] }}"</p>
                        <div class="flex items-center gap-3">
                            <img class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm"
                                src="https://i.pravatar.cc/100?img={{ $t['avatar'] }}" alt="{{ $t['name'] }}">
                            <div>
                                <div class="font-bold text-gray-900 text-sm">{{ $t['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $t['role'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="harga" class="py-20 bg-slate-50" x-data="{ planType: 'bulanan' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span
                    class="inline-block px-4 py-1.5 bg-[#36B2B2]/10 text-[#36B2B2] rounded-full text-xs font-semibold tracking-wide mb-4">Harga
                    Terjangkau</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Mulai Gratis, Upgrade Kalau Butuh
                </h2>
                <p class="text-gray-600 text-lg mb-8">Harga segini cuma seperempat biaya hire admin part-time!</p>

                <!-- Toggle -->
                <div class="inline-flex bg-gray-200/60 p-1.5 rounded-2xl relative mb-4 shadow-inner">
                    <button @click="planType = 'per_kamar'"
                        :class="planType === 'per_kamar' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="relative min-w-[140px] px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300">
                        Per Kamar
                    </button>
                    <button @click="planType = 'bulanan'"
                        :class="planType === 'bulanan' ? 'bg-[#36B2B2] text-white shadow-sm shadow-[#36b2b2]/30' : 'text-gray-500 hover:text-gray-700'"
                        class="relative min-w-[140px] px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300">
                        Paket Bulanan
                    </button>
                </div>
            </div>

            <!-- PER KAMAR - 2 Cards Grid -->
            <div x-show="planType === 'per_kamar'" x-cloak x-transition.opacity.duration.400ms
                class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">

                <!-- Per Kamar Premium -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200 hover:border-blue-500/50 hover:shadow-lg hover:-translate-y-2 transition-all duration-300 flex flex-col"
                    data-aos="fade-up">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">PER KAMAR PREMIUM</h3>
                    <p class="text-gray-500 text-xs mb-5">Standar kelola sistem kos</p>

                    <div class="mb-5">
                        <span class="text-3xl font-extrabold text-gray-900">Rp 3k</span>
                        <span class="text-gray-500 text-sm">/kamar/bln</span>
                    </div>

                    <ul class="space-y-4 mb-8 flex-1">
                        <li class="flex items-start gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Bayar hanya untuk kamar aktif</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Notifikasi tagihan otomatis</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Laporan keuangan standar</span>
                        </li>
                    </ul>

                    <a href="{{ route('signup') }}"
                        class="block w-full py-4 text-center rounded-xl border border-blue-500 text-blue-600 font-bold hover:bg-blue-50 transition shadow-sm">
                        Pilih Premium Per Kamar
                    </a>
                </div>

                <!-- Per Kamar Pro -->
                <div class="relative bg-gradient-to-b from-[#36B2B2] to-[#2b8f8f] rounded-3xl p-6 text-white shadow-xl shadow-[#36b2b2]/20 hover:-translate-y-2 transition-all duration-300 flex flex-col"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-gradient-to-r from-amber-400 to-orange-400 rounded-full text-[10px] font-bold tracking-wider text-white shadow-sm uppercase whitespace-nowrap">
                        Rekomendasi</div>

                    <h3 class="text-lg font-bold mb-1 mt-2">PER KAMAR PRO</h3>
                    <p class="text-white/80 text-xs mb-5">Fitur komplit bayar eceran</p>

                    <div class="mb-5">
                        <span class="text-3xl font-extrabold">Rp 5k</span>
                        <span class="text-white/80 text-sm">/kamar/bln</span>
                    </div>

                    <ul class="space-y-4 mb-8 flex-1">
                        <li class="flex items-start gap-3 text-white/90">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Semua keunggulan Premium</span>
                        </li>
                        <li class="flex items-start gap-3 text-white/90">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Aduan fasilitas terintegrasi</span>
                        </li>
                        <li class="flex items-start gap-3 text-white/90">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Analisis laporan lanjutan</span>
                        </li>
                    </ul>

                    <a href="{{ route('signup') }}"
                        class="block w-full py-4 text-center rounded-xl bg-white text-[#36B2B2] font-bold shadow-md hover:shadow-lg transition">
                        Pilih Pro Per Kamar
                    </a>
                </div>

            </div>

            <!-- PAKET BULANAN - 3 Cards Grid -->
            <div x-show="planType === 'bulanan'" x-cloak x-transition.opacity.duration.400ms
                class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto">
                <!-- Free Plan -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200 hover:border-[#36B2B2]/50 hover:shadow-lg hover:-translate-y-2 transition-all duration-300 flex flex-col"
                    data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">MEMBER BIASA</h3>
                    <p class="text-gray-500 text-xs mb-5">Gratis selamanya</p>

                    <div class="mb-5">
                        <span class="text-3xl font-extrabold text-gray-900">Rp 0</span>
                    </div>

                    <ul class="space-y-3 mb-6 flex-1">
                        <li class="flex items-start gap-2.5 text-gray-700 text-sm">
                            <svg class="w-4 h-4 text-[#36B2B2] shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Kelola hingga 5 kamar</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-gray-700 text-sm">
                            <svg class="w-4 h-4 text-[#36B2B2] shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Validasi NIK penghuni</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-gray-700 text-sm">
                            <svg class="w-4 h-4 text-[#36B2B2] shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Akses fitur dasar aplikasi</span>
                        </li>
                    </ul>

                    <a href="{{ route('signup') }}"
                        class="block w-full py-3 text-center rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:border-[#36B2B2] hover:text-[#36B2B2] transition">
                        Daftar Gratis
                    </a>
                </div>

                <!-- End of FREE PLAN -->
                <div class="relative bg-gradient-to-b from-[#36B2B2] to-[#2b8f8f] rounded-3xl p-6 text-white shadow-xl shadow-[#36b2b2]/20 hover:-translate-y-2 transition-all duration-300 flex flex-col"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-gradient-to-r from-amber-400 to-orange-400 rounded-full text-[10px] font-bold tracking-wider text-white shadow-sm uppercase whitespace-nowrap">
                        Paling Laris</div>

                    <h3 class="text-lg font-bold mb-1 mt-2">MEMBER PREMIUM</h3>
                    <p class="text-white/80 text-xs mb-5">Tanpa batasan jumlah kamar</p>

                    <div class="mb-5">
                        <span class="text-3xl font-extrabold">Rp 50k</span>
                        <span class="text-white/80 text-sm">/bulan</span>
                    </div>

                    <ul class="space-y-3 mb-6 flex-1">
                        <li class="flex items-start gap-2.5 text-white/90 text-sm">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Kelola <b class="font-bold">Unlimited</b> kamar kos</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-white/90 text-sm">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Manajemen komplain responsif</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-white/90 text-sm">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Laporan keuangan terpusat</span>
                        </li>
                    </ul>

                    <a href="{{ route('signup') }}"
                        class="block w-full py-3 text-center rounded-xl bg-white text-[#36B2B2] text-sm font-bold shadow-md hover:shadow-lg transition">
                        Daftar Premium
                    </a>
                </div>

                <!-- Pro Plan -->
                <div class="bg-gray-900 rounded-3xl p-6 text-white hover:shadow-2xl hover:shadow-gray-900/40 hover:-translate-y-2 transition-all duration-300 flex flex-col"
                    data-aos="fade-up" data-aos-delay="400">
                    <h3 class="text-lg font-bold mb-1">MEMBER PRO</h3>
                    <p class="text-gray-400 text-xs mb-5">Untuk juragan banyak lokasi kos</p>

                    <div class="mb-5">
                        <span class="text-3xl font-extrabold">Rp 80k</span>
                        <span class="text-gray-400 text-sm">/bulan</span>
                    </div>

                    <ul class="space-y-3 mb-6 flex-1">
                        <li class="flex items-start gap-2.5 text-gray-300 text-sm">
                            <svg class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Semua keunggulan Premium</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-gray-300 text-sm">
                            <svg class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Kelola <b class="font-bold">Multi-lokasi/Cabang</b></span>
                        </li>
                        <li class="flex items-start gap-2.5 text-gray-300 text-sm">
                            <svg class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Analisis laporan tingkat lanjut</span>
                        </li>
                    </ul>

                    <a href="{{ route('signup') }}"
                        class="block w-full py-3 text-center rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white text-sm font-bold hover:shadow-lg transition">
                        Pilih Pro
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" data-aos="zoom-in">
            <div
                class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] overflow-hidden relative shadow-2xl py-14 px-6 md:px-12 text-center text-white">
                <!-- Decorative Elements -->
                <div
                    class="absolute top-0 right-0 -m-20 w-72 h-72 bg-[#36B2B2] rounded-full mix-blend-screen opacity-20 blur-3xl">
                </div>
                <div
                    class="absolute bottom-0 left-0 -m-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-screen opacity-20 blur-3xl">
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-5 relative z-10 leading-tight">Siap
                    Merasakan Kemudahan Kelola Kos?</h2>
                <p class="text-gray-300 text-lg md:text-xl mb-8 max-w-2xl mx-auto relative z-10">Daftarkan akun gratis
                    sekarang. Gak pakai ribet, gak ada biaya tersembunyi.</p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                    <a href="{{ route('signup') }}"
                        class="px-8 py-4 bg-[#36B2B2] hover:bg-[#2b8f8f] font-bold text-lg rounded-xl transition shadow-lg shadow-[#36b2b2]/50 hover:shadow-xl hover:-translate-y-1 transform">
                        Buat Akun Gratis
                    </a>
                    <a href="#solusi"
                        class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-sm font-bold text-lg rounded-xl border border-white/20 hover:-translate-y-1 transform transition">
                        Pelajari Lebih Lanjut
                    </a>
                </div>

                <p class="text-gray-400 text-sm mt-6 relative z-10">⏱️ Setup cuma 2 menit • 🔄 Cancel kapan saja</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-10 mb-12">
                <!-- Brand -->
                <div class="lg:w-1/3">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div
                            class="w-9 h-9 bg-gradient-to-br from-[#36B2B2] to-[#2b8f8f] rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <span class="font-bold text-2xl text-gray-900">Nge<span class="text-[#36B2B2]">kos</span></span>
                    </div>
                    <p class="text-gray-500 max-w-sm leading-relaxed mb-5">Platform cerdas penemuan & manajemen kos
                        terpadu, membantu koneksi penyewa dan pemilik properti secara instan.</p>
                    <div class="flex gap-3">
                        <a href="#"
                            class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-[#36B2B2] hover:text-white transition"><svg
                                class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg></a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-[#36B2B2] hover:text-white transition"><svg
                                class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg></a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-[#36B2B2] hover:text-white transition"><svg
                                class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                            </svg></a>
                    </div>
                </div>

                <!-- Links -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 lg:gap-12 text-sm">
                    <div>
                        <h4 class="font-bold text-gray-900 mb-4">Produk</h4>
                        <ul class="space-y-3">
                            <li><a href="#solusi" class="text-gray-600 hover:text-[#36B2B2] transition">Untuk Pencari
                                    Kos</a></li>
                            <li><a href="#solusi" class="text-gray-600 hover:text-[#36B2B2] transition">Untuk Pemilik
                                    Kos</a></li>
                            <li><a href="#keunggulan" class="text-gray-600 hover:text-[#36B2B2] transition">Fitur</a>
                            </li>
                            <li><a href="#harga" class="text-gray-600 hover:text-[#36B2B2] transition">Harga</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-4">Perusahaan</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 hover:text-[#36B2B2] transition">Tentang Kami</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-[#36B2B2] transition">Karir</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-[#36B2B2] transition">Blog</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-[#36B2B2] transition">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-4">Legal</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 hover:text-[#36B2B2] transition">Syarat & Ketentuan</a>
                            </li>
                            <li><a href="#" class="text-gray-600 hover:text-[#36B2B2] transition">Kebijakan Privasi</a>
                            </li>
                            <li><a href="#" class="text-gray-600 hover:text-[#36B2B2] transition">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom -->
            <div
                class="pt-8 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4 text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} Ngekos. All rights reserved.</p>
                <div class="flex items-center gap-2">
                    <span>🇮🇩</span>
                    <span>Dibuat dengan ❤️ di Indonesia</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- AOS Animation Library JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                once: true, // whether animation should happen only once - while scrolling down
                offset: 50, // offset (in px) from the original trigger point
                duration: 800, // values from 0 to 3000, with step 50ms
                easing: 'ease-out-cubic', // default easing for AOS animations
            });
        });
    </script>
</body>

</html>
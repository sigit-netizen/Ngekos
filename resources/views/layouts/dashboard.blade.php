@extends('layouts.fullscreen-layout')

@section('content')
    <style>
        /* Floating Animation from Welcome Page */
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

        .animate-float-delayed {
            animation: float 4s ease-in-out infinite;
            animation-delay: 2s;
        }

        /* Custom Scrollbar for Main Content */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

    <!-- ====== PAGE LOADING OVERLAY ====== -->
    <style>
        #page-loader {
            position: fixed !important;
            inset: 0;
            background: rgba(248, 250, 252, 0.75);
            backdrop-filter: blur(18px) saturate(1.4);
            -webkit-backdrop-filter: blur(18px) saturate(1.4);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.45s ease, visibility 0.45s ease;
        }

        #page-loader.hidden-loader {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Teal underline pulse */
        .ldr-line {
            width: 48px;
            height: 3px;
            background: linear-gradient(90deg, #36B2B2, #8b5cf6);
            border-radius: 99px;
            margin-top: 14px;
            animation: ldr-pulse 1.4s ease-in-out infinite;
        }

        @keyframes ldr-pulse {

            0%,
            100% {
                opacity: 0.4;
                transform: scaleX(0.6);
            }

            50% {
                opacity: 1;
                transform: scaleX(1);
            }
        }
    </style>

    <div id="page-loader">
        <div style="display:flex; align-items:baseline; gap:0; line-height:1;">
            <span style="font-size:30px; font-weight:900; color:#111827; letter-spacing:-1.5px;">K</span>
            <span style="font-size:30px; font-weight:900; color:#36B2B2; letter-spacing:-1.5px;">Ngekos</span>
            <span style="font-size:17px; font-weight:600; color:#9ca3af; margin-left:3px;">.id</span>
        </div>
        <div class="ldr-line"></div>
    </div>

    <script>
        // Hide loader as soon as DOM is interactive (fast), not waiting for images/fonts
        (function () {
            function hideLdr() {
                var l = document.getElementById('page-loader');
                if (l) {
                    l.classList.add('hidden-loader');
                    setTimeout(function () { l.style.display = 'none'; }, 500);
                }
            }
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(hideLdr, 150);
            } else {
                document.addEventListener('DOMContentLoaded', function () { setTimeout(hideLdr, 150); });
            }
        })();
    </script>
    <!-- ====== END LOADING OVERLAY ====== -->







    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-slate-50 font-inter overflow-hidden relative">

        <!-- Background Accents -->
        <div
            class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] rounded-full bg-[#36B2B2]/10 blur-[100px] animate-float pointer-events-none z-0">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-5%] w-[35vw] h-[35vw] rounded-full bg-blue-400/10 blur-[100px] animate-float-delayed pointer-events-none z-0">
        </div>

        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            @click="sidebarOpen = false" style="display: none;"></div>

        <!-- Sidebar -->
        @include('layouts.dashboard-sidebar')

        <!-- Main ContentWrapper -->
        <div class="flex-1 flex flex-col min-w-0 z-10 overflow-hidden">

            <!-- Header (Mobile Toggle & Profile) -->
            @include('layouts.dashboard-header')

            <!-- Scrollable Main Container -->
            <main class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6 lg:p-8 relative z-0">
                @if (session('success_login') || session('success'))
                    @php $successMessage = session('success_login') ?? session('success'); @endphp
                    <div class="mb-6 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 p-4 shadow-sm"
                        x-data="{ show: true }" x-show="show" x-transition.opacity>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-emerald-800">
                                    {{ $successMessage }}
                                </p>
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

                @yield('dashboard-content')
            </main>
        </div>
    </div>
@endsection
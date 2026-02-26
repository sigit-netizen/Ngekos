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
                @yield('dashboard-content')
            </main>
        </div>
    </div>
@endsection
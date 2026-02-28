<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi | Ngekos.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .bg-gradient {
            background: radial-gradient(circle at top right, #36B2B2, transparent), radial-gradient(circle at bottom left, #1D4ED8, transparent);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6 bg-gradient overflow-hidden">

    <!-- Floating Blobs for Aesthetic -->
    <div
        class="absolute top-0 -left-4 w-72 h-72 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
    </div>
    <div
        class="absolute top-0 -right-4 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
    </div>
    <div
        class="absolute -bottom-8 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000">
    </div>

    <div class="max-w-md w-full relative group">
        <!-- Dashboard Card -->
        <div
            class="glass border border-white/40 shadow-2xl rounded-3xl p-10 text-center transform transition-all duration-500 hover:scale-[1.02]">

            <!-- Icon/Illustration Container -->
            <div class="relative w-32 h-32 mx-auto mb-8">
                <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping opacity-20"></div>
                <div class="relative bg-white rounded-full p-6 shadow-xl flex items-center justify-center">
                    <svg class="w-16 h-16 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <h1 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Hampir Siap! 🚀</h1>
            <p class="text-gray-500 leading-relaxed mb-8">
                Halo, <span class="text-emerald-600 font-bold">{{ $pendingUser->name }}</span>! Akun Anda sedang dalam
                tahap verifikasi oleh tim Superadmin kami.
                Mohon tunggu sejenak sementara kami memastikan semuanya aman.
            </p>

            <!-- Status Indicator -->
            <div
                class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full text-sm font-black mb-10 border border-emerald-100 uppercase tracking-widest">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                Status: Pending Verification
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 gap-4">
                <a href="{{ route('home') }}"
                    class="py-4 bg-gray-900 text-white rounded-2xl font-bold shadow-xl shadow-gray-900/20 hover:bg-black transition-all transform active:scale-95 text-center">
                    Kembali Ke Beranda
                </a>

                <a href="{{ route('login') }}"
                    class="w-full py-4 text-emerald-600 font-bold hover:bg-emerald-50 rounded-2xl transition-all text-center block">
                    Masuk Dengan Akun Lain
                </a>
            </div>
        </div>

        <!-- Footer Text -->
        <p class="text-white/60 text-[10px] text-center mt-8 font-bold uppercase tracking-[0.3em]">
            &copy; 2026 Ngekos.id • Secure Verification System
        </p>
    </div>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>

</html>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditolak | Ngekos.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

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
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6 overflow-hidden"
    style="background: radial-gradient(circle at top right, #f87171, transparent), radial-gradient(circle at bottom left, #1D4ED8, transparent);">

    <!-- Floating Blobs -->
    <div
        class="absolute top-0 -left-4 w-72 h-72 bg-red-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
    </div>
    <div
        class="absolute top-0 -right-4 w-72 h-72 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
    </div>
    <div
        class="absolute -bottom-8 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000">
    </div>

    <div class="max-w-md w-full relative">
        <div class="glass border border-white/40 shadow-2xl rounded-3xl p-10 text-center">

            <!-- Icon -->
            <div class="relative w-28 h-28 mx-auto mb-8">
                <div class="absolute inset-0 bg-red-500 rounded-full animate-ping opacity-10"></div>
                <div class="relative bg-white rounded-full p-5 shadow-xl flex items-center justify-center">
                    <svg class="w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">Pendaftaran Ditolak 😞</h1>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Maaf, <span class="text-red-600 font-bold">{{ $pendingUser->name }}</span>,
                pendaftaran Anda tidak dapat disetujui saat ini.
            </p>

            <!-- Status -->
            <div
                class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-full text-xs font-black mb-6 border border-red-100 uppercase tracking-widest">
                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                Status: Ditolak
            </div>

            <!-- Rejection Details Card -->
            <div class="bg-red-50/70 rounded-2xl p-5 mb-6 text-left border border-red-100">
                <h3 class="text-xs font-black text-red-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                    Pesan dari Super Admin
                </h3>
                <div class="bg-white/80 rounded-xl p-4 border border-red-100">
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">
                        "{{ $pendingUser->rejection_reason }}"
                    </p>
                </div>
                <div class="mt-3 pt-3 border-t border-red-200 space-y-1.5">
                    <div class="flex gap-2 text-xs">
                        <span class="text-gray-500 font-bold">Email:</span>
                        <span class="text-gray-800 font-bold">{{ $pendingUser->email }}</span>
                    </div>
                    <div class="flex gap-2 text-xs">
                        <span class="text-gray-500 font-bold">NIK:</span>
                        <span class="text-gray-800 font-bold">{{ $pendingUser->nik }}</span>
                    </div>
                </div>
            </div>

            <!-- Recommendation Box -->
            <div class="bg-blue-50 rounded-2xl p-4 mb-8 border border-blue-100 text-left">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-blue-800 text-xs font-bold mb-1">Rekomendasi:</p>
                        <p class="text-blue-700 text-xs leading-relaxed">
                            Silakan periksa dan perbaiki data Anda, lalu lakukan <strong>pendaftaran ulang</strong>
                            dengan data yang benar.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('register') }}"
                    class="py-4 bg-[#36B2B2] text-white rounded-2xl font-black shadow-xl shadow-[#36B2B2]/20 hover:bg-[#2D8E8E] transition-all transform active:scale-95 text-center text-sm">
                    🔄 Daftar Ulang
                </a>
                <a href="{{ route('home') }}"
                    class="py-4 text-gray-600 font-bold hover:bg-gray-100 rounded-2xl transition-all text-center text-sm">
                    Kembali Ke Beranda
                </a>
            </div>
        </div>

        <p class="text-white/60 text-[10px] text-center mt-8 font-bold uppercase tracking-[0.3em]">
            &copy; 2026 Ngekos.id &bull; Verification System
        </p>
    </div>
</body>

</html>
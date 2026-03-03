<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | Ngekos.id</title>
    <link rel="icon" type="image/svg+xml" href="/images/logo/logo-icon.svg?v=2">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .modal-open {
            overflow: hidden !important;
            padding-right: 15px;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Modern SweetAlert2 UI - White & Gradient Border */
        .modern-swal-popup {
            background-color: transparent !important;
            background-image: linear-gradient(white, white), linear-gradient(135deg, #36B2B2, #000000) !important;
            background-clip: padding-box, border-box !important;
            background-origin: padding-box, border-box !important;
            border: 2px solid transparent !important;
            padding: 3rem !important;
            border-radius: 2.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
        }

        .modern-swal-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            margin-bottom: 0.8rem !important;
            padding: 0 !important;
            letter-spacing: -0.01em !important;
        }

        .modern-swal-text {
            font-size: 0.95rem !important;
            color: #64748b !important;
            line-height: 1.6 !important;
            margin-bottom: 2.5rem !important;
            padding: 0 !important;
            font-weight: 400 !important;
        }

        .modern-swal-confirm {
            background: #36B2B2 !important;
            color: white !important;
            padding: 1rem 2.2rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border-radius: 1.5rem !important;
            box-shadow: 0 4px 6px -1px rgba(54, 178, 178, 0.2) !important;
            transition: all 0.2s ease !important;
            margin: 0.5rem !important;
            border: none !important;
            cursor: pointer !important;
            text-transform: none !important;
        }

        .modern-swal-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 20px 25px -5px rgba(54, 178, 178, 0.35) !important;
            background: #2d9696 !important;
        }

        .modern-swal-cancel {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
            padding: 1rem 2.2rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border-radius: 1.5rem !important;
            transition: all 0.2s ease !important;
            margin: 0.5rem !important;
            border: none !important;
            cursor: pointer !important;
            text-transform: none !important;
        }

        .modern-swal-cancel:hover {
            background-color: #e2e8f0 !important;
            color: #475569 !important;
        }

        .swal2-actions {
            margin-top: 1rem !important;
            gap: 0.5rem !important;
        }

        /* Dark Mode Consistency */
        .dark .modern-swal-popup {
            background-image: linear-gradient(white, white), linear-gradient(135deg, #36B2B2, #000000) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
        }
    </style>

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                // ... (rest of theme logic remains original)
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('profile', {
                isOpen: false,
                open() {
                    this.isOpen = true;
                    document.body.classList.add('modal-open');
                },
                close() {
                    this.isOpen = false;
                    document.body.classList.remove('modal-open');
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Global Helpers -->
    <script>
        window.swalConfirm = (title, text, icon = 'question') => {
            return Swal.fire({
                title: title,
                text: text,
                showCancelButton: true,
                confirmButtonText: 'Konfirmasi',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'modern-swal-popup',
                    title: 'modern-swal-title',
                    htmlContainer: 'modern-swal-text',
                    confirmButton: 'modern-swal-confirm',
                    cancelButton: 'modern-swal-cancel'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
            });
        };

        window.swalToast = (title, icon = 'success') => {
            Swal.fire({
                title: title,
                icon: icon,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border-none bg-white dark:bg-slate-800 p-4'
                }
            });
        };
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark', 'bg-gray-900');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark', 'bg-gray-900');
            }
        })();
    </script>
</head>

<body x-data="{ 'loaded': true}" x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
const checkMobile = () => {
    if (window.innerWidth < 1280) {
        $store.sidebar.setMobileOpen(false);
        $store.sidebar.isExpanded = false;
    } else {
        $store.sidebar.isMobileOpen = false;
        $store.sidebar.isExpanded = true;
    }
};
window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader />
    {{-- preloader end --}}

    @yield('content')

    @stack('modals')
</body>

@stack('scripts')

</html>
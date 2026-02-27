@extends('layouts.dashboard')

@section('dashboard-content')
    <!-- Header Section -->
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-sm border border-white/50 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6"
        data-aos="fade-up" data-aos-duration="800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Statistik Sistem 📊</h1>
            <p class="text-gray-500 text-sm">Monitor pertumbuhan pengguna dan performa member secara real-time.</p>
        </div>

        <!-- Filters -->
        <form action="{{ route('superadmin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative" x-data="{ open: false }">
                <select name="status_member" onchange="this.form.submit()"
                    class="appearance-none bg-gray-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-gray-700 pr-10 focus:ring-2 focus:ring-[#36B2B2]/20 cursor-pointer">
                    <option value="all" {{ $filters['status_member'] == 'all' ? 'selected' : '' }}>Semua Member</option>
                    <option value="active" {{ $filters['status_member'] == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ $filters['status_member'] == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="relative">
                <select name="year" onchange="this.form.submit()"
                    class="appearance-none bg-gray-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-gray-700 pr-10 focus:ring-2 focus:ring-[#36B2B2]/20 cursor-pointer">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $filters['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            {{-- Month Filter --}}
            <div class="relative">
                <select name="month" onchange="this.form.submit()"
                    class="appearance-none bg-gray-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-gray-700 pr-10 focus:ring-2 focus:ring-[#36B2B2]/20 cursor-pointer">
                    <option value="" {{ $filters['month'] == '' ? 'selected' : '' }}>Semua Bulan</option>
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $i => $mName)
                        <option value="{{ $i + 1 }}" {{ $filters['month'] == ($i + 1) ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Grid -->
    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:24px; margin-bottom:32px;">
        <!-- Total Pengguna -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group"
            data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <span
                    class="text-[10px] font-black text-blue-500 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-wider">Total</span>
            </div>
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Total Pengguna</h3>
            <div class="text-2xl font-black text-gray-900">{{ number_format($stats['total_overall']) }}</div>
        </div>

        <!-- User (Anak Kos) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group"
            data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-cyan-50 rounded-xl text-cyan-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <span
                    class="text-[10px] font-black text-cyan-500 bg-cyan-50 px-2 py-1 rounded-lg uppercase tracking-wider">Anak
                    Kos</span>
            </div>
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Jumlah User</h3>
            <div class="text-2xl font-black text-gray-900">{{ number_format($stats['total_users']) }}</div>
        </div>

        <!-- Member (Owner) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group"
            data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-50 rounded-xl text-amber-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <span
                    class="text-[10px] font-black text-amber-500 bg-amber-50 px-2 py-1 rounded-lg uppercase tracking-wider">Pemilik
                    Kos</span>
            </div>
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Jumlah Member</h3>
            <div class="text-2xl font-black text-gray-900">{{ number_format($stats['total_members']) }}</div>
        </div>

        <!-- Active Packages -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group"
            data-aos="fade-up" data-aos-delay="400">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span
                    class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-wider">Aktif</span>
            </div>
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Member Aktif</h3>
            <div class="text-2xl font-black text-gray-900">{{ number_format($stats['active_members']) }}</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-8 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm" data-aos="fade-up" data-aos-delay="500">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Grafik Pertumbuhan</h3>
                    <p class="text-sm text-gray-500">
                        @if($filters['month'])
                            Per-hari registrasi
                            {{ \Carbon\Carbon::createFromDate($filters['year'], $filters['month'], 1)->format('F Y') }}
                        @else
                            Per-bulan registrasi tahun {{ $filters['year'] }}
                        @endif
                        &mdash; Sumbu X: {{ $chart['mode'] === 'daily' ? 'Tanggal' : 'Bulan' }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span style="display:inline-block; width:14px; height:14px; border-radius:50%; background:#36B2B2;"></span>
                        <span class="text-xs font-bold" style="color:#36B2B2;">User (Anak Kos)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span style="display:inline-block; width:14px; height:14px; border-radius:50%; background:#8b5cf6;"></span>
                        <span class="text-xs font-bold" style="color:#8b5cf6;">Member (Pemilik Kos)</span>
                    </div>
                </div>
            </div>
            <div class="h-[400px]">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('growthChart').getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chart['labels']) !!},
                        datasets: [
                            {
                                label: 'User (Anak Kos)',
                                data: {!! json_encode($chart['user_growth']) !!},
                                borderColor: '#36B2B2',
                                backgroundColor: 'rgba(54, 178, 178, 0.12)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#36B2B2',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 3,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                pointHoverBackgroundColor: '#36B2B2',
                            },
                            {
                                label: 'Member (Pemilik Kos)',
                                data: {!! json_encode($chart['member_growth']) !!},
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.12)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#8b5cf6',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 3,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                pointHoverBackgroundColor: '#8b5cf6',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 14,
                                titleFont: { size: 14, weight: 'bold', family: 'Inter, sans-serif' },
                                bodyFont: { size: 13, family: 'Inter, sans-serif' },
                                cornerRadius: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(ctx) {
                                        let v = ctx.raw;
                                        if (v >= 1000000) v = (v/1000000).toFixed(1) + 'M';
                                        else if (v >= 1000) v = (v/1000).toFixed(1) + 'k';
                                        return ' ' + ctx.dataset.label + ': ' + v;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: { size: 11, weight: 'bold' },
                                    color: '#9ca3af',
                                    // Auto-format large numbers: 1500 → 1.5k, 10000 → 10k
                                    callback: function (value) {
                                        if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                        if (value >= 1000) return (value / 1000).toFixed(value % 1000 === 0 ? 0 : 1) + 'k';
                                        return value;
                                    },
                                    maxTicksLimit: 8
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { size: 11, weight: 'bold' },
                                    color: '#9ca3af',
                                    maxRotation: 45,
                                    autoSkip: true,
                                    maxTicksLimit: {!! $chart['mode'] === 'daily' ? 31 : 12 !!}
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
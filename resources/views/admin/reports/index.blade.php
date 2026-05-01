@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            Actionable Insights & Reports
        </h2>

        <div class="flex items-center gap-3">
            <form action="{{ route('admin.reports') }}" method="GET" id="filterForm">
                <select name="range" onchange="this.form.submit()" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-300">
                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="7_days" {{ $range === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="this_month" {{ $range === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="this_year" {{ $range === 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-3 2xl:gap-7.5 mb-6">
        <!-- Revenue Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($totalRevenue, 2) }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs font-medium text-success-600">
                <span>{{ ucfirst(str_replace('_', ' ', $range)) }} performance</span>
            </div>
        </div>

        <!-- Bookings Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Bookings</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalBookings) }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs font-medium text-success-600">
                <span>{{ $totalBookings }} sessions scheduled</span>
            </div>
        </div>

        <!-- Users Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">New User Growth</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($newUserCount) }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs font-medium text-success-600">
                <span>Registered this period</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Revenue & Bookings Trend -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Revenue & Booking Trends</h3>
            <div class="h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- User Registrations -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">User Registration Volume</h3>
            <div class="h-[300px]">
                <canvas id="userChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Export Data</h3>
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                Background Processing Enabled
            </span>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="flex flex-col justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Orders Report</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Detailed list of all instrument orders including tax breakdowns and payment statuses.</p>
                </div>
                <form action="{{ route('admin.reports.export') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="type" value="orders">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export CSV (Excel)
                    </button>
                </form>
            </div>

            <div class="flex flex-col justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Singer Earnings Report</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Summary of approved bookings and calculated payouts based on current singer fees.</p>
                </div>
                <form action="{{ route('admin.reports.export') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="type" value="singer_earnings">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export CSV (Excel)
                    </button>
                </form>
            </div>
        </div>
        <p class="mt-4 text-[11px] text-gray-400 dark:text-gray-500 italic">
            * Large exports are processed in the background. You will receive a system notification with the download link once ready.
        </p>
    </div>

    <!-- Chart.js scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = @json($trends['labels']);
            
            // Revenue Chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Revenue (₹)',
                            data: @json($trends['revenue']),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Bookings (Count)',
                            data: @json($trends['bookings']),
                            borderColor: '#10b981',
                            backgroundColor: 'transparent',
                            borderDash: [5, 5],
                            tension: 0.4,
                            yAxisID: 'y1'
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
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: false }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                        }
                    }
                }
            });

            // User Growth Chart
            new Chart(document.getElementById('userChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'New Users',
                        data: @json($trends['users']),
                        backgroundColor: '#8b5cf6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: false }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endsection

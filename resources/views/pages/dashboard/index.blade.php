@extends('layouts.app')

@section('content')
<!-- Action-Oriented Header -->
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black dark:text-white">Dashboard Overview</h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Here is a summary of your event and instrument operations.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.reports') }}" class="inline-flex items-center justify-center rounded-lg border border-stroke bg-white px-5 py-2.5 text-center font-medium text-black hover:bg-gray-50 transition-colors shadow-sm dark:border-strokedark dark:bg-meta-4 dark:text-white dark:hover:bg-opacity-90">
            Generate Report
        </a>
        <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-center font-medium text-white shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-indigo-800">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            New Booking
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
    <!-- Metric: Total Bookings -->
    <div class="group rounded-2xl border border-stroke bg-white py-6 px-7.5 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 dark:border-strokedark dark:bg-boxdark overflow-hidden relative">
        <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-indigo-50 to-transparent opacity-50 dark:from-indigo-900/20 transition-transform duration-300 group-hover:scale-110"></div>
        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 relative z-10 transition-colors duration-300 group-hover:bg-indigo-600 group-hover:text-white dark:group-hover:bg-indigo-500">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="mt-6 flex items-end justify-between relative z-10">
            <div>
                <h4 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ $totalBookings }}</h4>
                <span class="text-xs uppercase tracking-tight font-bold text-gray-500 dark:text-gray-400 mt-1 block">Total Bookings</span>
            </div>
        </div>
    </div>

    <!-- Metric: Pending Bookings -->
    <div class="group rounded-2xl border border-stroke bg-white py-6 px-7.5 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 dark:border-strokedark dark:bg-boxdark overflow-hidden relative">
        <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-amber-50 to-transparent opacity-50 dark:from-amber-900/20 transition-transform duration-300 group-hover:scale-110"></div>
        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 relative z-10 transition-colors duration-300 group-hover:bg-amber-500 group-hover:text-white dark:group-hover:bg-amber-500">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="mt-6 flex items-end justify-between relative z-10">
            <div>
                <h4 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ $pendingBookings }}</h4>
                <span class="text-xs uppercase tracking-tight font-bold text-gray-500 dark:text-gray-400 mt-1 block">Pending Bookings</span>
            </div>
        </div>
    </div>

    <!-- Metric: Total Revenue -->
    <div class="group rounded-2xl border border-stroke bg-white py-6 px-7.5 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 dark:border-strokedark dark:bg-boxdark overflow-hidden relative">
        <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-emerald-50 to-transparent opacity-50 dark:from-emerald-900/20 transition-transform duration-300 group-hover:scale-110"></div>
        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 relative z-10 transition-colors duration-300 group-hover:bg-emerald-500 group-hover:text-white dark:group-hover:bg-emerald-500">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
        </div>
        <div class="mt-6 flex items-end justify-between relative z-10">
            <div>
                <h4 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">₹{{ number_format($totalRevenue, 2) }}</h4>
                <span class="text-xs uppercase tracking-tight font-bold text-gray-500 dark:text-gray-400 mt-1 block">Total Revenue</span>
            </div>
        </div>
    </div>

    <!-- Metric: Out of Stock -->
    <div class="group rounded-2xl border border-stroke bg-white py-6 px-7.5 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 dark:border-strokedark dark:bg-boxdark overflow-hidden relative">
        <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-rose-50 to-transparent opacity-50 dark:from-rose-900/20 transition-transform duration-300 group-hover:scale-110"></div>
        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 relative z-10 transition-colors duration-300 group-hover:bg-rose-500 group-hover:text-white dark:group-hover:bg-rose-500">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <div class="mt-6 flex items-end justify-between relative z-10">
            <div>
                <h4 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ $outOfStock }}</h4>
                <span class="text-xs uppercase tracking-tight font-bold text-gray-500 dark:text-gray-400 mt-1 block">Out of Stock</span>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
    <!-- Booking Trends Chart -->
    <div class="col-span-12 rounded-2xl border border-stroke bg-white p-7 shadow-md dark:border-strokedark dark:bg-boxdark xl:col-span-8">
        <div class="mb-6 justify-between gap-4 sm:flex border-b border-stroke pb-4 dark:border-strokedark">
            <div>
                <h4 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                    Booking Trends
                </h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Overview of booking status distribution.</p>
            </div>
        </div>
        <div id="bookingChart" class="-ml-5"></div>
    </div>

    <!-- Inventory Usage Chart -->
    <div class="col-span-12 rounded-2xl border border-stroke bg-white p-7 shadow-md dark:border-strokedark dark:bg-boxdark xl:col-span-4">
        <div class="mb-6 justify-between gap-4 sm:flex border-b border-stroke pb-4 dark:border-strokedark">
            <div>
                <h4 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                    Inventory Usage
                </h4>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Distribution by category.</p>
            </div>
        </div>
        <div id="inventoryChart" class="mx-auto flex justify-center mt-6"></div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include ApexCharts if not bundled -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Determine theme mode for chart styling
    const isDarkMode = document.documentElement.classList.contains('dark');
    const labelColor = isDarkMode ? '#AEB7C0' : '#64748B';

    // Booking Chart
    var bookingOptions = {
        series: [{
            name: 'Bookings',
            data: {!! json_encode($bookingChartData) !!}
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#4F46E5', '#10B981', '#F43F5E'],
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: false,
                distributed: true,
                columnWidth: '45%'
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: {!! json_encode($bookingChartLabels) !!},
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: labelColor, fontSize: '13px' }
            }
        },
        yaxis: {
            labels: {
                style: { colors: labelColor, fontSize: '13px' }
            }
        },
        grid: {
            borderColor: isDarkMode ? '#2E3A47' : '#E2E8F0',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        legend: { show: false }
    };
    var bookingChart = new ApexCharts(document.querySelector("#bookingChart"), bookingOptions);
    bookingChart.render();

    // Inventory Chart
    var inventoryOptions = {
        series: {!! json_encode($inventoryChartData) !!},
        chart: {
            type: 'donut',
            width: '100%',
            height: 320,
            fontFamily: 'inherit'
        },
        colors: ['#4F46E5', '#10B981', '#F59E0B', '#3B82F6', '#8B5CF6'],
        labels: {!! json_encode($inventoryChartLabels) !!},
        stroke: {
            colors: isDarkMode ? ['#24303F'] : ['#FFFFFF'],
            width: 3
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'bottom',
            labels: {
                colors: labelColor
            },
            markers: {
                radius: 12
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 280 }
            }
        }]
    };
    var inventoryChart = new ApexCharts(document.querySelector("#inventoryChart"), inventoryOptions);
    inventoryChart.render();
});
</script>
@endpush

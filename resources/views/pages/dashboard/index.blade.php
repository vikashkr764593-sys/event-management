@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
    <!-- Metric: Total Bookings -->
    <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 0C4.9 0 0 4.9 0 11C0 17.1 4.9 22 11 22C17.1 22 22 17.1 22 11C22 4.9 17.1 0 11 0ZM11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20Z" fill=""/>
                <path d="M12 5H10V12L16 15.5L17 13.5L12 10.5V5Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">{{ $totalBookings }}</h4>
                <span class="text-sm font-medium">Total Bookings</span>
            </div>
        </div>
    </div>

    <!-- Metric: Pending Bookings -->
    <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-warning dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- warning icon -->
                <path d="M11 0C4.92 0 0 4.92 0 11C0 17.08 4.92 22 11 22C17.08 22 22 17.08 22 11C22 4.92 17.08 0 11 0ZM12 16H10V14H12V16ZM12 12H10V6H12V12Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">{{ $pendingBookings }}</h4>
                <span class="text-sm font-medium">Pending Bookings</span>
            </div>
        </div>
    </div>

    <!-- Metric: Total Revenue -->
    <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-success dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- dollar icon -->
                <path d="M11 0C4.9 0 0 4.9 0 11C0 17.1 4.9 22 11 22C17.1 22 22 17.1 22 11C22 4.9 17.1 0 11 0ZM12 17V15H14C15.1 15 16 14.1 16 13V11C16 9.9 15.1 9 14 9H10V7H16V5H12V3H10V5H8C6.9 5 6 5.9 6 7V9C6 10.1 6.9 11 8 11H12V13H6V15H10V17H12Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">₹{{ number_format($totalRevenue, 2) }}</h4>
                <span class="text-sm font-medium">Total Revenue</span>
            </div>
        </div>
    </div>

    <!-- Metric: Out of Stock -->
    <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-danger dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- alert icon -->
                <path d="M22 20L11 0L0 20H22ZM12 17H10V15H12V17ZM12 13H10V8H12V13Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">{{ $outOfStock }}</h4>
                <span class="text-sm font-medium">Instruments Out of Stock</span>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7.5">
    <!-- Booking Trends Chart -->
    <div class="col-span-12 rounded-sm border border-stroke bg-white px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:col-span-8">
        <div class="mb-3 justify-between gap-4 sm:flex">
            <div>
                <h4 class="text-xl font-semibold text-black dark:text-white">
                    Booking Trends
                </h4>
            </div>
        </div>
        <div id="bookingChart" class="-ml-5"></div>
    </div>

    <!-- Inventory Usage Chart -->
    <div class="col-span-12 rounded-sm border border-stroke bg-white px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:col-span-4">
        <div class="mb-3 justify-between gap-4 sm:flex">
            <div>
                <h4 class="text-xl font-semibold text-black dark:text-white">
                    Inventory Usage by Category
                </h4>
            </div>
        </div>
        <div id="inventoryChart" class="mx-auto flex justify-center"></div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include ApexCharts if not bundled -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Booking Chart
    var bookingOptions = {
        series: [{
            name: 'Bookings',
            data: {!! json_encode($bookingChartData) !!}
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        colors: ['#3C50E0', '#10B981', '#EF4444'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                distributed: true
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: {!! json_encode($bookingChartLabels) !!},
        }
    };
    var bookingChart = new ApexCharts(document.querySelector("#bookingChart"), bookingOptions);
    bookingChart.render();

    // Inventory Chart
    var inventoryOptions = {
        series: {!! json_encode($inventoryChartData) !!},
        chart: {
            type: 'donut',
            width: 380,
        },
        labels: {!! json_encode($inventoryChartLabels) !!},
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }]
    };
    var inventoryChart = new ApexCharts(document.querySelector("#inventoryChart"), inventoryOptions);
    inventoryChart.render();
});
</script>
@endpush

@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black dark:text-white">
        Data Reports
    </h2>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6.5">
    <h3 class="text-title-sm font-medium text-black dark:text-white mb-4">Export Capabilities</h3>
    <p class="mb-5 text-gray-500 dark:text-gray-400">
        The reporting data is aggregated and visualized on the main dashboard. 
        Advanced PDF and CSV exports will be available in the upcoming release.
    </p>
    
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90">
            Return to Dashboard
        </a>
    </div>
</div>
@endsection

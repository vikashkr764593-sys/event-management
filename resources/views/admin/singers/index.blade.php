@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Singers Management" />

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Header and Filter -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">Singers</h3>
                    
                    <!-- Genre Filter -->
                    <form action="{{ route('admin.singers.index') }}" method="GET" class="flex items-center">
                        <select name="genre" onchange="this.form.submit()" class="rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            <option value="" class="dark:bg-gray-800">All Genres</option>
                            @foreach($genres as $genreOption)
                                <option value="{{ $genreOption }}" class="dark:bg-gray-800" {{ request('genre') === $genreOption ? 'selected' : '' }}>
                                    {{ $genreOption }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                
                <a href="{{ route('admin.singers.create') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-brand-600 sm:w-auto transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New Singer
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Profile</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Genre</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Experience</th>
                            <th class="px-6 py-4 text-right border-b border-gray-200 dark:border-gray-800">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($singers as $singer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-12 h-12">
                                        <img class="w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ $singer->profile_image_url }}" alt="{{ $singer->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $singer->name }}
                                            @if($singer->stage_name)
                                                <span class="text-gray-500 text-xs ml-1">"{{ $singer->stage_name }}"</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Account: {{ $singer->user->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($singer->genre)
                                    <span class="inline-flex items-center justify-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                        {{ $singer->genre }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $singer->experience_years ? $singer->experience_years . ' Years' : '0 Years' }}
                                <br>
                                <span class="text-xs {{ $singer->availability_status === 'available' ? 'text-success-500' : ($singer->availability_status === 'busy' ? 'text-warning-500' : 'text-gray-400') }} capitalize">
                                    {{ $singer->availability_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                </button>
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-6 mt-2 w-32 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                    <div class="py-1">
                                        <a href="{{ route('admin.singers.edit', $singer->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 text-left">Edit</a>
                                        <form action="{{ route('admin.singers.destroy', $singer->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10 transition" onclick="return confirm('Are you sure you want to delete this singer?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No singers found matching the criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Showing all {{ $singers->count() }} singers.</span>
            </div>
        </div>
    </div>
@endsection
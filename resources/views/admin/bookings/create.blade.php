@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="New Event Booking" />

    <div class="space-y-6">
        @if(session('error'))
            <div class="p-4 text-sm text-error-800 rounded-lg bg-error-50 dark:bg-gray-800 dark:text-error-400" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">Booking Details</h3>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User / Client -->
                        <div>
                            <label for="user_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Client / User</label>
                            <select name="user_id" id="user_id" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="" class="dark:bg-gray-800">Select a Client</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" class="dark:bg-gray-800" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Singer -->
                        <div>
                            <label for="singer_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Singer</label>
                            <select name="singer_id" id="singer_id" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="" class="dark:bg-gray-800">Select a Singer</option>
                                @foreach($singers as $singer)
                                    <option value="{{ $singer->id }}" class="dark:bg-gray-800" {{ old('singer_id') == $singer->id ? 'selected' : '' }}>
                                        {{ $singer->name }} {{ $singer->stage_name ? '('.$singer->stage_name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('singer_id')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Date -->
                        <div>
                            <label for="event_date" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Event Date</label>
                            <input type="date" name="event_date" id="event_date" min="{{ date('Y-m-d') }}" value="{{ old('event_date') }}" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('event_date')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time Slot -->
                        <div>
                            <label for="time_slot" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Time Slot (24-hour format)</label>
                            <input type="time" name="time_slot" id="time_slot" value="{{ old('time_slot') }}" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('time_slot')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div>
                            <label for="event_type" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Event Type (Optional)</label>
                            <input type="text" name="event_type" id="event_type" value="{{ old('event_type') }}" placeholder="e.g. Wedding, Concert, Corporate" 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('event_type')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Booking Status</label>
                            <select name="status" id="status" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="pending" class="dark:bg-gray-800" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" class="dark:bg-gray-800" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" class="dark:bg-gray-800" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="cancelled" class="dark:bg-gray-800" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="notes" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="4" placeholder="Any special requests or instructions..."
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                            Create Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
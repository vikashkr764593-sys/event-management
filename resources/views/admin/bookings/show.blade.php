@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Booking Details: #{{ $booking->id }}" />

<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Bookings
        </a>
        <div class="flex gap-3">
            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Edit Booking
            </a>
            <a href="{{ route('admin.chat.show', ['booking_id' => $booking->id]) }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">
                Open Chat
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <h3 class="text-lg font-semibold mb-6 text-gray-800 dark:text-white">Booking Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Event Type</label>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->event_type ?? 'Standard Performance' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                        <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize 
                            {{ $booking->status === 'approved' ? 'bg-success-50 text-success-600' : ($booking->status === 'pending' ? 'bg-warning-50 text-warning-600' : 'bg-error-50 text-error-600') }}">
                            {{ $booking->status }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date</label>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->event_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Time Slot</label>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->time_slot }}</p>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-800">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Event Notes</label>
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 text-sm text-gray-600 dark:text-gray-400 italic">
                        "{{ $booking->notes ?? 'No special instructions provided.' }}"
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Customer Card -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <h4 class="text-sm font-semibold mb-4 text-gray-800 dark:text-white uppercase tracking-wider">Customer</h4>
                <div class="flex items-center">
                    <img src="{{ $booking->user->profile_picture_url }}" class="w-12 h-12 rounded-full mr-4 object-cover" alt="">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $booking->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->user->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Performer Card -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <h4 class="text-sm font-semibold mb-4 text-gray-800 dark:text-white uppercase tracking-wider">Performer</h4>
                <div class="flex items-center">
                    <img src="{{ asset('storage/' . $booking->singer->profile_image) }}" class="w-12 h-12 rounded-full mr-4 object-cover" alt="">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $booking->singer->name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->singer->genre }} Specialist</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

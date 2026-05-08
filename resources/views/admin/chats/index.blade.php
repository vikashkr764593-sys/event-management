@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Centralized Chat Dashboard" />

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Booking Chats -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Booking Conversations</h3>
        <div class="space-y-4">
            @forelse($bookingChats as $bookingId => $messages)
                @php $booking = $messages->first()->booking; @endphp
                <a href="{{ route('admin.chat.show', ['booking_id' => $bookingId]) }}" class="block p-4 rounded-xl border border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/[0.02] transition">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-brand-600">Booking #{{ $bookingId }}</span>
                        <span class="text-xs text-gray-500">{{ $messages->last()->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 truncate">
                        <strong>{{ $booking->user->name ?? 'User' }}:</strong> {{ $messages->last()->message }}
                    </div>
                </a>
            @empty
                <p class="text-gray-500 text-sm">No active booking chats.</p>
            @endforelse
        </div>
    </div>

    <!-- Order Chats -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Order Conversations</h3>
        <div class="space-y-4">
            @forelse($orderChats as $orderId => $messages)
                @php $order = $messages->first()->order; @endphp
                <a href="{{ route('admin.chat.show', ['order_id' => $orderId]) }}" class="block p-4 rounded-xl border border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/[0.02] transition">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-success-600">Order #{{ $orderId }}</span>
                        <span class="text-xs text-gray-500">{{ $messages->last()->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 truncate">
                        <strong>{{ $order->user->name ?? 'User' }}:</strong> {{ $messages->last()->message }}
                    </div>
                </a>
            @empty
                <p class="text-gray-500 text-sm">No active order chats.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

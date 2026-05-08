@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Chat History: {{ $type }} #{{ $context->id }}" />

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Context Info -->
    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h4 class="font-medium text-gray-800 dark:text-white">Customer: {{ $context->user->name }}</h4>
            <p class="text-sm text-gray-500">{{ $context->user->email }} | {{ $context->user->phone }}</p>
        </div>
        <a href="{{ $type === 'Booking' ? route('admin.bookings.show', $context->id) : route('admin.orders.show', $context->id) }}" class="text-brand-600 hover:underline text-sm font-medium">
            View {{ $type }} Details
        </a>
    </div>

    <!-- Message History -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="p-6 h-[500px] overflow-y-auto space-y-4 bg-gray-50/50 dark:bg-transparent">
            @foreach($chats as $chat)
                <div class="flex {{ $chat->sender->role === 'admin' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%] rounded-2xl p-4 {{ $chat->sender->role === 'admin' ? 'bg-brand-500 text-white rounded-tr-none' : 'bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-tl-none shadow-sm' }}">
                        <div class="text-xs mb-1 opacity-70 flex justify-between gap-4">
                            <span>{{ $chat->sender->name }}</span>
                            <span>{{ $chat->created_at->format('H:i') }}</span>
                        </div>
                        <p class="text-sm leading-relaxed">{{ $chat->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Reply Form -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            <form action="{{ route('admin.chat.reply') }}" method="POST" class="flex gap-4">
                @csrf
                <input type="hidden" name="{{ strtolower($type) }}_id" value="{{ $context->id }}">
                <textarea name="message" rows="1" class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-brand-500 focus:border-brand-500" placeholder="Type your reply here..." required></textarea>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">
                    Send
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

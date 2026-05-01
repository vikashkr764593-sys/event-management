@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black dark:text-white">
        Admin Chat Monitor
    </h2>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">Recent Messages</h3>
    </div>
    
    <div class="flex flex-col gap-5 p-6.5">
        @forelse($chats as $chat)
        <div class="flex items-center gap-5">
            <div class="relative h-14 w-14 rounded-full">
                <!-- Placeholder Avatar -->
                <div class="h-full w-full rounded-full bg-gray-200 flex items-center justify-center text-xl font-bold text-gray-500">
                    {{ strtoupper(substr($chat->sender->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex flex-1 items-center justify-between">
                <div>
                    <h5 class="font-medium text-black dark:text-white">{{ $chat->sender->name }}</h5>
                    <p>
                        <span class="text-sm text-black dark:text-white">{{ $chat->message }}</span>
                        <span class="text-xs"> - Booking #{{ $chat->booking_id }}</span>
                    </p>
                </div>
                <div class="text-xs">{{ $chat->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <p class="text-gray-500 dark:text-gray-400">No chat messages found.</p>
        </div>
        @endforelse
    </div>

    @if($chats->hasPages())
    <div class="border-t border-stroke py-4 px-6.5 dark:border-strokedark">
        {{ $chats->links() }}
    </div>
    @endif
</div>
@endsection

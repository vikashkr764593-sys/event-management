@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black dark:text-white">
        System Notifications
    </h2>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">All Alerts</h3>
    </div>
    
    <div class="flex flex-col gap-5 p-6.5">
        @forelse($notifications as $notification)
        <div class="flex items-center gap-5 p-3 rounded-md {{ $notification->is_read ? '' : 'bg-gray-100 dark:bg-gray-800' }}">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.125 12.825L14.4563 10.5187C14.175 10.125 14.0625 9.61875 14.0625 9.1125V6.75C14.0625 3.9375 11.8125 1.6875 9 1.6875C6.1875 1.6875 3.9375 3.9375 3.9375 6.75V9.1125C3.9375 9.61875 3.825 10.125 3.54375 10.5187L1.875 12.825C1.65 13.1063 1.59375 13.5 1.65 13.8375C1.70625 14.175 1.93125 14.4563 2.25 14.5687C2.56875 14.6812 2.85 14.625 3.075 14.4L3.9375 13.5H14.0625L14.925 14.4C15.0938 14.5688 15.3187 14.625 15.525 14.625C15.6375 14.625 15.75 14.625 15.8625 14.5687C16.1812 14.4562 16.4062 14.175 16.4625 13.8375C16.5187 13.5 16.35 13.1062 16.125 12.825ZM8.99999 16.3125C10.0687 16.3125 10.9687 15.4125 10.9687 14.3438H7.03124C7.03124 15.4125 7.93124 16.3125 8.99999 16.3125Z" fill=""/>
                </svg>
            </div>
            <div class="flex flex-1 items-center justify-between">
                <div>
                    <h5 class="font-medium text-black dark:text-white">{{ $notification->title }}</h5>
                    <p class="text-sm">
                        {{ $notification->message }}
                        @if($notification->user)
                        <span class="text-xs text-gray-500"> - Sent to {{ $notification->user->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-xs">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <p class="text-gray-500 dark:text-gray-400">No system notifications found.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="border-t border-stroke py-4 px-6.5 dark:border-strokedark">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

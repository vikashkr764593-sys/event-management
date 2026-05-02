@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Notifications Management" />

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-brand-600 transition">
                        Mark All as Read
                    </button>
                </form>

                <form action="{{ route('admin.notifications.cleanup') }}" method="POST" onsubmit="return confirm('Delete all notifications older than 30 days?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                        Cleanup Old Alerts
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">All Notifications</h3>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    @forelse($notifications as $notification)
                        <div class="flex items-start gap-4 p-4 rounded-xl border {{ $notification->read() ? 'border-gray-100 bg-white dark:border-gray-800 dark:bg-transparent' : 'border-brand-100 bg-brand-50/30 dark:border-brand-500/20 dark:bg-brand-500/5' }} transition-all">
                            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full {{ $notification->read() ? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' : 'bg-brand-100 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400' }}">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25C10.067 2.25 8.5 3.817 8.5 5.75V11.1441L6.75631 13.9314C6.18341 14.8471 6.8406 16.0526 7.91578 16.0526H16.0842C17.1594 16.0526 17.8166 14.8471 17.2437 13.9314L15.5 11.1441V5.75C15.5 3.817 13.933 2.25 12 2.25ZM10 5.75C10 4.64543 10.8954 3.75 12 3.75C13.1046 3.75 14 4.64543 14 5.75V11.2335C14 11.3697 14.0457 11.5015 14.1299 11.6083L15.9329 13.8967C15.9866 13.9649 15.9381 14.0526 15.8524 14.0526H8.14758C8.0619 14.0526 8.0134 13.9649 8.06707 13.8967L9.8701 11.6083C9.9543 11.5015 10 11.3697 10 11.2335V5.75ZM12 19.5C13.1046 19.5 14 18.6046 14 17.5H10C10 18.6046 10.8954 19.5 12 19.5Z" fill="currentColor" />
                                </svg>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                
                                <div class="mt-3 flex items-center gap-4">
                                    @if($notification->unread())
                                        <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                                Mark as read
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.notifications.unread', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                                Mark as unread
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-error-600 hover:text-error-700 dark:text-error-400 dark:hover:text-error-300">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-white/5 mb-4 text-gray-400">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 8V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 16H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">All caught up!</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No new notifications for you right now.</p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="mt-8 border-t border-gray-100 pt-6 dark:border-gray-800">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

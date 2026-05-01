@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Orders Management" />

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">All Orders</h3>
                
                <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-brand-600 sm:w-auto transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Manual Order
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Order ID & Date</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Customer</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Total Amount</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Order Status</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Payment Status</th>
                            <th class="px-6 py-4 text-right border-b border-gray-200 dark:border-gray-800">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $order->created_at->format('M d, Y h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $order->user->first_name ?? 'Unknown' }} {{ $order->user->last_name ?? '' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $order->user->email ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                                    {{ $order->status === 'issued' ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 
                                       ($order->status === 'approved' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 
                                       'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                                    {{ $order->payment_status === 'Paid' ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 
                                       ($order->payment_status === 'Failed' ? 'bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400' : 
                                       'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400') }}">
                                    {{ $order->payment_status }}
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
                                    class="absolute right-6 mt-2 w-36 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                    <div class="py-1">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 text-left">View Summary</a>
                                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 text-left">Update Status</a>
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10 transition" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No orders found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Showing all {{ $orders->count() }} orders.</span>
            </div>
        </div>
    </div>
@endsection
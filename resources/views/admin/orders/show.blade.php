@extends('layouts.app')
@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            Order Summary #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
        </h2>

        <nav>
            <ol class="flex items-center gap-2">
                <li><a class="font-medium hover:text-brand-500" href="{{ route('admin.dashboard') }}">Dashboard /</a></li>
                <li><a class="font-medium hover:text-brand-500" href="{{ route('admin.orders.index') }}">Orders /</a></li>
                <li class="font-medium text-brand-500">Summary</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Customer Info -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 border-b border-gray-200 dark:border-gray-800 pb-3 mb-4">Customer Details</h3>
                <div class="space-y-3">
                    <div>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Name</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->user->first_name ?? 'Unknown' }} {{ $order->user->last_name ?? '' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Email Address</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->user->email ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Order Placed On</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Status -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 border-b border-gray-200 dark:border-gray-800 pb-3 mb-4">Order Status</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fulfillment Status</span>
                        <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                            {{ $order->status === 'issued' ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 
                                ($order->status === 'approved' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 
                                'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300') }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Status</span>
                        <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                            {{ $order->payment_status === 'Paid' ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 
                                ($order->payment_status === 'Failed' ? 'bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400' : 
                                'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400') }}">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                    @if($order->razorpay_payment_id)
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Razorpay Payment ID</span>
                        <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $order->razorpay_payment_id }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] flex flex-col justify-center">
                <div class="mb-4 text-center">
                    <span class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Total Order Value</span>
                    <span class="text-3xl font-bold text-brand-500">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="w-full inline-flex items-center justify-center rounded-lg bg-gray-100 px-5 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 transition mb-3">
                    Update Status
                </a>
            </div>
        </div>

        <!-- Order Items -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">Purchased Items</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Product</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 text-center">Quantity</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 text-right">Snapshot Price</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @if($item->instrument && $item->instrument->image)
                                        <div class="h-12 w-12 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 flex-shrink-0">
                                            <img src="{{ Storage::url($item->instrument->image) }}" alt="Product Image" class="h-full w-full object-cover">
                                        </div>
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700 flex-shrink-0">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->instrument->name ?? 'Unknown Instrument' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Item ID: {{ $item->instrument_id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-500 dark:text-gray-400">
                                ₹{{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white uppercase">
                                Grand Total
                            </td>
                            <td class="px-6 py-4 text-right text-lg font-bold text-brand-500">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

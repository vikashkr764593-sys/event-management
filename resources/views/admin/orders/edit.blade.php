@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Update Order Status" />

    <div class="space-y-6">
        @if(session('error'))
            <div class="p-4 text-sm text-error-800 rounded-lg bg-error-50 dark:bg-gray-800 dark:text-error-400" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">Order #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h3>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 transition">View Full Summary &rarr;</a>
            </div>

            <div class="p-6">
                <!-- Info Alert -->
                <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-100 dark:bg-blue-900/20 dark:border-blue-800/30">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <strong>Inventory Sync Note:</strong> If you change the Payment Status to <strong>Paid</strong>, the system will automatically decrement the stock for all instruments in this order. This action cannot be easily undone.
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fulfillment Status -->
                        <div>
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Fulfillment Status</label>
                            <select name="status" id="status" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="requested" class="dark:bg-gray-800" {{ old('status', $order->status) == 'requested' ? 'selected' : '' }}>Requested</option>
                                <option value="approved" class="dark:bg-gray-800" {{ old('status', $order->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="issued" class="dark:bg-gray-800" {{ old('status', $order->status) == 'issued' ? 'selected' : '' }}>Issued</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Payment Status</label>
                            <select name="payment_status" id="payment_status" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="Pending" class="dark:bg-gray-800" {{ old('payment_status', $order->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Paid" class="dark:bg-gray-800" {{ old('payment_status', $order->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Failed" class="dark:bg-gray-800" {{ old('payment_status', $order->payment_status) == 'Failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            @error('payment_status')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Razorpay Details (Optional) -->
                        <div>
                            <label for="razorpay_order_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Razorpay Order ID</label>
                            <input type="text" name="razorpay_order_id" id="razorpay_order_id" value="{{ old('razorpay_order_id', $order->razorpay_order_id) }}" 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500"
                                placeholder="e.g. order_...">
                            @error('razorpay_order_id')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="razorpay_payment_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Razorpay Payment ID</label>
                            <input type="text" name="razorpay_payment_id" id="razorpay_payment_id" value="{{ old('razorpay_payment_id', $order->razorpay_payment_id) }}" 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500"
                                placeholder="e.g. pay_...">
                            @error('razorpay_payment_id')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                            Save Changes & Sync Inventory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
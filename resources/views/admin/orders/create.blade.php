@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Create Manual Order" />

    <div class="space-y-6" x-data="orderForm()">
        @if(session('error'))
            <div class="p-4 text-sm text-error-800 rounded-lg bg-error-50 dark:bg-gray-800 dark:text-error-400" role="alert">
                {{ session('error') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="p-4 text-sm text-error-800 rounded-lg bg-error-50 dark:bg-gray-800 dark:text-error-400">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">Order Details</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a new order on behalf of a customer. Prices will be captured automatically.</p>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-200 dark:border-gray-800">
                        <!-- User / Client -->
                        <div>
                            <label for="user_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                            <select name="user_id" id="user_id" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="" class="dark:bg-gray-800">Select Customer</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" class="dark:bg-gray-800" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statuses -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Order Status</label>
                                <select name="status" id="status" required 
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                    <option value="requested" class="dark:bg-gray-800" {{ old('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                                    <option value="approved" class="dark:bg-gray-800" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="issued" class="dark:bg-gray-800" {{ old('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                                </select>
                            </div>
                            <div>
                                <label for="payment_status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Payment Status</label>
                                <select name="payment_status" id="payment_status" required 
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                    <option value="Pending" class="dark:bg-gray-800" {{ old('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Paid" class="dark:bg-gray-800" {{ old('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Failed" class="dark:bg-gray-800" {{ old('payment_status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Dynamic Field -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-md font-medium text-gray-800 dark:text-white/90">Order Items</h4>
                            <button type="button" @click="addItem()" class="text-sm font-medium text-brand-500 hover:text-brand-600 transition">
                                + Add Another Item
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                    <div class="flex-1">
                                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Select Instrument</label>
                                        <select :name="`instrument_id[]`" required x-model="item.id" @change="updatePrice(index)"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-brand-500">
                                            <option value="" class="dark:bg-gray-800">Choose...</option>
                                            @foreach($instruments as $instrument)
                                                <option value="{{ $instrument->id }}" data-price="{{ $instrument->price }}" class="dark:bg-gray-800">
                                                    {{ $instrument->name }} (₹{{ number_format($instrument->price, 2) }}) - Stock: {{ $instrument->stock }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-24">
                                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                        <input type="number" :name="`quantity[]`" required min="1" x-model="item.qty"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-brand-500">
                                    </div>
                                    <div class="w-10 flex justify-end pt-5">
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-error-500 hover:text-error-600 transition p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                            Create Order & Snapshot Prices
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js script to manage dynamic rows -->
    <script>
        function orderForm() {
            return {
                items: [
                    { id: '', qty: 1 }
                ],
                addItem() {
                    this.items.push({ id: '', qty: 1 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                updatePrice(index) {
                    // Logic to show price if needed dynamically could go here
                }
            }
        }
    </script>
@endsection
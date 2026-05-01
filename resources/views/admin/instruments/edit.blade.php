@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Instrument" />

    <div class="space-y-6">
        @if(session('error'))
            <div class="p-4 text-sm text-error-800 rounded-lg bg-error-50 dark:bg-gray-800 dark:text-error-400" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">Update Instrument Details</h3>
                @if($instrument->image)
                    <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <img src="{{ $instrument->image_url }}" alt="{{ $instrument->name }}" class="w-full h-full object-cover">
                    </div>
                @endif
            </div>

            <div class="p-6">
                <form action="{{ route('admin.instruments.update', $instrument->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Instrument Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $instrument->name) }}" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('name')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <select name="category_id" id="category_id" 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="" class="dark:bg-gray-800">Select a Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" class="dark:bg-gray-800" {{ old('category_id', $instrument->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Price (₹)</label>
                            <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price', $instrument->price) }}" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('price')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="stock" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Stock Quantity</label>
                            <input type="number" min="0" step="1" name="stock" id="stock" value="{{ old('stock', $instrument->stock) }}" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('stock')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="image" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Update Instrument Image</label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-gray-700 dark:file:text-white transition">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to keep the current image. PNG, JPG, or WEBP up to 2MB.</p>
                            @error('image')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ route('admin.instruments.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                            Update Instrument
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
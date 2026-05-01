@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Singer Profile" />

    <div class="space-y-6">
        @if(session('error'))
            <div class="p-4 text-sm text-error-800 rounded-lg bg-error-50 dark:bg-gray-800 dark:text-error-400" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">Edit Profile</h3>
                @if($singer->profile_image)
                    <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-200 dark:border-gray-700">
                        <img src="{{ $singer->profile_image_url }}" alt="Profile" class="w-full h-full object-cover">
                    </div>
                @endif
            </div>

            <div class="p-6">
                <form action="{{ route('admin.singers.update', $singer->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Linked User Account -->
                        <div>
                            <label for="user_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Linked User Account</label>
                            <select name="user_id" id="user_id" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" class="dark:bg-gray-800" {{ old('user_id', $singer->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Real Name -->
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Real Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $singer->name) }}" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('name')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stage Name -->
                        <div>
                            <label for="stage_name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Stage Name (Optional)</label>
                            <input type="text" name="stage_name" id="stage_name" value="{{ old('stage_name', $singer->stage_name) }}" 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('stage_name')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Genre -->
                        <div>
                            <label for="genre" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Primary Genre</label>
                            <input type="text" name="genre" id="genre" value="{{ old('genre', $singer->genre) }}" placeholder="e.g. Pop, Jazz, Rock"
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('genre')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Experience Years -->
                        <div>
                            <label for="experience_years" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Experience (Years)</label>
                            <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years', $singer->experience_years ?? 0) }}" 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('experience_years')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Availability Status -->
                        <div>
                            <label for="availability_status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Availability Status</label>
                            <select name="availability_status" id="availability_status" required 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                                <option value="available" class="dark:bg-gray-800" {{ old('availability_status', $singer->availability_status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="busy" class="dark:bg-gray-800" {{ old('availability_status', $singer->availability_status) === 'busy' ? 'selected' : '' }}>Busy</option>
                                <option value="unavailable" class="dark:bg-gray-800" {{ old('availability_status', $singer->availability_status) === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                            @error('availability_status')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rating -->
                        <div>
                            <label for="rating" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Rating (0.0 to 5.0)</label>
                            <input type="number" step="0.1" min="0" max="5" name="rating" id="rating" value="{{ old('rating', $singer->rating) }}" 
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">
                            @error('rating')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Profile Image -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="profile_image" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Upload New Profile Image</label>
                            <input type="file" name="profile_image" id="profile_image" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-gray-700 dark:file:text-white transition">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to keep the current image. PNG, JPG, or WEBP up to 2MB.</p>
                            @error('profile_image')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Biography -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="biography" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Biography / About</label>
                            <textarea name="biography" id="biography" rows="4"
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white dark:focus:border-brand-500">{{ old('biography', $singer->biography) }}</textarea>
                            @error('biography')
                                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ route('admin.singers.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                            Update Singer Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
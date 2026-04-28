@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Bookings" />
    <div class="space-y-6">
        <x-common.component-card title="Edit Bookings">
            @php $var = substr('bookings', 0, -1); if ('bookings' == 'users') $var = 'user'; @endphp
            <form action="{{ route('admin.bookings.update', $$var->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block mb-1">Name / Detail</label>
                    <input type="text" name="name" value="{{ $$var->name ?? '' }}" class="w-full border px-3 py-2 rounded">
                </div>
                <!-- Additional fields can be added here -->
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.bookings.index') }}" class="ml-2 text-gray-600">Cancel</a>
            </form>
        </x-common.component-card>
    </div>
@endsection
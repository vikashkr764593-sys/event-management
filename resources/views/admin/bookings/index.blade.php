@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Bookings Management" />
    <div class="space-y-6">
        <x-common.component-card title="Bookings">
            <div class="mb-4">
                <a href="{{ route('admin.bookings.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Add New</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Details</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $item)
                        <tr class="border-b">
                            <td class="px-4 py-2 border">{{ $item->id }}</td>
                            <td class="px-4 py-2 border">{{ $item->name ?? $item->status ?? 'Detail' }}</td>
                            <td class="px-4 py-2 border">
                                <a href="{{ route('admin.bookings.edit', $item->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                <form action="{{ route('admin.bookings.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>
@endsection
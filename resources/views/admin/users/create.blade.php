@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Create Users" />
    <div class="space-y-6">
        <x-common.component-card title="New Users">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1">Name / Detail</label>
                    <input type="text" name="name" class="w-full border px-3 py-2 rounded">
                </div>
                <!-- Additional fields can be added here -->
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600">Cancel</a>
            </form>
        </x-common.component-card>
    </div>
@endsection
<?php

$resources = ['users', 'singers', 'instruments', 'bookings', 'orders'];

foreach ($resources as $res) {
    $dir = __DIR__ . "/resources/views/admin/{$res}/";
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $title = ucfirst($res);

    // INDEX VIEW
    $index = <<<EOD
@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="{$title} Management" />
    <div class="space-y-6">
        <x-common.component-card title="{$title}">
            <div class="mb-4">
                <a href="{{ route('admin.{$res}.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Add New</a>
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
                        @foreach(\${$res} as \$item)
                        <tr class="border-b">
                            <td class="px-4 py-2 border">{{ \$item->id }}</td>
                            <td class="px-4 py-2 border">{{ \$item->name ?? \$item->status ?? 'Detail' }}</td>
                            <td class="px-4 py-2 border">
                                <a href="{{ route('admin.{$res}.edit', \$item->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                <form action="{{ route('admin.{$res}.destroy', \$item->id) }}" method="POST" class="inline">
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
EOD;

    file_put_contents($dir . 'index.blade.php', $index);

    // CREATE VIEW
    $create = <<<EOD
@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Create {$title}" />
    <div class="space-y-6">
        <x-common.component-card title="New {$title}">
            <form action="{{ route('admin.{$res}.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1">Name / Detail</label>
                    <input type="text" name="name" class="w-full border px-3 py-2 rounded">
                </div>
                <!-- Additional fields can be added here -->
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                <a href="{{ route('admin.{$res}.index') }}" class="ml-2 text-gray-600">Cancel</a>
            </form>
        </x-common.component-card>
    </div>
@endsection
EOD;

    file_put_contents($dir . 'create.blade.php', $create);

    // EDIT VIEW
    $edit = <<<EOD
@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb pageTitle="Edit {$title}" />
    <div class="space-y-6">
        <x-common.component-card title="Edit {$title}">
            @php \$var = substr('{$res}', 0, -1); if ('{$res}' == 'users') \$var = 'user'; @endphp
            <form action="{{ route('admin.{$res}.update', \$\$var->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block mb-1">Name / Detail</label>
                    <input type="text" name="name" value="{{ \$\$var->name ?? '' }}" class="w-full border px-3 py-2 rounded">
                </div>
                <!-- Additional fields can be added here -->
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.{$res}.index') }}" class="ml-2 text-gray-600">Cancel</a>
            </form>
        </x-common.component-card>
    </div>
@endsection
EOD;

    file_put_contents($dir . 'edit.blade.php', $edit);
}

echo "Admin views scaffolded.\n";

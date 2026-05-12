@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb pageTitle="Users Management" />

<div class="space-y-6" x-data="userListingComponent()">
    @if(session('success'))
    <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <!-- Tab Navigation -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex gap-8 border-b border-gray-200 dark:border-gray-800 -mb-px">
                <button @click="activeTab = 'active'; loadUsers()" :class="activeTab === 'active' ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300'" class="px-1 py-3 font-medium text-sm transition whitespace-nowrap">
                    Active Users (<span x-text="activeUsers.length">0</span>)
                </button>
                <button @click="activeTab = 'pending'; loadPendingUsers()" :class="activeTab === 'pending' ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300'" class="px-1 py-3 font-medium text-sm transition whitespace-nowrap">
                    Pending Users (<span x-text="pendingUsers.length">0</span>)
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90" x-text="activeTab === 'active' ? 'Active Users' : 'Pending Users'"></h3>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-brand-600 sm:w-auto transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New User
            </a>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="px-6 py-8 text-center">
            <div class="inline-block">
                <svg class="w-8 h-8 text-brand-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

        <!-- Active Users Table -->
        <template x-if="activeTab === 'active'">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Name</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Mobile Number</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Role</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Subscription</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Status</th>
                            <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Last Login</th>
                            <th class="px-6 py-4 text-right border-b border-gray-200 dark:border-gray-800">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <template x-for="user in activeUsers" :key="user.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <img class="w-10 h-10 rounded-full object-cover" :src="user.profile_picture_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name)" :alt="user.name">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="user.name"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="user.email"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white" x-text="user.phone || 'Not provided'"></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 capitalize" x-text="user.role || 'User'"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <template x-if="user.role === 'admin'">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">NA</span>
                                    </template>
                                    <template x-if="user.role !== 'admin'">
                                        <template x-if="user.status === 'active' || !user.status">
                                            <span class="inline-flex items-center text-xs font-medium text-success-600 dark:text-success-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-success-600 mr-1.5"></span>
                                                Subscribed
                                            </span>
                                        </template>
                                        <template x-if="user.status !== 'active' && user.status">
                                            <span class="inline-flex items-center text-xs font-medium text-error-600 dark:text-error-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-error-600 mr-1.5"></span>
                                                Unsubscribed
                                            </span>
                                        </template>
                                    </template>
                                </td>
                                <td class="px-6 py-4">
                                    <template x-if="user.status === 'active' || !user.status">
                                        <span class="inline-flex items-center justify-center rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/10 dark:text-success-400">
                                            Active
                                        </span>
                                    </template>
                                    <template x-if="user.status !== 'active' && user.status">
                                        <span class="inline-flex items-center justify-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            Inactive
                                        </span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" x-text="user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never logged in'"></td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a :href="'/admin/users/' + user.id + '/edit'" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <template x-if="activeUsers.length === 0 && !loading">
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No active users found.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>

 <!-- Pending Users Table -->
<template x-if="activeTab === 'pending'">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">

            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                    <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Name</th>
                    <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Email</th>
                    <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Phone</th>
                    <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Order ID</th>
                    <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Status</th>
                    <th class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">Registered At</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">

                <!-- User Listing -->
                <template x-for="user in pendingUsers" :key="user.id">
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">

                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="user.name"></div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-300" x-text="user.email"></div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-300" x-text="user.phone ? user.phone : '—'"></div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-300 font-mono" x-text="user.razorpay_order_id"></div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-600 dark:bg-amber-500/10 dark:text-amber-400"
                                x-text="user.status">
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap"
                            x-text="new Date(user.created_at).toLocaleDateString()">
                        </td>

                    </tr>
                </template>

                <!-- Empty State -->
                <template x-if="pendingUsers.length === 0 && !loading">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No pending users at the moment.
                        </td>
                    </tr>
                </template>

            </tbody>

        </table>
    </div>
</template>

        <!-- Summary -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
            <span class="text-sm text-gray-500 dark:text-gray-400" x-show="activeTab === 'active'">
                Showing <span x-text="activeUsers.length">0</span> active users.
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400" x-show="activeTab === 'pending'">
                Showing <span x-text="pendingUsers.length">0</span> pending users.
            </span>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    function userListingComponent() {
        return {
            activeTab: 'active',
            activeUsers: @json($users ?? []),
            pendingUsers: [],
            loading: false,

            init() {
                this.loadUsers();
            },

            loadUsers() {
                this.loading = false;
            },

            loadPendingUsers() {

                this.loading = true;

                fetch('/api/get-pending-users')
                    .then(response => response.json())
                    .then(data => {

                        console.log(data);

                        if (data.status) {
                            this.pendingUsers = data.data;
                        }

                        this.loading = false;
                    })
                    .catch(error => {

                        console.error('Error loading pending users:', error);

                        this.loading = false;
                    });
            }
        };
    }
</script>
@endpush

@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Support" />
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Help & Support</h3>
        
        <div class="space-y-4 text-gray-600 dark:text-gray-400">
            <p>If you need help or have any questions about using the admin dashboard, please contact the system administrator.</p>
            
            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-800 dark:text-white mb-2">Contact Information</h4>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Email: support@example.com</li>
                    <li>Phone: +1 (555) 123-4567</li>
                    <li>Hours: Monday - Friday, 9:00 AM - 5:00 PM</li>
                </ul>
            </div>
            
            <div class="mt-8">
                <h4 class="text-md font-medium text-gray-800 dark:text-white mb-2">Documentation</h4>
                <p>For detailed guides on how to manage events, instruments, and users, please refer to the <a href="#" class="text-blue-600 dark:text-blue-500 hover:underline">User Manual</a>.</p>
            </div>
        </div>
    </div>
@endsection

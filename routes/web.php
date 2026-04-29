<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSingerController;
use App\Http\Controllers\Admin\AdminInstrumentController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminOrderController;

// Redirect root to admin dashboard (or login if unauthenticated)
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Authentication Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Panel Routes (Protected by Auth)
// Assuming we will create a middleware 'admin' later, using standard 'auth' for now
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Chat & Notifications Views
    Route::get('/chat', [DashboardController::class, 'chat'])->name('chat');
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    // CRUD Management (Resources)
    Route::resource('users', AdminUserController::class);
    Route::resource('singers', AdminSingerController::class);
    Route::resource('instruments', AdminInstrumentController::class);
    Route::resource('bookings', AdminBookingController::class);
    Route::resource('orders', AdminOrderController::class);
});

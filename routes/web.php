<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSingerController;
use App\Http\Controllers\Admin\AdminInstrumentController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminChatController;

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

    // Chat & Messaging
    Route::get('/chat', [AdminChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [AdminChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{id}', [AdminChatController::class, 'store'])->name('chat.store');
    Route::post('/chat/start', [AdminChatController::class, 'startConversation'])->name('chat.start');
    
    // Notifications Management
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/{id}/unread', [AdminNotificationController::class, 'markAsUnread'])->name('notifications.unread');
    Route::post('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/cleanup', [AdminNotificationController::class, 'cleanup'])->name('notifications.cleanup');
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    // Profile & Account Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::patch('/settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password');
    Route::get('/support', [ProfileController::class, 'support'])->name('support');

    // CRUD Management (Resources)
    Route::resource('users', AdminUserController::class);
    Route::resource('singers', AdminSingerController::class);
    Route::resource('instruments', AdminInstrumentController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('bookings', AdminBookingController::class);
    Route::resource('orders', AdminOrderController::class);
});

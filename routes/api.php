<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\SingerController;
use App\Http\Controllers\Api\InstrumentController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ApprovalController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/register/initiate', [RegistrationController::class, 'preRegister']);
Route::post('/auth/register/webhook', [RegistrationController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // User Territory: Personal Management

    // Singers (Read-only for General Users, Management for Performers)
    Route::apiResource('singers', SingerController::class)->only(['index', 'show']);
    Route::get('/singers/{id}/availability', [SingerController::class, 'getAvailability']);
    // Route::post('/singers/{id}/availability', [SingerController::class, 'storeAvailability']);
    // Route::put('/singers/{id}/availability', [SingerController::class, 'updateAvailability']);

    // Instruments
    Route::apiResource('instruments', InstrumentController::class)->only(['index', 'show']);

    // Bookings
    Route::get('/bookings/my', [BookingController::class, 'myBookings']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Orders
    Route::get('/orders/my', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/retry-payment', [OrderController::class, 'retryPayment']);
    Route::post('/orders/verify-payment', [OrderController::class, 'verifyPayment']);

    // Chat
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::get('/chat/booking/{booking_id}', [ChatController::class, 'getByBooking']);
    Route::get('/chat/order/{order_id}', [ChatController::class, 'getByOrder']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // File Upload (Personal)
    Route::post('/upload', [UploadController::class, 'upload']);
    Route::delete('/upload', [UploadController::class, 'destroy']);
});

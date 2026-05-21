<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerLogController;
use App\Http\Controllers\Api\ServiceController as ApiServiceController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\BookingController as ApiBookingController;
use App\Http\Controllers\Api\AdminBookingController as ApiAdminBookingController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\CustomerAuthController as ApiCustomerAuthController;

// =========================================================================
// ADMIN AUTH (email + password for admin users in the Laravel panel)
// =========================================================================
Route::post('/login', [ApiAuthController::class, 'login']);

// =========================================================================
// CUSTOMER AUTH (phone + password for customers using the Flutter app)
// =========================================================================
Route::prefix('customer')->group(function () {
    Route::post('/register', [ApiCustomerAuthController::class, 'register']);
    Route::post('/login',    [ApiCustomerAuthController::class, 'login']);
});

// =========================================================================
// PROTECTED ADMIN ROUTES (Sanctum — User model tokens)
// =========================================================================
Route::middleware('auth:sanctum')->group(function () {
    // Get the authenticated admin user
    Route::get('/user',    [ApiAuthController::class, 'user']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // Admin routes for managing all bookings
    Route::prefix('admin')->group(function () {
        Route::get('/bookings',           [ApiAdminBookingController::class, 'index']);
        Route::get('/bookings/{booking}', [ApiAdminBookingController::class, 'show']);
        Route::put('/bookings/{booking}', [ApiAdminBookingController::class, 'update']);
        Route::get('/admins',             [ApiAuthController::class, 'admins']);
    });
});

// =========================================================================
// PROTECTED CUSTOMER ROUTES (Sanctum — Customer model tokens)
// =========================================================================
Route::middleware('auth:sanctum')->prefix('customer')->group(function () {
    Route::get('/me',             [ApiCustomerAuthController::class, 'me']);
    Route::post('/logout',        [ApiCustomerAuthController::class, 'logout']);
    Route::put('/profile',        [ApiCustomerAuthController::class, 'updateProfile']);
});

// Customer bookings — all require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings',            [ApiBookingController::class, 'index']);
    Route::post('/bookings',           [ApiBookingController::class, 'store']);
    Route::get('/bookings/{booking}',  [ApiBookingController::class, 'show']);
    Route::put('/bookings/{booking}',  [ApiBookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [ApiBookingController::class, 'destroy']);
});

// =========================================================================
// PUBLIC ROUTES (no auth required)
// =========================================================================
Route::apiResource('v1/customer-logs', CustomerLogController::class)->only(['index', 'show', 'store']);

Route::get('/services', [ApiServiceController::class, 'index']);
Route::apiResource('employees', \App\Http\Controllers\Api\EmployeeController::class)->only(['index', 'show']);

Route::get('/products', [ApiProductController::class, 'index']);
Route::post('/products/purchase', [ApiProductController::class, 'purchase']);

Route::get('/events',          [\App\Http\Controllers\Api\EventController::class, 'index']);
Route::get('/events/{event}',  [\App\Http\Controllers\Api\EventController::class, 'show']);

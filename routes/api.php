<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerLogController;
use App\Http\Controllers\Api\ServiceController as ApiServiceController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\BookingController as ApiBookingController;
use App\Http\Controllers\Api\AdminBookingController as ApiAdminBookingController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;

// --- AUTHENTICATION ROUTES (Public) ---
Route::post('/login', [ApiAuthController::class, 'login']);

// All API routes will be protected by Sanctum's token authentication
Route::middleware('auth:sanctum')->group(function () {
    // Get the authenticated user
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // API resource routes for customer logs
   

    // --- ADMIN API ROUTES ---
    // Admin routes for managing all bookings
    Route::prefix('admin')->group(function () {
        Route::get('/bookings', [ApiAdminBookingController::class, 'index']);
        Route::get('/bookings/{booking}', [ApiAdminBookingController::class, 'show']);
        Route::put('/bookings/{booking}', [ApiAdminBookingController::class, 'update']);
        // Get list of all admin users
        Route::get('/admins', [ApiAuthController::class, 'admins']);
    });
});

Route::apiResource('v1/customer-logs', CustomerLogController::class)->only(['index', 'show', 'store']);

// --- NEW PUBLIC API ROUTES ---
Route::get('/services', [ApiServiceController::class, 'index']);

// Get a list of all available products
Route::get('/products', [ApiProductController::class, 'index']);

// Purchase a product (decreases stock quantity)
Route::post('/products/purchase', [ApiProductController::class, 'purchase']);

// Allow a user to create a new booking
Route::post('/bookings', [ApiBookingController::class, 'store']);

// Allow a user to view their bookings (requires phone parameter)
Route::get('/bookings', [ApiBookingController::class, 'index']);

// Allow a user to view a specific booking (requires phone parameter)
Route::get('/bookings/{booking}', [ApiBookingController::class, 'show']);

// Allow a user to update their booking (e.g., change the time) - requires phone parameter
Route::put('/bookings/{booking}', [ApiBookingController::class, 'update']);

// Allow a user to cancel their booking - requires phone parameter
Route::delete('/bookings/{booking}', [ApiBookingController::class, 'destroy']);

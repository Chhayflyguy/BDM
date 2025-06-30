<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerLogController;

// All API routes will be protected by Sanctum's token authentication
Route::middleware('auth:sanctum')->group(function () {
    // Get the authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API resource routes for customer logs
    Route::apiResource('v1/customer-logs', CustomerLogController::class)->only(['index', 'show', 'store']);
});

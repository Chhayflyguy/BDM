<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerLogController; 
use App\Http\Controllers\CustomerController; // NEW: Import CustomerController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyExpenseController; // NEW
use App\Http\Controllers\AccountantController; // NEW
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController; // NEW

// ...

Route::get('/dashboard', [CustomerLogController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Completion workflow routes
    Route::post('/customers/{customer}/top-up', [CustomerController::class, 'topUpVipBalance'])->name('customers.top-up'); // NEW
    Route::get('/customer_logs/{customerLog}/complete', [CustomerLogController::class, 'showCompletionForm'])->name('customer_logs.complete.form');
    Route::post('/customer_logs/{customerLog}/complete', [CustomerLogController::class, 'markAsComplete'])->name('customer_logs.complete.submit');
    Route::post('/customer_logs/export', [CustomerLogController::class, 'export'])->name('customer_logs.export');
    
    Route::resource('daily_expenses', DailyExpenseController::class)->except(['show']);
    Route::get('/accountant', [AccountantController::class, 'index'])->name('accountant.index');
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index'); // NEW
    Route::resource('employees', EmployeeController::class)->except(['show']); // MODIFIED
    // Resource routes
    Route::resource('customer_logs', CustomerLogController::class); // MODIFIED: Enable all log routes
    Route::resource('customers', CustomerController::class)->except(['destroy']); // MODIFIED: Enable all customer routes except destroy
});

require __DIR__.'/auth.php';
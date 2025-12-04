<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerLogController;
use App\Http\Controllers\CustomerController; // NEW: Import CustomerController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyExpenseController; // NEW
use App\Http\Controllers\AccountantController; // NEW
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController; // NEW
use App\Http\Controllers\Auth\SecurityQuestionResetController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
// ...
Route::get('language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/dashboard', [CustomerLogController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/security-questions', [ProfileController::class, 'storeSecurityQuestions'])->name('profile.security-questions.store')->middleware('password.confirm');
    // Completion workflow routes
    Route::post('/customers/{customer}/top-up', [CustomerController::class, 'topUpVipBalance'])->name('customers.top-up'); // NEW
    Route::get('/customer_logs/{customerLog}/complete', [CustomerLogController::class, 'showCompletionForm'])->name('customer_logs.complete.form');
    Route::post('/customer_logs/{customerLog}/complete', [CustomerLogController::class, 'markAsComplete'])->name('customer_logs.complete.submit');
    Route::post('/customer_logs/export', [CustomerLogController::class, 'export'])->name('customer_logs.export');

    Route::resource('daily_expenses', DailyExpenseController::class)->except(['show']);
    Route::get('/accountant', [AccountantController::class, 'index'])->name('accountant.index');
    
    // Payroll Routes
Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
Route::get('/payroll/employee/{employee}', [PayrollController::class, 'show'])->name('payroll.show');
Route::patch('/payroll/customer-log/{customerLog}/commission', [PayrollController::class, 'updateCommission'])->name('payroll.update-commission');
Route::post('/payroll/export', [PayrollController::class, 'export'])->name('payroll.export'); // MODIFIED
    // Resource routes
    Route::resource('employees', EmployeeController::class)->except(['show']); // MODIFIED
    Route::resource('customer_logs', CustomerLogController::class); // MODIFIED: Enable all log routes
    Route::resource('customers', CustomerController::class)->except(['destroy']);

    // Security Question Reset Routes
    // Route::get('/forgot-password', [SecurityQuestionResetController::class, 'showEmailForm'])->middleware('guest')->name('password.request');
    // Route::post('/forgot-password', [SecurityQuestionResetController::class, 'handleEmailForm'])->middleware('guest')->name('password.email');
    // Route::get('/reset-password/questions', [SecurityQuestionResetController::class, 'showQuestionForm'])->middleware('guest')->name('password.questions');
    // Route::post('/reset-password/questions', [SecurityQuestionResetController::class, 'verifyQuestions'])->middleware('guest')->name('password.questions.verify');
    // Route::get('/reset-password/new', [SecurityQuestionResetController::class, 'showNewPasswordForm'])->middleware('guest')->name('password.reset');
    // Route::post('/reset-password/new', [SecurityQuestionResetController::class, 'updatePassword'])->middleware('guest')->name('password.update');

    // Export Routes
    Route::post('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('/daily_expenses/export', [DailyExpenseController::class, 'export'])->name('daily_expenses.export');
    Route::post('/payroll/export', [PayrollController::class, 'export'])->name('payroll.export');
    Route::post('/accountant/export', [AccountantController::class, 'export'])->name('accountant.export');
    
    //admin route 
    // Change it to this
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::get('/products/{product}/stock', [AdminProductController::class, 'showStockForm'])->name('products.stock');
    Route::post('/products/{product}/stock', [AdminProductController::class, 'addStock'])->name('products.add-stock');
    Route::resource('services', AdminServiceController::class);
    Route::resource('bookings', AdminBookingController::class)->only(['index', 'update', 'destroy']);
});
});

require __DIR__ . '/auth.php';

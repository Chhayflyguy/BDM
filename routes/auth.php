<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\SecurityQuestionResetController; // Import our new controller
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // --- THIS IS THE CORRECTED PASSWORD RESET FLOW ---
    Route::get('forgot-password', [SecurityQuestionResetController::class, 'showEmailForm'])
        ->name('password.request');

    Route::post('forgot-password', [SecurityQuestionResetController::class, 'handleEmailForm'])
        ->name('password.email');

    Route::get('reset-password/questions', [SecurityQuestionResetController::class, 'showQuestionForm'])
        ->name('password.questions');

    Route::post('reset-password/questions', [SecurityQuestionResetController::class, 'verifyQuestions'])
        ->name('password.questions.verify');

    Route::get('reset-password', [SecurityQuestionResetController::class, 'showNewPasswordForm'])
        ->name('password.reset');

    Route::post('reset-password', [SecurityQuestionResetController::class, 'updatePassword'])
        ->name('password.update');
    // --- END OF CORRECTED FLOW ---
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update']); // Note: password.update is now used for changing password in profile

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
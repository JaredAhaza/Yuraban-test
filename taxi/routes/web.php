<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PhoneVerificationNotificationController;
use App\Http\Controllers\Auth\PhoneVerificationPromptController;
use App\Http\Controllers\Auth\VerifyPhoneController;
use App\Http\Controllers\Admin\DriverApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\DriverDashboardController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Driver dashboard route
    Route::get('/driver/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');

    // Driver routes

});

Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware(['auth'])
    ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware(['auth']);

Route::post('/phone/verification-notification', [PhoneVerificationNotificationController::class, 'store'])
    ->middleware(['auth']);

Route::get('/verify-phone', PhoneVerificationPromptController::class)
    ->middleware(['auth'])
    ->name('verification.phone');

Route::post('/verify-phone', VerifyPhoneController::class)
    ->middleware(['auth']);

Route::post('/verification/send', [PhoneVerificationNotificationController::class, 'send'])
    ->middleware(['auth'])
    ->name('verification.send');

// Admin routes for driver approval
Route::middleware(['admin'])->group(function () {
    Route::get('admin/drivers', [DriverApprovalController::class, 'index'])->name('admin.drivers.index');
    Route::post('admin/drivers/{id}/approve', [DriverApprovalController::class, 'approve'])->name('admin.drivers.approve');
    Route::post('admin/drivers/{id}/decline', [DriverApprovalController::class, 'decline'])->name('admin.drivers.decline');
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
});

Route::get('/waiting-approval', function () {
    return view('auth.waiting-approval');
})->name('waiting.approval');

Route::get('/declined', function () {
    return view('auth.declined');
})->name('declined');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.phone');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
        // Waiting approval route
    Route::get('/waiting-approval', function () {
        return view('auth.waiting-approval');
    })->name('waiting.approval');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Ride routes
    Route::get('/book-ride', [RideController::class, 'create'])
        ->name('ride.create');
    Route::post('/book-ride', [RideController::class, 'store'])
        ->name('ride.store');

    // Customer routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Ride requests
    Route::get('/ride-requests', [RideController::class, 'requests'])->name('ride.requests');
});

require __DIR__.'/auth.php';

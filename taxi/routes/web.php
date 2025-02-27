<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PhoneVerificationNotificationController;
use App\Http\Controllers\Auth\PhoneVerificationPromptController;
use App\Http\Controllers\Auth\VerifyPhoneController;
use App\Http\Controllers\Admin\DriverApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\AdminRideController;
use App\Http\Controllers\Customer\CustomerRideController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Driver\DriverRideController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard Route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
});

// Password Confirmation Routes
Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware(['auth'])
    ->name('password.confirm');
Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware(['auth']);

// Phone Verification Routes
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

// Admin Routes (Driver Approval)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/drivers', [DriverApprovalController::class, 'index'])->name('drivers.index');
    Route::post('/drivers/{id}/approve', [DriverApprovalController::class, 'approve'])->name('drivers.approve');
    Route::post('/drivers/{id}/decline', [DriverApprovalController::class, 'decline'])->name('drivers.decline');
});

// Driver Management
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
});

// Waiting Approval & Declined Pages
Route::get('/waiting-approval', function () {
    return view('auth.waiting-approval');
})->name('waiting.approval');

Route::get('/declined', function () {
    return view('auth.declined');
})->name('declined');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.phone');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Admin Ride Management
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/rides', [AdminRideController::class, 'index'])->name('rides.index');
    Route::get('/rides/{ride}', [AdminRideController::class, 'show'])->name('rides.show');
    Route::post('/rides/{ride}/assign', [AdminRideController::class, 'assign'])->name('rides.assign');
    Route::post('/rides/{ride}/cancel', [AdminRideController::class, 'cancel'])->name('rides.cancel');
    Route::get('/ride/requests', [AdminRideController::class, 'rideRequests'])->name('ride.requests');
});

// Customer Ride Management
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/rides', [CustomerRideController::class, 'index'])->name('rides.index');
    Route::get('/rides/create', [CustomerRideController::class, 'create'])->name('rides.create');
    Route::post('/rides', [CustomerRideController::class, 'store'])->name('rides.store');
    Route::get('/rides/{ride}', [CustomerRideController::class, 'show'])->name('rides.show');
    Route::post('/rides/{ride}/cancel', [CustomerRideController::class, 'cancel'])->name('rides.cancel');
    
});

// Driver Ride Management
Route::middleware(['auth', 'driver', 'driver.approved'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/rides', [DriverRideController::class, 'index'])->name('rides.index');
    Route::post('/rides/{ride}/accept', [DriverRideController::class, 'accept'])->name('rides.accept');
    Route::post('/rides/{ride}/start', [DriverRideController::class, 'start'])->name('rides.start');
    Route::post('/rides/{ride}/complete', [DriverRideController::class, 'complete'])->name('rides.complete');
    Route::post('/rides/{ride}/cancel', [DriverRideController::class, 'cancel'])->name('rides.cancel');
});


// Ping Route
Route::prefix('api')->group(function () {
    Route::get('/ping', function () {
        return response()->json(['message' => 'pong']);
    });
    Route::post('register', [RegisteredUserController::class, 'storeApi'])
    ->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
});

require __DIR__.'/auth.php';

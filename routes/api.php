<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->middleware('guest');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->middleware('guest');


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('http://localhost:3000/login?verified=true');
})->middleware(['auth', 'signed'])->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification email resent.']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


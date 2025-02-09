<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ProfileController;
use Illuminate\Support\Facades\Route;

// Frontend
Route::get('register', [RegisteredUserController::class, 'register'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::get('login', [AuthenticatedSessionController::class, 'login'])->name('login');
Route::get('forgot-password', [PasswordResetLinkController::class, 'passwordRequest'])->name('password.request');
Route::get('reset-password/{token}', [NewPasswordController::class, 'passwordReset'])->name('password.reset');
Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice')->middleware('auth');
Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->name('verification.verify')->middleware(['auth', 'signed', 'throttle:6,1']);
Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send')->middleware(['auth', 'throttle:6,1']);

// Socialite
Route::get('/auth/{provider}/redirect', [AuthenticatedSessionController::class, 'redirectToProvider'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [AuthenticatedSessionController::class, 'handleProviderCallback'])->name('socialite.callback');

// Backend
Route::get('backend/login', [AuthenticatedSessionController::class, 'backendLogin'])->name('backend.login');
Route::get('backend-forgot-password', [PasswordResetLinkController::class, 'backendPasswordRequest'])->name('backend.password.request');
Route::get('backend-reset-password/{token}', [NewPasswordController::class, 'backendPasswordReset'])->name('backend.password.reset');

// Frontend & Backend
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('auth');
Route::put('password', [PasswordController::class, 'update'])->name('password.update')->middleware('auth');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

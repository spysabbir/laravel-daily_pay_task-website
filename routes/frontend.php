<?php

use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('about.us');
Route::get('/contact-us', [FrontendController::class, 'contactUs'])->name('contact.us');
Route::get('/faq', [FrontendController::class, 'faq'])->name('faq');
Route::get('/how-it-works', [FrontendController::class, 'howItWorks'])->name('how.it.works');
Route::get('/referral-program', [FrontendController::class, 'referralProgram'])->name('referral.program');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-and-conditions', [FrontendController::class, 'termsAndConditions'])->name('terms.and.conditions');

Route::middleware(['auth', 'verified', 'check_user_type:Frontend'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit', [UserController::class, 'profileEdit'])->name('profile.edit');
    Route::get('/profile/setting', [UserController::class, 'profileSetting'])->name('profile.setting');

    Route::get('/nid-verification', [UserController::class, 'nidVerification'])->name('nid.verification');
    Route::post('/nid-verification', [UserController::class, 'nidVerificationStore'])->name('nid.verification.store');
});


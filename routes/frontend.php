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

    Route::get('/verification', [UserController::class, 'verification'])->name('verification');
    Route::post('/verification', [UserController::class, 'verificationStore'])->name('verification.store');

    Route::get('/find-works', [UserController::class, 'findWorks'])->name('find.works');
    Route::get('/work-details/{id}', [UserController::class, 'workDetails'])->name('work.details');
    Route::post('/work-details/{id}/apply', [UserController::class, 'workApplyStore'])->name('work.apply.store');

    Route::get('/work-list-pending', [UserController::class, 'workListPending'])->name('work.list.pending');
    Route::get('/work-list-approved', [UserController::class, 'workListApproved'])->name('work.list.approved');
    Route::get('/work-list-rejected', [UserController::class, 'workListRejected'])->name('work.list.rejected');

    Route::get('/post-job', [UserController::class, 'postJob'])->name('post.job');
    Route::get('/post-job-get-sub-category', [UserController::class, 'getSubCategory'])->name('post_job.get_sub_category');
    Route::get('/post-job-get-child-category', [UserController::class, 'getChildCategory'])->name('post_job.get_child_category');
    Route::get('/post-job-get-job-charges', [UserController::class, 'getJobCharge'])->name('post_job.get_job_charge');
    Route::post('/post-job', [UserController::class, 'postJobStore'])->name('post_job.submit');


    Route::get('/job-list-running', [UserController::class, 'jobListRunning'])->name('job.list.running');
    Route::get('/job-list-completed', [UserController::class, 'jobListCompleted'])->name('job.list.completed');


    Route::get('/deposit', [UserController::class, 'deposit'])->name('deposit');
    Route::post('/deposit', [UserController::class, 'depositStore'])->name('deposit.store');

    Route::get('/withdraw', [UserController::class, 'withdraw'])->name('withdraw');
    Route::post('/withdraw', [UserController::class, 'withdrawStore'])->name('withdraw.store');

    Route::get('/notification', [UserController::class, 'notification'])->name('notification');
    Route::get('/notification/read/{id}', [UserController::class, 'notificationRead'])->name('notification.read');
    Route::get('/notification/read-all', [UserController::class, 'notificationReadAll'])->name('notification.read.all');

    Route::get('/refferal', [UserController::class, 'refferal'])->name('refferal');

});


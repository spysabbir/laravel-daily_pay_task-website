<?php

use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\JobListController;
use App\Http\Controllers\Frontend\PostJobController;
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

    Route::get('/user-profile/{id}', [UserController::class, 'userProfile'])->name('user.profile');
    Route::get('/block-user/{id}', [UserController::class, 'blockUser'])->name('block_user');
    Route::post('/report-user/{id}', [UserController::class, 'reportUser'])->name('report_user');

    Route::get('/verification', [UserController::class, 'verification'])->name('verification');
    Route::post('/verification', [UserController::class, 'verificationStore'])->name('verification.store');
    Route::put('/re-verification', [UserController::class, 'reVerificationStore'])->name('re-verification.store');

    Route::get('/find-works', [UserController::class, 'findWorks'])->name('find.works');
    Route::get('/work-details/{id}', [UserController::class, 'workDetails'])->name('work.details');
    Route::post('/work-proof-submit/{id}', [UserController::class, 'workProofSubmit'])->name('work.proof.submit');

    Route::get('/work-list-pending', [UserController::class, 'workListPending'])->name('work.list.pending');
    Route::get('/work-list-approved', [UserController::class, 'workListApproved'])->name('work.list.approved');
    Route::get('/work-list-rejected', [UserController::class, 'workListRejected'])->name('work.list.rejected');
    Route::get('/work-list-reviewed', [UserController::class, 'workListReviewed'])->name('work.list.reviewed');
    Route::get('/approved-job-view/{id}', [UserController::class, 'approvedJobView'])->name('approved.job.view');
    Route::get('/reviewed-job-view/{id}', [UserController::class, 'reviewedJobView'])->name('reviewed.job.view');
    Route::get('/rejected.job.check/{id}', [UserController::class, 'rejectedJobCheck'])->name('rejected.job.check');
    Route::put('/rejected.job.reviewed/{id}', [UserController::class, 'rejectedJobReviewed'])->name('rejected.job.reviewed');

    Route::get('/post-job', [PostJobController::class, 'postJob'])->name('post.job');
    Route::get('/post-job-get-sub-category', [PostJobController::class, 'getSubCategories'])->name('post_job.get_sub_category');
    Route::get('/post-job-get-child-category', [PostJobController::class, 'getChildCategories'])->name('post_job.get_child_category');
    Route::get('/post-job-get_job_post_charge', [PostJobController::class, 'getJobPostCharge'])->name('post_job.get_job_post_charge');
    Route::post('/post-job', [PostJobController::class, 'postJobStore'])->name('post_job.submit');
    Route::get('/post_job-edit/{id}', [PostJobController::class, 'postJobEdit'])->name('post_job.edit');
    Route::put('/post_job-update/{id}', [PostJobController::class, 'postJobUpdate'])->name('post_job.update');

    Route::get('/job-list-pending', [JobListController::class, 'jobListPending'])->name('job.list.pending');
    Route::get('/job-list-rejected', [JobListController::class, 'jobListRejected'])->name('job.list.rejected');
    Route::get('/job-list-canceled', [JobListController::class, 'jobListCanceled'])->name('job.list.canceled');
    Route::get('/job-list-paused', [JobListController::class, 'jobListPaused'])->name('job.list.paused');
    Route::get('/job-view/{id}', [JobListController::class, 'jobView'])->name('job.view');

    Route::get('/job-list-running', [JobListController::class, 'jobListRunning'])->name('job.list.running');
    Route::get('/running-job-paused_resume/{id}', [JobListController::class, 'runningJobPausedResume'])->name('running_job.paused_resume');
    Route::post('/running-job-canceled/{id}', [JobListController::class, 'runningJobCanceled'])->name('running_job.canceled');
    Route::get('/running-job-edit/{id}', [JobListController::class, 'runningJobEdit'])->name('running_job.edit');
    Route::put('/running-job-update/{id}', [JobListController::class, 'runningJobUpdate'])->name('running_job.update');
    Route::get('/running-job-show/{id}', [JobListController::class, 'runningJobShow'])->name('running_job.show');
    Route::get('/running-job-proof-check/{id}', [JobListController::class, 'runningJobProofCheck'])->name('running_job.proof.check');
    Route::put('/running-job-proof-check-update/{id}', [JobListController::class, 'runningJobProofCheckUpdate'])->name('running_job.proof.check.update');
    Route::get('/running-job-approved_all/{id}', [JobListController::class, 'runningJobApprovedAll'])->name('running_job.approved_all');
    Route::post('/running-job-selected_item_approved', [JobListController::class, 'runningJobSelectedItemApproved'])->name('running_job.selected_item_approved');
    Route::post('/running-job-selected_item_rejected', [JobListController::class, 'runningJobSelectedItemRejected'])->name('running_job.selected_item_rejected');

    Route::get('/job-list-completed', [JobListController::class, 'jobListCompleted'])->name('job.list.completed');

    Route::get('/deposit', [UserController::class, 'deposit'])->name('deposit');
    Route::post('/deposit', [UserController::class, 'depositStore'])->name('deposit.store');

    Route::get('/withdraw', [UserController::class, 'withdraw'])->name('withdraw');
    Route::post('/withdraw', [UserController::class, 'withdrawStore'])->name('withdraw.store');

    Route::get('/notification', [UserController::class, 'notification'])->name('notification');
    Route::get('/notification/read/{id}', [UserController::class, 'notificationRead'])->name('notification.read');
    Route::get('/notification/read-all', [UserController::class, 'notificationReadAll'])->name('notification.read.all');

    Route::get('/refferal', [UserController::class, 'refferal'])->name('refferal');

});


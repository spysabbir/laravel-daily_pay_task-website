<?php

use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\PostingTaskController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\WorkingTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('about.us');
Route::get('/contact-us', [FrontendController::class, 'contactUs'])->name('contact.us');
Route::get('/faq', [FrontendController::class, 'faq'])->name('faq');
Route::get('/how-it-works', [FrontendController::class, 'howItWorks'])->name('how.it.works');
Route::get('/referral-program', [FrontendController::class, 'referralProgram'])->name('referral.program');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-and-conditions', [FrontendController::class, 'termsAndConditions'])->name('terms.and.conditions');

Route::post('/subscribe', [FrontendController::class, 'subscribe'])->name('subscribe');
Route::post('/contact-store', [FrontendController::class, 'contactStore'])->name('contact.store');

Route::middleware(['auth', 'verified', 'check_user_type:Frontend'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit', [UserController::class, 'profileEdit'])->name('profile.edit');
    Route::get('/profile/setting', [UserController::class, 'profileSetting'])->name('profile.setting');

    Route::get('/user-profile/{id}', [UserController::class, 'userProfile'])->name('user.profile');
    Route::get('/block-list', [UserController::class, 'blockList'])->name('block_list');
    Route::get('/block-user/{id}', [UserController::class, 'blockUser'])->name('block_user');
    Route::get('/report-list', [UserController::class, 'reportList'])->name('report_list');
    Route::get('/report-view/{id}', [UserController::class, 'reportView'])->name('report_view');
    Route::post('/report-user/{id}', [UserController::class, 'reportUser'])->name('report_user');

    Route::get('/verification', [UserController::class, 'verification'])->name('verification');
    Route::post('/verification', [UserController::class, 'verificationStore'])->name('verification.store');
    Route::put('/re-verification', [UserController::class, 'reVerificationStore'])->name('re-verification.store');
    // Working Task
    Route::get('/find_tasks', [WorkingTaskController::class, 'findTasks'])->name('find_tasks');
    Route::get('/find_task-details/{id}', [WorkingTaskController::class, 'findTaskDetails'])->name('find_task.details');
    Route::post('/find_task-proof-submit/{id}', [WorkingTaskController::class, 'findTaskProofSubmit'])->name('find_task.proof.submit');

    Route::get('/working_task-list-pending', [WorkingTaskController::class, 'workingTaskListPending'])->name('working_task.list.pending');
    Route::get('/working_task-list-approved', [WorkingTaskController::class, 'workingTaskListApproved'])->name('working_task.list.approved');
    Route::get('/working_task-list-rejected', [WorkingTaskController::class, 'workingTaskListRejected'])->name('working_task.list.rejected');
    Route::get('/working_task-list-reviewed', [WorkingTaskController::class, 'workingTaskListReviewed'])->name('working_task.list.reviewed');

    Route::get('/approved-working_task-view/{id}', [WorkingTaskController::class, 'approvedWorkingTaskView'])->name('approved.working_task.view');
    Route::get('/reviewed-working_task-view/{id}', [WorkingTaskController::class, 'reviewedWorkingTaskView'])->name('reviewed.working_task.view');
    Route::get('/rejected-working_task-check/{id}', [WorkingTaskController::class, 'rejectedWorkingTaskCheck'])->name('rejected.working_task.check');
    Route::put('/rejected-working_task-reviewed/{id}', [WorkingTaskController::class, 'rejectedWorkingTaskReviewed'])->name('rejected.working_task.reviewed');
    // Posting Task
    Route::get('/post_task', [PostingTaskController::class, 'postTask'])->name('post_task');
    Route::get('/post_task-get-sub-category', [PostingTaskController::class, 'postTaskGetSubCategories'])->name('post_task.get.sub.category');
    Route::get('/post_task-get-child-category', [PostingTaskController::class, 'postTaskGetChildCategories'])->name('post_task.get.child.category');
    Route::get('/post_task-get-task-post-charge', [PostingTaskController::class, 'postTaskGetTaskPostCharge'])->name('post_task.get.task.post.charge');
    Route::post('/post_task-store', [PostingTaskController::class, 'postTaskStore'])->name('post_task.store');
    Route::get('/post_task-edit/{id}', [PostingTaskController::class, 'postTaskEdit'])->name('post_task.edit');
    Route::put('/post_task-update/{id}', [PostingTaskController::class, 'postTaskUpdate'])->name('post_task.update');

    Route::get('/posting_task-list-pending', [PostingTaskController::class, 'postingTaskListPending'])->name('posting_task.list.pending');
    Route::get('/posting_task-list-rejected', [PostingTaskController::class, 'postingTaskListRejected'])->name('posting_task.list.rejected');
    Route::get('/posting_task-list-canceled', [PostingTaskController::class, 'postingTaskListCanceled'])->name('posting_task.list.canceled');
    Route::get('/posting_task-list-paused', [PostingTaskController::class, 'postingTaskListPaused'])->name('posting_task.list.paused');
    Route::get('/posting_task-list-running', [PostingTaskController::class, 'postingTaskListRunning'])->name('posting_task.list.running');
    Route::get('/posting_task-list-completed', [PostingTaskController::class, 'postingTaskListCompleted'])->name('posting_task.list.completed');
    Route::get('/posting_task-view/{id}', [PostingTaskController::class, 'postingTaskView'])->name('posting_task.view');

    Route::get('/running-posting_task-paused-resume/{id}', [PostingTaskController::class, 'postingRunningTaskPausedResume'])->name('running.posting_task.paused.resume');
    Route::post('/running-posting_task-canceled/{id}', [PostingTaskController::class, 'postingRunningTaskCanceled'])->name('running.posting_task.canceled');
    Route::get('/running-posting_task-edit/{id}', [PostingTaskController::class, 'postingRunningTaskEdit'])->name('running.posting_task.edit');
    Route::put('/running-posting_task-update/{id}', [PostingTaskController::class, 'postingRunningTaskUpdate'])->name('running.posting_task.update');
    Route::get('/running-posting_task-show/{id}', [PostingTaskController::class, 'postingRunningTaskShow'])->name('running.posting_task.show');
    Route::get('/running-posting_task-proof-check/{id}', [PostingTaskController::class, 'postingRunningTaskProofCheck'])->name('running.posting_task.proof.check');
    Route::put('/running-posting_task-proof-check-update/{id}', [PostingTaskController::class, 'postingRunningTaskProofCheckUpdate'])->name('running.posting_task.proof.check.update');
    Route::get('/running-posting_task-approved-all/{id}', [PostingTaskController::class, 'postingRunningTaskApprovedAll'])->name('running.posting_task.approved.all');
    Route::post('/running-posting_task-selected-item-approved', [PostingTaskController::class, 'postingRunningTaskSelectedItemApproved'])->name('running.posting_task.selected.item.approved');
    Route::post('/running-posting_task-selected-item-rejected', [PostingTaskController::class, 'postingRunningTaskSelectedItemRejected'])->name('running.posting_task.selected.item.rejected');

    Route::get('/deposit', [UserController::class, 'deposit'])->name('deposit');
    Route::post('/deposit', [UserController::class, 'depositStore'])->name('deposit.store');
    Route::post('withdrawal/balance/deposit', [UserController::class, 'withdrawalBalanceDepositStore'])->name('withdrawal.balance.deposit.store');

    Route::get('/withdraw', [UserController::class, 'withdraw'])->name('withdraw');
    Route::post('/withdraw', [UserController::class, 'withdrawStore'])->name('withdraw.store');

    Route::get('/bonus', [UserController::class, 'bonus'])->name('bonus');

    Route::get('/notification', [UserController::class, 'notification'])->name('notification');
    Route::get('/notification/read/{id}', [UserController::class, 'notificationRead'])->name('notification.read');
    Route::get('/notification/read-all', [UserController::class, 'notificationReadAll'])->name('notification.read.all');

    Route::get('/refferal', [UserController::class, 'refferal'])->name('refferal');

    Route::get('/support', [UserController::class, 'support'])->name('support');
    Route::post('/support-send-message', [UserController::class, 'supportSendMessage'])->name('support.send-message');
});


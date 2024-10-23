<?php

use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\PostedTaskController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\WorkedTaskController;
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

    Route::get('/block-unblock-user/{id}', [UserController::class, 'blockUnblockUser'])->name('block.unblock.user');
    Route::get('/block-list', [UserController::class, 'blockList'])->name('block_list');

    Route::post('/report-send/{id}', [UserController::class, 'reportSend'])->name('report.send');
    Route::get('/report-list', [UserController::class, 'reportList'])->name('report_list');
    Route::get('/report-view/{id}', [UserController::class, 'reportView'])->name('report_view');

    Route::get('/verification', [UserController::class, 'verification'])->name('verification');
    Route::post('/verification', [UserController::class, 'verificationStore'])->name('verification.store');
    Route::put('/re-verification', [UserController::class, 'reVerificationStore'])->name('re-verification.store');
    // Worked Task
    Route::get('/find_tasks', [WorkedTaskController::class, 'findTasks'])->name('find_tasks');
    Route::get('/find_task-details/{id}', [WorkedTaskController::class, 'findTaskDetails'])->name('find_task.details');
    Route::get('/find_task-proof-submit-limit-check/{id}', [WorkedTaskController::class, 'findTaskProofSubmitLimitCheck'])->name('find_task.proof.submit.limit.check');
    Route::post('/find_task-proof-submit/{id}', [WorkedTaskController::class, 'findTaskProofSubmit'])->name('find_task.proof.submit');

    Route::get('/worked_task-list-pending', [WorkedTaskController::class, 'workedTaskListPending'])->name('worked_task.list.pending');
    Route::get('/worked_task-list-approved', [WorkedTaskController::class, 'workedTaskListApproved'])->name('worked_task.list.approved');
    Route::get('/worked_task-list-rejected', [WorkedTaskController::class, 'workedTaskListRejected'])->name('worked_task.list.rejected');
    Route::get('/worked_task-list-reviewed', [WorkedTaskController::class, 'workedTaskListReviewed'])->name('worked_task.list.reviewed');

    Route::get('/approved-worked_task-view/{id}', [WorkedTaskController::class, 'approvedWorkedTaskView'])->name('approved.worked_task.view');
    Route::get('/reviewed-worked_task-view/{id}', [WorkedTaskController::class, 'reviewedWorkedTaskView'])->name('reviewed.worked_task.view');
    Route::get('/rejected-worked_task-check/{id}', [WorkedTaskController::class, 'rejectedWorkedTaskCheck'])->name('rejected.worked_task.check');
    Route::put('/rejected-worked_task-reviewed/{id}', [WorkedTaskController::class, 'rejectedWorkedTaskReviewed'])->name('rejected.worked_task.reviewed');
    // Posted Task
    Route::get('/post_task', [PostedTaskController::class, 'postTask'])->name('post_task');
    Route::get('/post_task-get-sub-category', [PostedTaskController::class, 'postTaskGetSubCategories'])->name('post_task.get.sub.category');
    Route::get('/post_task-get-child-category', [PostedTaskController::class, 'postTaskGetChildCategories'])->name('post_task.get.child.category');
    Route::get('/post_task-get-task-post-charge', [PostedTaskController::class, 'postTaskGetTaskPostCharge'])->name('post_task.get.task.post.charge');
    Route::post('/post_task-store', [PostedTaskController::class, 'postTaskStore'])->name('post_task.store');
    Route::get('/post_task-edit/{id}', [PostedTaskController::class, 'postTaskEdit'])->name('post_task.edit');
    Route::put('/post_task-update/{id}', [PostedTaskController::class, 'postTaskUpdate'])->name('post_task.update');

    Route::get('/posted_task-list-pending', [PostedTaskController::class, 'postedTaskListPending'])->name('posted_task.list.pending');
    Route::get('/posted_task-list-rejected', [PostedTaskController::class, 'postedTaskListRejected'])->name('posted_task.list.rejected');
    Route::get('/posted_task-list-canceled', [PostedTaskController::class, 'postedTaskListCanceled'])->name('posted_task.list.canceled');
    Route::get('/posted_task-list-paused', [PostedTaskController::class, 'postedTaskListPaused'])->name('posted_task.list.paused');
    Route::get('/posted_task-list-running', [PostedTaskController::class, 'postedTaskListRunning'])->name('posted_task.list.running');
    Route::get('/posted_task-list-completed', [PostedTaskController::class, 'postedTaskListCompleted'])->name('posted_task.list.completed');
    Route::get('/posted_task-view/{id}', [PostedTaskController::class, 'postedTaskView'])->name('posted_task.view');

    Route::get('/running-posted_task-paused-resume/{id}', [PostedTaskController::class, 'runningPostedTaskPausedResume'])->name('running.posted_task.paused.resume');
    Route::post('/running-posted_task-canceled/{id}', [PostedTaskController::class, 'runningPostedTaskCanceled'])->name('running.posted_task.canceled');
    Route::get('/running-posted_task-edit/{id}', [PostedTaskController::class, 'runningPostedTaskEdit'])->name('running.posted_task.edit');
    Route::put('/running-posted_task-update/{id}', [PostedTaskController::class, 'runningPostedTaskUpdate'])->name('running.posted_task.update');

    Route::get('/proof_task-list/{id}', [PostedTaskController::class, 'proofTaskList'])->name('proof_task.list');
    Route::get('/proof_task-check/{id}', [PostedTaskController::class, 'proofTaskCheck'])->name('proof_task.check');
    Route::put('/proof_task-check-update/{id}', [PostedTaskController::class, 'proofTaskCheckUpdate'])->name('proof_task.check.update');
    Route::get('/proof_task-approved-all/{id}', [PostedTaskController::class, 'proofTaskApprovedAll'])->name('proof_task.approved.all');
    Route::post('/proof_task-selected-item-approved', [PostedTaskController::class, 'proofTaskSelectedItemApproved'])->name('proof_task.selected.item.approved');
    Route::post('/proof_task-selected-item-rejected', [PostedTaskController::class, 'proofTaskSelectedItemRejected'])->name('proof_task.selected.item.rejected');
    Route::get('/proof_task-report/{id}', [PostedTaskController::class, 'proofTaskReport'])->name('proof_task.report');

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


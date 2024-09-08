<?php

use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ChildCategoryController;
use App\Http\Controllers\Backend\DepositController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\JobPostChargeController;
use App\Http\Controllers\Backend\JobController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\RolePermissionController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SubCategoryController;
use App\Http\Controllers\Backend\VerificationController;
use App\Http\Controllers\Backend\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::prefix('backend')->name('backend.')->middleware(['auth', 'check_user_type:Backend'])->group(function() {
    Route::get('/dashboard', [BackendController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit', [BackendController::class, 'profileEdit'])->name('profile.edit');
    Route::get('/profile/setting', [BackendController::class, 'profileSetting'])->name('profile.setting');
    // Role & Permission
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('role-permission', RolePermissionController::class);
    // Setting
    Route::get('site/setting', [SettingController::class, 'siteSetting'])->name('site.setting');
    Route::post('site/setting/update', [SettingController::class, 'siteSettingUpdate'])->name('site.setting.update');
    Route::get('default/setting', [SettingController::class, 'defaultSetting'])->name('default.setting');
    Route::post('default/setting/update', [SettingController::class, 'defaultSettingUpdate'])->name('default.setting.update');
    Route::get('seo/setting', [SettingController::class, 'seoSetting'])->name('seo.setting');
    Route::post('seo/setting/update', [SettingController::class, 'seoSettingUpdate'])->name('seo.setting.update');
    Route::get('mail/setting', [SettingController::class, 'mailSetting'])->name('mail.setting');
    Route::post('mail/setting/update', [SettingController::class, 'mailSettingUpdate'])->name('mail.setting.update');
    Route::get('sms/setting', [SettingController::class, 'smsSetting'])->name('sms.setting');
    Route::post('sms/setting/update', [SettingController::class, 'smsSettingUpdate'])->name('sms.setting.update');
    Route::get('captcha/setting', [SettingController::class, 'captchaSetting'])->name('captcha.setting');
    Route::post('captcha/setting/update', [SettingController::class, 'captchaSettingUpdate'])->name('captcha.setting.update');
    // Employee
    Route::resource('employee', EmployeeController::class);
    Route::get('employee-trash', [EmployeeController::class, 'trash'])->name('employee.trash');
    Route::get('employee/restore/{id}', [EmployeeController::class, 'restore'])->name('employee.restore');
    Route::get('employee/delete/{id}', [EmployeeController::class, 'delete'])->name('employee.delete');
    Route::get('employee/status/{id}', [EmployeeController::class, 'status'])->name('employee.status');
    // User
    Route::get('user/active', [BackendController::class, 'userActiveList'])->name('user.active');
    Route::get('user/show/{id}', [BackendController::class, 'userView'])->name('user.show');
    Route::get('user/edit/{id}', [BackendController::class, 'userEdit'])->name('user.edit');
    Route::put('user/update/{id}', [BackendController::class, 'userUpdate'])->name('user.update');
    Route::delete('user/destroy/{id}', [BackendController::class, 'userDestroy'])->name('user.destroy');
    Route::get('user/trash', [BackendController::class, 'userTrash'])->name('user.trash');
    Route::get('user/restore/{id}', [BackendController::class, 'userRestore'])->name('user.restore');
    Route::get('user/delete/{id}', [BackendController::class, 'userDelete'])->name('user.delete');
    Route::get('user/inactive', [BackendController::class, 'userInactiveList'])->name('user.inactive');
    Route::get('user/blocked', [BackendController::class, 'userBlockedList'])->name('user.blocked');
    Route::get('user/banned', [BackendController::class, 'userBannedList'])->name('user.banned');
    // Category
    Route::resource('category', CategoryController::class);
    Route::get('category-trash', [CategoryController::class, 'trash'])->name('category.trash');
    Route::get('category/restore/{id}', [CategoryController::class, 'restore'])->name('category.restore');
    Route::get('category/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');
    Route::get('category/status/{id}', [CategoryController::class, 'status'])->name('category.status');
    // Sub Category
    Route::resource('sub_category', SubCategoryController::class);
    Route::get('sub_category-trash', [SubCategoryController::class, 'trash'])->name('sub_category.trash');
    Route::get('sub_category/restore/{id}', [SubCategoryController::class, 'restore'])->name('sub_category.restore');
    Route::get('sub_category/delete/{id}', [SubCategoryController::class, 'delete'])->name('sub_category.delete');
    Route::get('sub_category/status/{id}', [SubCategoryController::class, 'status'])->name('sub_category.status');
    // Child Category
    Route::resource('child_category', ChildCategoryController::class);
    Route::get('child_category-get_sub_category', [ChildCategoryController::class, 'getSubCategories'])->name('child_category.get_sub_categories');
    Route::get('child_category-trash', [ChildCategoryController::class, 'trash'])->name('child_category.trash');
    Route::get('child_category/restore/{id}', [ChildCategoryController::class, 'restore'])->name('child_category.restore');
    Route::get('child_category/delete/{id}', [ChildCategoryController::class, 'delete'])->name('child_category.delete');
    Route::get('child_category/status/{id}', [ChildCategoryController::class, 'status'])->name('child_category.status');
    // Job Charge
    Route::resource('job_post_charge', JobPostChargeController::class);
    Route::get('job_post_charge-get_sub_category', [JobPostChargeController::class, 'getSubCategories'])->name('job_post_charge.get_sub_categories');
    Route::get('job_post_charge-get_child_category', [JobPostChargeController::class, 'getChildCategories'])->name('job_post_charge.get_child_categories');
    Route::get('job_post_charge-trash', [JobPostChargeController::class, 'trash'])->name('job_post_charge.trash');
    Route::get('job_post_charge/restore/{id}', [JobPostChargeController::class, 'restore'])->name('job_post_charge.restore');
    Route::get('job_post_charge/delete/{id}', [JobPostChargeController::class, 'delete'])->name('job_post_charge.delete');
    Route::get('job_post_charge/status/{id}', [JobPostChargeController::class, 'status'])->name('job_post_charge.status');
    // Faq
    Route::resource('faq', FaqController::class);
    Route::get('faq-trash', [FaqController::class, 'trash'])->name('faq.trash');
    Route::get('faq/restore/{id}', [FaqController::class, 'restore'])->name('faq.restore');
    Route::get('faq/delete/{id}', [FaqController::class, 'delete'])->name('faq.delete');
    Route::get('faq/status/{id}', [FaqController::class, 'status'])->name('faq.status');
    // Verification
    Route::get('verification-request', [VerificationController::class, 'verificationRequest'])->name('verification.request');
    Route::get('verification-request/{id}', [VerificationController::class, 'verificationRequestShow'])->name('verification.request.show');
    Route::put('verification-request-status-change/{id}', [VerificationController::class, 'verificationRequestStatusChange'])->name('verification.request.status.change');
    Route::get('verification-request-rejected', [VerificationController::class, 'verificationRequestRejected'])->name('verification.request.rejected');
    Route::get('verification-request-approved', [VerificationController::class, 'verificationRequestApproved'])->name('verification.request.approved');
    Route::delete('verification-request-delete/{id}', [VerificationController::class, 'verificationRequestDelete'])->name('verification.request.delete');
    // Deposit
    Route::get('deposit-request', [DepositController::class, 'depositRequest'])->name('deposit.request');
    Route::get('deposit-request/{id}', [DepositController::class, 'depositRequestShow'])->name('deposit.request.show');
    Route::put('deposit-request-status-change/{id}', [DepositController::class, 'depositRequestStatusChange'])->name('deposit.request.status.change');
    Route::get('deposit-request-rejected', [DepositController::class, 'depositRequestRejected'])->name('deposit.request.rejected');
    Route::get('deposit-request-approved', [DepositController::class, 'depositRequestApproved'])->name('deposit.request.approved');
    Route::delete('deposit-request-delete/{id}', [DepositController::class, 'depositRequestDelete'])->name('deposit.request.delete');
    // Withdraw
    Route::get('withdraw-request', [WithdrawController::class, 'withdrawRequest'])->name('withdraw.request');
    Route::get('withdraw-request/{id}', [WithdrawController::class, 'withdrawRequestShow'])->name('withdraw.request.show');
    Route::put('withdraw-request-status-change/{id}', [WithdrawController::class, 'withdrawRequestStatusChange'])->name('withdraw.request.status.change');
    Route::get('withdraw-request-rejected', [WithdrawController::class, 'withdrawRequestRejected'])->name('withdraw.request.rejected');
    Route::get('withdraw-request-approved', [WithdrawController::class, 'withdrawRequestApproved'])->name('withdraw.request.approved');
    Route::delete('withdraw-request-delete/{id}', [WithdrawController::class, 'withdrawRequestDelete'])->name('withdraw.request.delete');
    // Job List
    Route::get('job_list-pending', [JobController::class, 'jobListPending'])->name('job_list.pending');
    Route::get('pending-job-view/{id}', [JobController::class, 'pendingJobView'])->name('pending.job_view');
    Route::get('job_list-running', [JobController::class, 'jobListRunning'])->name('job_list.running');
    Route::get('running-job-view/{id}', [JobController::class, 'runningJobView'])->name('running.job_view');
    Route::get('job_list-rejected', [JobController::class, 'jobListRejected'])->name('job_list.rejected');
    Route::get('rejected-job-view/{id}', [JobController::class, 'rejectedJobView'])->name('rejected.job_view');
    Route::get('job_list-canceled', [JobController::class, 'jobListCanceled'])->name('job_list.canceled');
    Route::get('canceled-job-view/{id}', [JobController::class, 'canceledJobView'])->name('canceled.job_view');
    Route::get('job_list-completed', [JobController::class, 'jobListCompleted'])->name('job_list.completed');
    Route::get('completed-job-view/{id}', [JobController::class, 'completedJobView'])->name('completed.job_view');
    Route::put('job-status-update/{id}', [JobController::class, 'jobStatusUpdate'])->name('job_status_update');

    Route::get('job_proof-pending', [JobController::class, 'jobProofPending'])->name('job_proof.pending');
    Route::get('job_proof-pending-list/{id}', [JobController::class, 'jobProofPendingList'])->name('job_proof.pending.list');
    Route::get('job_proof-rejected', [JobController::class, 'jobProofRejected'])->name('job_proof.rejected');
    Route::get('job_proof-rejected-list/{id}', [JobController::class, 'jobProofRejectedList'])->name('job_proof.rejected.list');
    Route::get('job_proof-reviewed', [JobController::class, 'jobProofReviewed'])->name('job_proof.reviewed');
    Route::get('job_proof-reviewed-list/{id}', [JobController::class, 'jobProofReviewedList'])->name('job_proof.reviewed.list');
    Route::get('job_proof-check/{id}', [JobController::class, 'jobProofCheck'])->name('job_proof.check');
    Route::put('job_proof-check-update/{id}', [JobController::class, 'jobProofCheckUpdate'])->name('job_proof.check.update');

});

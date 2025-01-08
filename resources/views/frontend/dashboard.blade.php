@extends('layouts.template_master')

@section('title', 'Dashboard')

@section('content')
<!-- Verify Account Status -->
@if (Auth::user()->hasVerification('Pending'))
<div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <div class="alert-heading mb-3">
        <i data-feather="alert-circle"></i>
        <h4>Account Verification Pending!</h4>
    </div>
    <p class="mt-3">
        Your account verification is pending. Please wait for admin approval. Admin will verify your account as soon as possible. If you have any issue, please contact with us. We are always ready to help you.
    </p>
    <hr>
    <div class="mb-0">
        <a href="{{ route('verification') }}" class="btn btn-info btn-sm">Status Check</a>
        <a href="{{ route('support') }}" class="btn btn-primary btn-sm">Contact Us</a>
    </div>
</div>
@elseif (Auth::user()->hasVerification('Rejected'))
<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
    <div class="alert-heading mb-3">
        <i data-feather="alert-circle"></i>
        <h4>Account Verification Rejected!</h4>
    </div>
    <p class="mt-3">
        Your account verification is rejected by admin. Please contact with us to re-verify your account. We are always ready to help you.
    </p>
    <hr>
    <div class="mb-0">
        <a href="{{ route('verification') }}" class="btn btn-danger btn-sm">Re-Verify</a>
        <a href="{{ route('support') }}" class="btn btn-primary btn-sm">Contact Us</a>
    </div>
</div>
@elseif (Auth::user()->hasVerification('Approved'))
<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <div class="alert-heading mb-3">
        <h4>Welcome to {{ config('app.name') }}!</h4>
    </div>
    <hr>
    <marquee><strong class="text-info">Notice: {{ get_site_settings('site_notice') }}</strong></marquee>
</div>
@else
<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
    <div class="alert-heading mb-3">
        <i data-feather="alert-circle"></i>
        <h4>Account Verification Required!</h4>
    </div>
    <p class="mt-3">
        Your account verification is required. Please verify your account to access your account. If you have any issue, please contact with us. We are always ready to help you.
    </p>
    <hr>
    <div class="mb-0">
        <a href="{{ route('verification') }}" class="btn btn-warning btn-sm">Verify Now</a>
        <a href="{{ route('support') }}" class="btn btn-primary btn-sm">Contact Us</a>
    </div>
</div>
@endif

<!-- User Account Status -->
@if (Auth::user()->status == 'Blocked')
<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
	<div class="alert-heading mb-3">
        <i data-feather="alert-circle"></i>
        <h4> Your account is blocked!</h4>
    </div>
	<p class="mt-3">
        Your account is blocked by admin. You can't access your account. Please wait for the unblock! If you think this is a mistake, please contact us! We are always ready to help you.
    </p>
    <hr>
    @if ($userStatus->created_by == Auth::user()->id)
        <span class="text-success mt-2">
            Already send Iinstant unblock request to admin. Please wait for the admin response. If your account is not unblocked within 1 hour, please contact us.
        </span><br>
        <span class="text-info mt-2">
            Note: If your account is automatically unblocked before the admin response, you will get an unblock charge refund on your withdraw balance.
        </span>
    @else
    <div>
        <strong>Blocked Reason: {{ $userStatus->reason }}</strong><br>
        <strong>Blocked Time: {{ date('d M, Y h:i A', strtotime($userStatus->created_at)) }}</strong><br>
        <strong>Blocked Duration: {{ $userStatus->blocked_duration }} hours</strong><br>
        <strong>Unblocked Time: {{ date('d M, Y h:i A', strtotime($userStatus->created_at) + $userStatus->blocked_duration * 60 * 60) }}</strong> <br>
        <span class="text-info">
            If you want to unblock your account before the unblocked time, you can pay the unblock charge. The unblock charge is <strong>{{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('user_blocked_instant_resolved_charge') }}</strong>.<br>
        </span>
        <span class="text-info mt-2">
            Note: If your account is automatically unblocked before the admin response, you will get an unblock charge refund on your withdraw balance.
        </span>
    </div>
    @endif
	<hr>
	<div class="mb-0 d-flex justify-content-center align-items-center flex-wrap">
        <a href="{{ route('support') }}" class="btn btn-danger btn-xs mx-2">Contact Us</a>
        @if ($userStatus->created_by != Auth::user()->id)
            <button type="button" class="btn btn-primary btn-xs mx-2" data-bs-toggle="modal" data-bs-target=".instantUnblockedModel">Instant Unblocked</button>
        @endif
        <!-- Normal Withdraw Modal -->
        <div class="modal fade instantUnblockedModel" tabindex="-1" aria-labelledby="instantUnblockedModelLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="instantUnblockedModelLabel">Instant Unblocked</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                    </div>
                    <form class="forms-sample" id="instantUnblockedModelForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="Deposit Balance">Deposit Balance</option>
                                    <option value="Withdraw Balance">Withdraw Balance</option>
                                </select>
                                <span class="text-danger error-text payment_method_error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Enter reason" required></textarea>
                                <span class="text-danger error-text reason_error"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if (Auth::user()->status == 'Banned')
<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
	<div class="alert-heading mb-3">
        <i data-feather="alert-circle"></i>
        <h4> Your account is banned!</h4>
    </div>
	<p class="mt-3">
        Your account is banned by admin. You can't access your account. If you think this is a mistake, please contact us! We are always ready to help you.
    </p>
	<hr>
    <div>
        <strong>Banned Reason: {{ $userStatus->reason }}</strong><br>
        <strong>Banned Time: {{ date('d M, Y h:i A', strtotime($userStatus->created_at)) }}</strong>
    </div>
	<hr>
	<div class="mb-0">
        <a href="{{ route('support') }}" class="btn btn-danger btn-sm">Contact Us</a>
    </div>
</div>
@endif

<!-- Statics -->
<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Posted Task Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $today_posted_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $monthly_posted_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $yearly_posted_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_posted_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Posted Task Proof Submit Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $today_posted_task_proof_submit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $monthly_posted_task_proof_submit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $yearly_posted_task_proof_submit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_posted_task_proof_submit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- row -->
<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Total Posted Task Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($total_pending_posted_task == 0 && $total_running_posted_task == 0 && $total_rejected_posted_task == 0 && $total_canceled_posted_task == 0 && $total_paused_posted_task == 0 && $total_completed_posted_task == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for total posted task status. Please check back later.
                    </p>
                </div>
                @else
                <canvas id="totalPostedTaskChartjsPie"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Total Posted Task Proof Submit Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($total_pending_posted_task_proof_submit == 0 && $total_approved_posted_task_proof_submit == 0 && $total_rejected_posted_task_proof_submit == 0 && $total_reviewed_posted_task_proof_submit == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for total posted task proof submit status. Please check back later.
                    </p>
                </div>
                @else
                <canvas id="totalPostedTaskProofSubmitChartjsDoughnut"></canvas>
                @endif
            </div>
        </div>
    </div>
</div><!-- row -->

<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Worked Task Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $today_worked_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $monthly_worked_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $yearly_worked_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_worked_task }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total - {{ Auth::user()->created_at->format('d M, Y') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Approved Posted Task Charge Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $postTaskChargeWaiting }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Waiting</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $postTaskChargePayment }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Payment</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $postTaskChargeRefund }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Refund</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $postTaskChargeHold }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-warning">
                                        <span>Hold</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- row -->
<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Total Worked Task Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($total_pending_worked_task == 0 && $total_approved_worked_task == 0 && $total_rejected_worked_task == 0 && $total_reviewed_worked_task == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for total worked task status. Please check back later.
                    </p>
                </div>
                @else
                <div id="totalWorkedTaskApexPie"></div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Total Approved Posted Task Charge</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($postTaskChargeWaiting == 0 && $postTaskChargePayment == 0 && $postTaskChargeRefund == 0 && $postTaskChargeHold == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for total approved posted task charge. Please check back later.
                    </p>
                </div>
                @else
                <div id="totalApprovedPostedTaskChargeApexDonut"></div>
                @endif
            </div>
        </div>
    </div>
</div><!-- row -->

<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Posted Task Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-2">
                                <h3 class="mb-2">{{ $total_pending_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-2">
                                <h3 class="mb-2">{{ $total_running_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Running</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-2">
                                <h3 class="mb-2">{{ $total_rejected_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-warning">
                                        <span>Rejected</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-2">
                                <h3 class="mb-2">{{ $total_canceled_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Canceled</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-2">
                                <h3 class="mb-2">{{ $total_paused_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Paused</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-2">
                                <h3 class="mb-2">{{ $total_completed_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-secondary">
                                        <span>Completed</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Posted Task Proof Submit Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_pending_posted_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_approved_posted_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_rejected_posted_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Rejected</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_reviewed_posted_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-warning">
                                        <span>Reviewed</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Worked Task Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_pending_worked_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_approved_worked_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_rejected_worked_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Rejected</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_reviewed_worked_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-warning">
                                        <span>Reviewed</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- row -->

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Total Balance Transfer Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($total_posted_task_proof_submit == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for total task proof submit status. Please check back later.
                    </p>
                </div>
                @else
                <canvas id="totalBalanceTransferChartjsLine">
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Total Report Send Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if (empty($totalReportSendApexLineData['categories']))
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for total report send status. Please check back later.
                        </p>
                    </div>
                @else
                    <div id="totalReportSendApexLine"></div>
                @endif
            </div>
        </div>
    </div>
</div><!-- row -->

<div class="row">
    <div class="col-xl-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Last 12 Months approved Deposit and Withdraw Amount Status<h6></h6>
                </div>
                <p class="text-muted">This chart shows the monthly approved deposit and withdraw amount.</p>
            </div>
            <div class="card-body">
                @if ($total_withdraw == 0 && $total_deposit == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for monthly approved deposit and withdraw amount status. Please check back later.
                    </p>
                </div>
                @else
                <div id="monthlyDepositAndWithdrawChart"></div>
                @endif
            </div>
        </div>
    </div>
</div> <!-- row -->

<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Approved Deposit Amount</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $monthly_deposit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $monthly_deposit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $yearly_deposit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_deposit }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Approved Withdraw Amount</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $monthly_withdraw }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $monthly_withdraw }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $yearly_withdraw }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_withdraw }}</h3>
                                <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Deposit Amount</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_pending_deposit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_approved_deposit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_rejected_deposit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Rejected</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Withdraw Amount</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_pending_withdraw }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_approved_withdraw }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_rejected_withdraw }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Rejected</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- row -->
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // instantUnblocked Data
        $('#instantUnblockedModelForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('instant.unblocked.request') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        if (response.status == 200) {
                            $('.instantUnblockedModel').modal('hide');
                            $('#instantUnblockedModelForm')[0].reset();
                            toastr.success('Withdraw request sent successfully.');

                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }else{
                            toastr.error(response.message);
                        }
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>
@endsection
<script>
    var total_posted_task_labels = ['Pending', 'Running', 'Paused', 'Rejected', 'Canceled', 'Completed'];
    var total_posted_task_series = [{{ $total_pending_posted_task }}, {{ $total_running_posted_task }}, {{ $total_paused_posted_task }}, {{ $total_rejected_posted_task }}, {{ $total_canceled_posted_task }}, {{ $total_completed_posted_task }}];

    var total_posted_task_proof_submit_labels = ['Pending', 'Approved', 'Rejected', 'Reviewed'];
    var total_posted_task_proof_submit_series = [{{ $total_pending_posted_task_proof_submit }}, {{ $total_approved_posted_task_proof_submit }}, {{ $total_rejected_posted_task_proof_submit }}, {{ $total_reviewed_posted_task_proof_submit }}];

    var total_worked_task_labels = ['Pending', 'Approved', 'Rejected', 'Reviewed'];
    var total_worked_task_series = [{{ $total_pending_worked_task }}, {{ $total_approved_worked_task }}, {{ $total_rejected_worked_task }}, {{ $total_reviewed_worked_task }}];

    var monthlyDepositAndWithdrawCategories = {!! json_encode(array_keys($monthlyWithdraw)) !!};
    var monthlyWithdrawSeries = {!! json_encode(array_values($monthlyWithdraw)) !!};
    var monthlyDepositeSeries = {!! json_encode(array_values($monthlyDeposite)) !!};

    var totalPostTaskChargeLabels = ['Waiting', 'Payment', 'Refund', 'Hold'];
    var totalPostTaskChargeSeries = [{{ round($postTaskChargeWaiting, 2) }}, {{ round($postTaskChargePayment, 2) }}, {{ round($postTaskChargeRefund, 2) }}, {{ round($postTaskChargeHold, 2) }}];

    const totalBalanceTransferChartjsLineData = @json($totalBalanceTransferChartjsLineData);
    const totalReportSendApexLineData = @json($totalReportSendApexLineData);
</script>

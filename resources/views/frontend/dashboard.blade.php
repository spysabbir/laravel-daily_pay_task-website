@extends('layouts.template_master')

@section('title', 'Dashboard')

@section('content')
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
    <marquee behavior="" direction=""><strong class="text-info">Notice: {{ get_site_settings('site_notice') }}</strong></marquee>
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

@if (Auth::user()->status == 'Blocked')
<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
	<div class="alert-heading mb-3">
        <i data-feather="alert-circle"></i>
        <h4> Your account is blocked!</h4>
    </div>
	<p class="mt-3">
        Your account is blocked by admin. You can't access your account. Please contact with us to unblock your account. We are always ready to help you.
    </p>
    <hr>
    <div>
        <strong>Blocked Reason: {{ $userStatus->reason }}</strong><br>
        <strong>Blocked Duration: {{ $userStatus->blocked_duration }} hours</strong><br>
        <strong>Blocked At: {{ date('d M, Y h:i A', strtotime($userStatus->created_at)) }}</strong>
    </div>
	<hr>
	<div class="mb-0">
        <a href="{{ route('support') }}" class="btn btn-danger btn-sm">Contact Us</a>
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
        Your account is banned by admin. You can't access your account. Please contact with us to unban your account. We are always ready to help you.
    </p>
	<hr>
    <div>
        <strong>Banned Reason: {{ $userStatus->reason }}</strong><br>
        <strong>Banned At: {{ date('d M, Y h:i A', strtotime($userStatus->created_at)) }}</strong>
    </div>
	<hr>
	<div class="mb-0">
        <a href="{{ route('support') }}" class="btn btn-danger btn-sm">Contact Us</a>
    </div>
</div>
@endif

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Today Posted Task Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($today_pending_posted_task == 0 && $today_running_posted_task == 0 && $today_rejected_posted_task == 0 && $today_canceled_posted_task == 0 && $today_paused_posted_task == 0 && $today_completed_posted_task == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for today posted task status. Please check back later.
                    </p>
                </div>
                @else
                <canvas id="todayPostedTaskChartjsPie"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Today Task Proof Submit Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($today_pending_task_proof_submit == 0 && $today_approved_task_proof_submit == 0 && $today_rejected_task_proof_submit == 0 && $today_reviewed_task_proof_submit == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for today task proof submit status. Please check back later.
                    </p>
                </div>
                @else
                <canvas id="todayTaskProofSubmitChartjsDoughnut"></canvas>
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
                            <h6 class="card-title mb-2">Posted Task Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $monthly_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $yearly_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $total_posted_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total - {{ Auth::user()->created_at->format('d M, Y') }}</span>
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
                            <h6 class="card-title mb-2">Task Proof Submit Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $monthly_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $yearly_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $total_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total - {{ Auth::user()->created_at->format('d M, Y') }}</span>
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
                    <h6 class="card-title">Today Worked Task Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($today_pending_worked_task == 0 && $today_approved_worked_task == 0 && $today_rejected_worked_task == 0 && $today_reviewed_worked_task == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for today worked task status. Please check back later.
                    </p>
                </div>
                @else
                <div id="todayWorkedTaskApexPie"></div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Today Report Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($today_pending_report == 0 && $today_false_report == 0 && $today_received_report == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for today report status. Please check back later.
                    </p>
                </div>
                @else
                <div id="todayReportApexDonut"></div>
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
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $monthly_worked_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $yearly_worked_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $total_worked_task }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total - {{ Auth::user()->created_at->format('d M, Y') }}</span>
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
                            <h6 class="card-title mb-2">Report Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $monthly_report }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $yearly_report }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ $total_report }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total - {{ Auth::user()->created_at->format('d M, Y') }}</span>
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
                    <h6 class="card-title">Total Task Proof Submit Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($total_task_proof_submit == 0)
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
                <canvas id="totalTaskProofSubmitChartjsLine">
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title">Total Worked Task Status</h6>
                </div>
            </div>
            <div class="card-body">
                @if ($total_worked_task == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for total worked task. Please check back later.
                    </p>
                </div>
                @else
                <div id="totalWorkedTaskApexLine"></div>
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
                            <h6 class="card-title mb-2">Total Task Proof Submit Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_pending_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_approved_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_rejected_task_proof_submit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Rejected</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ $total_reviewed_task_proof_submit }}</h3>
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
                            <h6 class="card-title mb-2">Total Posted Task Charge Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargeTotal, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargeWaiting, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-secondary">
                                        <span>Waiting</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargeCanceled, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Canceled</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargePending, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
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
                            <h6 class="card-title mb-2">Total Posted Task Charge Status</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargeWorkerPayment, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Worker Payment</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargeSitePayment, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Site Payment</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargeRefund, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>Refund</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-3">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ round($postTaskChargeHold, 2) }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-warning">
                                        <span>Hold</span>
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
    <div class="col-xl-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Last 12 Months Deposit and Withdraw Amount Status<h6></h6>
                </div>
                <p class="text-muted">This chart shows the monthly deposit and withdraw amount.</p>
            </div>
            <div class="card-body">
                @if ($total_withdraw == 0 && $total_deposit == 0)
                <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <div class="alert-heading mb-3">
                        <i data-feather="alert-circle"></i>
                        <h4>No data found!</h4>
                    </div>
                    <p class="mt-3">
                        No data found for monthly deposit and withdraw amount status. Please check back later.
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
                            <h6 class="card-title mb-2">Deposit Amount</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $monthly_deposit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $yearly_deposit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_deposit }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total - {{ Auth::user()->created_at->format('d M, Y') }}</span>
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
                            <h6 class="card-title mb-2">Withdraw Amount</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $monthly_withdraw }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $yearly_withdraw }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">{{ get_site_settings('site_currency_symbol') }} {{ $total_withdraw }}</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Total - {{ Auth::user()->created_at->format('d M, Y') }}</span>
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


<script>
    var today_posted_task_labels = ['Pending', 'Running', 'Rejected', 'Canceled', 'Paused', 'Completed'];
    var today_posted_task_series = [{{ $today_pending_posted_task }}, {{ $today_running_posted_task }}, {{ $today_rejected_posted_task }}, {{ $today_canceled_posted_task }}, {{ $today_paused_posted_task }}, {{ $today_completed_posted_task }}];

    var today_task_proof_submit_labels = ['Pending', 'Approved', 'Rejected', 'Reviewed'];
    var today_task_proof_submit_series = [{{ $today_pending_task_proof_submit }}, {{ $today_approved_task_proof_submit }}, {{ $today_rejected_task_proof_submit }}, {{ $today_reviewed_task_proof_submit }}];

    var today_worked_task_labels = ['Pending', 'Approved', 'Rejected', 'Reviewed'];
    var today_worked_task_series = [{{ $today_pending_worked_task }}, {{ $today_approved_worked_task }}, {{ $today_rejected_worked_task }}, {{ $today_reviewed_worked_task }}];

    var monthlyDepositAndWithdrawCategories = {!! json_encode(array_keys($monthlyWithdraw)) !!};
    var monthlyWithdrawSeries = {!! json_encode(array_values($monthlyWithdraw)) !!};
    var monthlyDepositeSeries = {!! json_encode(array_values($monthlyDeposite)) !!};

    var todayReportLabels = ['Pending', 'False', 'Received'];
    var todayReportSeries = [{{ $today_pending_report }}, {{ $today_false_report }}, {{ $today_received_report }}];

    const totalTaskProofSubmitChartjsLineData = @json($totalTaskProofSubmitChartjsLineData);
    const totalWorkedTaskApexLineData = @json($totalWorkedTaskApexLineData);
</script>

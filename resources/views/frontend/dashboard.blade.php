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
    <strong>Notice: </strong>{{ get_site_settings('site_notice') }}
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
	<div class="mb-0">
        <a href="{{ route('support') }}" class="btn btn-danger btn-sm">Contact Us</a>
    </div>
</div>
@endif

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Today Posted Task</h6>
                <canvas id="chartjsPie"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Today Worked Task</h6>
                <div id="apexPie"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Posted Task</h6>
                <canvas id="chartjsLine">
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Worked Task</h6>
                <div id="apexLine"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Posted Task</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Worked Task</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Deposit Amount</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Withdraw Amount</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Today - {{ date('l') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Monthly - {{ date('F') }}</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-primary">
                                        <span>Yearly - {{ date('Y') }}</span>
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
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Monthly Deposit & Withdraw Amount</h6>
                </div>
                <p class="text-muted">This chart shows the monthly deposit and withdraw amount.</p>
                <div id="monthlySalesChart"></div>
            </div>
        </div>
    </div>
</div> <!-- row -->

<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Posted Task</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Worked Task</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Deposit Amount</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-2">Total Withdraw Amount</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-info">
                                        <span>Pending</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>Approved</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <h3 class="mb-2">00</h3>
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

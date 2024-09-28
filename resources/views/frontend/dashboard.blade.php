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

<div class="example mb-5">
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25</div>
        <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25</div>
        <div class="progress-bar bg-danger" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25</div>
        <div class="progress-bar bg-warning" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25</div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">New Customers</h6>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-12 col-xl-5">
                                <h3 class="mb-2">3,897</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>+3.3%</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-7">
                                <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">New Orders</h6>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-12 col-xl-5">
                                <h3 class="mb-2">35,084</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-danger">
                                        <span>-2.8%</span>
                                        <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-7">
                                <div id="ordersChart" class="mt-md-3 mt-xl-0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Growth</h6>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-12 col-xl-5">
                                <h3 class="mb-2">89.87%</h3>
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <span>+2.8%</span>
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-7">
                                <div id="growthChart" class="mt-md-3 mt-xl-0"></div>
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
            <div class="card-body">
                <h6 class="card-title">Pie chart</h6>
                <div id="apexPie"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Line chart</h6>
                <div id="apexLine"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Pie chart</h6>
                <canvas id="chartjsPie"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title mb-0">Cloud storage</h6>
                </div>
                <div id="storageChart"></div>
                <div class="row mb-3">
                    <div class="col-6 d-flex justify-content-end">
                        <div>
                            <label class="d-flex align-items-center justify-content-end tx-10 text-uppercase fw-bolder">Total storage <span class="p-1 ms-1 rounded-circle bg-secondary"></span></label>
                            <h5 class="fw-bolder mb-0 text-end">8TB</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div>
                            <label class="d-flex align-items-center tx-10 text-uppercase fw-bolder"><span class="p-1 me-1 rounded-circle bg-primary"></span> Used storage</label>
                            <h5 class="fw-bolder mb-0">~5TB</h5>
                        </div>
                    </div>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary">Upgrade storage</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 col-xl-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Monthly sales</h6>
                </div>
                <p class="text-muted">Sales are activities related to selling or the number of goods or services sold in a given time period.</p>
                <div id="monthlySalesChart"></div>
            </div>
        </div>
    </div>
</div> <!-- row -->

<div class="row">
    <div class="col-lg-7 col-xl-8 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Projects</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="pt-0">#</th>
                                <th class="pt-0">Project Name</th>
                                <th class="pt-0">Start Date</th>
                                <th class="pt-0">Due Date</th>
                                <th class="pt-0">Status</th>
                                <th class="pt-0">Assign</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border-bottom">3</td>
                                <td class="border-bottom">NobleUI EmberJs</td>
                                <td class="border-bottom">01/05/2022</td>
                                <td class="border-bottom">10/11/2022</td>
                                <td class="border-bottom"><span class="badge bg-info">Pending</span></td>
                                <td class="border-bottom">Jensen Combs</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> <!-- row -->
@endsection

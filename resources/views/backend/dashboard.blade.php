@extends('layouts.template_master')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title">Verified User (Approved) - Last 7 days</h6>
                        </div>
                        @if (array_sum($formattedVerifiedUsersData) <= 0)
                        <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                            <div class="alert-heading mb-3">
                                <i data-feather="alert-circle"></i>
                                <h4>No data found!</h4>
                            </div>
                            <p class="mt-3">
                                No data found for verified users.
                            </p>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-12">
                                <div id="verifiedUsersChart" class="mt-md-3 mt-xl-0"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title">Posted Task (Approved) - Last 7 days</h6>
                        </div>
                        @if (array_sum($formattedPostedTasksData) <= 0)
                        <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                            <div class="alert-heading mb-3">
                                <i data-feather="alert-circle"></i>
                                <h4>No data found!</h4>
                            </div>
                            <p class="mt-3">
                                No data found for posted tasks.
                            </p>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-12">
                                <div id="postedTasksDataChart" class="mt-md-3 mt-xl-0"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title">Worked Tasks - Last 7 days</h6>
                        </div>
                        @if (array_sum($formattedWorkedTasksData) <= 0)
                        <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                            <div class="alert-heading mb-3">
                                <i data-feather="alert-circle"></i>
                                <h4>No data found!</h4>
                            </div>
                            <p class="mt-3">
                                No data found for worked tasks.
                            </p>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-12">
                                <div id="workedTasksDataChart" class="mt-md-3 mt-xl-0"></div>
                            </div>
                        </div>
                        @endif
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
                <h6 class="card-title">Last 7 days Deposit and Withdraw Summary (Approved)</h6>
                @if (array_sum($formattedDepositData->toArray()) <= 0 && array_sum($formattedWithdrawData->toArray()) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for deposit and withdraw summary.
                        </p>
                    </div>
                @else
                    <canvas id="approvedDepositAndWithdrawChartjsLine"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Last 7 days Report Status Summary</h6>
                @if (empty($formattedStatusWiseReportsDataSeries) ||
                    collect($formattedStatusWiseReportsDataSeries)->every(function ($series) {
                        return collect($series['data'])->sum() <= 0;
                    })
                )
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for report status summary.
                        </p>
                    </div>
                @else
                    <div id="reportStatusApexLine"></div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total User Status Summary</h6>
                @if ($formattedUserStatusData->sum('data') <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for user status summary.
                        </p>
                    </div>
                @else
                    <div class="flot-chart-wrapper">
                        <div class="flot-chart" id="userStatusFlotPie"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title mb-0">Currently Online User Summary</h6>
                </div>
                <div id="currentlyOnlineUserChart"></div>
                <div class="row mb-3">
                    <div class="col-6 d-flex justify-content-end">
                        <div>
                            <label class="d-flex align-items-center justify-content-end tx-10 text-uppercase fw-bolder">Total Active User<span class="p-1 ms-1 rounded-circle bg-primary"></span></label>
                            <h5 class="fw-bolder mb-0 text-end">{{ $totalActiveUserCount }}</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div>
                            <label class="d-flex align-items-center tx-10 text-uppercase fw-bolder"><span class="p-1 me-1 rounded-circle bg-success"></span> Online User</label>
                            <h5 class="fw-bolder mb-0">{{ $currentlyOnlineUserCount }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Request Summary</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="pt-0">#</th>
                                <th class="pt-0">Item</th>
                                <th class="pt-0">Status</th>
                                <th class="pt-0">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Verification</td>
                                <td><span class="badge bg-info">Pending</span></td>
                                <td>{{ $verificationRequestCount }}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Deposit</td>
                                <td><span class="badge bg-info">Pending</span></td>
                                <td>{{ $depositRequestCount }}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Withdraw</td>
                                <td><span class="badge bg-info">Pending</span></td>
                                <td>{{ $withdrawRequestCount }}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Posted Task</td>
                                <td><span class="badge bg-info">Pending</span></td>
                                <td>{{ $postTaskRequestCount }}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Worked Task</td>
                                <td><span class="badge bg-warning">Reviewed</span></td>
                                <td>{{ $proofTaskRequestCount }}</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>Report</td>
                                <td><span class="badge bg-info">Pending</span></td>
                                <td>{{ $reportRequestCount }}</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>Contact</td>
                                <td><span class="badge bg-secondary">Unread</span></td>
                                <td>{{ $contactRequestCount }}</td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>Support</td>
                                <td><span class="badge bg-secondary">Unread</span></td>
                                <td>{{ $supportsRequestCount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Deposit Summary</h6>
                @if (array_sum($formattedDepositsStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for deposit summary.
                        </p>
                    </div>
                @else
                <canvas id="totalDepositChartjsPie"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Withdraw Summary</h6>
                @if (array_sum($formattedWithdrawsStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for withdraw summary.
                        </p>
                    </div>
                @else
                <canvas id="totalWithdrawChartjsPie"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Posted Tasks Summary</h6>
                @if (array_sum($postedTasksStatusStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for posted tasks summary.
                        </p>
                    </div>
                @else
                <div id="totalPostedTasksApexRadialBar"></div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Worked Tasks Summary</h6>
                @if (array_sum($workedTasksStatusStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for worked tasks summary.
                        </p>
                    </div>
                @else
                <div id="totalWorkedTasksApexRadialBar"></div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    var lastSevenDaysCategories = @json($lastSevenDaysCategories);
    var formattedVerifiedUsersData = @json($formattedVerifiedUsersData);
    var formattedPostedTasksData = @json($formattedPostedTasksData);
    var formattedWorkedTasksData = @json($formattedWorkedTasksData);
    var formattedUserStatusData = @json($formattedUserStatusData);
    var postedTasksStatusStatuses = @json($postedTasksStatusStatuses);
    var postedTasksStatusStatusesData = @json($postedTasksStatusStatusesData);
    var workedTasksStatusStatuses = @json($workedTasksStatusStatuses);
    var workedTasksStatusStatusesData = @json($workedTasksStatusStatusesData);
    var depositsStatuses = @json($depositsStatuses);
    var formattedDepositsStatusesData = @json($formattedDepositsStatusesData);
    var withdrawsStatuses = @json($withdrawsStatuses);
    var formattedWithdrawsStatusesData = @json($formattedWithdrawsStatusesData);
    var formattedDepositData = @json($formattedDepositData);
    var formattedWithdrawData = @json($formattedWithdrawData);
    var formattedStatusWiseReportsDataSeries = @json($formattedStatusWiseReportsDataSeries);
    var totalActiveUserCount = @json($totalActiveUserCount);
    var currentlyOnlineUserCount = @json($currentlyOnlineUserCount);
</script>

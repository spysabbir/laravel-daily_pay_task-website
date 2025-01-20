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

{{-- <div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Bar chart</h6>
                <div id="apexBar"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Grouped bar chart</h6>
                <canvas id="chartjsGroupedBar"></canvas>
            </div>
        </div>
    </div>
</div> --}}

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Last 7 days Deposit and Withdraw Distribution (Approved)</h6>
                @if (array_sum($formattedDepositData->toArray()) <= 0 && array_sum($formattedWithdrawData->toArray()) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for deposit and withdraw distribution.
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
                <h6 class="card-title">Last 7 days Report Status Distribution</h6>
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
                            No data found for report status distribution.
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
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total User Status Distribution</h6>
                @if ($formattedUserStatusData->sum('data') <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for user status distribution.
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
</div>

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Deposit Distribution</h6>
                @if (array_sum($formattedDepositsStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for deposit distribution.
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
                <h6 class="card-title">Total Withdraw Distribution</h6>
                @if (array_sum($formattedWithdrawsStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for withdraw distribution.
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
                <h6 class="card-title">Total Posted Tasks Distribution</h6>
                @if (array_sum($postedTasksStatusStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for posted tasks distribution.
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
                <h6 class="card-title">Total Worked Tasks Distribution</h6>
                @if (array_sum($workedTasksStatusStatusesData) <= 0)
                    <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        <div class="alert-heading mb-3">
                            <i data-feather="alert-circle"></i>
                            <h4>No data found!</h4>
                        </div>
                        <p class="mt-3">
                            No data found for worked tasks distribution.
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
    const formattedUserStatusData = @json($formattedUserStatusData);
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
</script>

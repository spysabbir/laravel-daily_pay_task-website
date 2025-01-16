@extends('layouts.template_master')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @if ($totalVerifiedUsers == 0)
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
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title">Verified User - Last 10 days</h6>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-12 col-xl-2">
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                    <h3 class="mb-2">{{ $totalVerifiedUsers }}</h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-10">
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
                        @if ($totalPostedTasks == 0)
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
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title">Posted Task (Approved) - Last 10 days</h6>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-12 col-xl-2">
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                    <h3 class="mb-2">{{ $totalPostedTasks }}</h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-10">
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
                        @if ($totalWorkedTasks == 0)
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
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title">Worked Tasks (Approved) - Last 10 days</h6>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-12 col-xl-2">
                                <div class="d-flex align-items-baseline">
                                    <p class="text-success">
                                        <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                    </p>
                                    <h3 class="mb-2">{{ $totalWorkedTasks }}</h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-10">
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
                @if ($formattedUserStatusData->isEmpty())
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
                    <h6 class="card-title">User Status Distribution</h6>
                    <div class="flot-chart-wrapper">
                        <div class="flot-chart" id="userStatusFlotPie"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">
                <div id="totalPostedTaskChartjsPie"></div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    const formattedUserStatusData = @json($formattedUserStatusData);
    var lastTenDaysCategories = @json($lastTenDaysCategories);  // Dates
    var formattedVerifiedUsersData = @json($formattedVerifiedUsersData);  // Counts
    var formattedPostedTasksData = @json($formattedPostedTasksData);  // Counts
    var formattedWorkedTasksData = @json($formattedWorkedTasksData);  // Counts
</script>

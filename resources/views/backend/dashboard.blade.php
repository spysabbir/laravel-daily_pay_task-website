@extends('layouts.template_master')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @if (!$formattedWorkedTasksData)
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
                            <h6 class="card-title">Verified User (Approved) - Last 7 days</h6>
                        </div>
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
                        @if (!$formattedPostedTasksData)
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
                            <h6 class="card-title">Posted Task (Approved) - Last 7 days</h6>
                        </div>
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
                        @if (!$formattedWorkedTasksData)
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
                            <h6 class="card-title">Worked Tasks - Last 7 days</h6>
                        </div>
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
                @if (!$formattedUserStatusData)
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
                    <h6 class="card-title">Total User Status Distribution</h6>
                    <div class="flot-chart-wrapper">
                        <div class="flot-chart" id="userStatusFlotPie"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        
    </div>
</div>

<div class="row">
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Posted Tasks Distribution</h6>
                <div id="totalPostedTasksApexRadialBar"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Worked Tasks Distribution</h6>
                <div id="totalWorkedTasksApexRadialBar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    var lastSevenDaysCategories = @json($lastSevenDaysCategories);  // Dates
    var formattedVerifiedUsersData = @json($formattedVerifiedUsersData);  // Counts
    var formattedPostedTasksData = @json($formattedPostedTasksData);  // Counts
    var formattedWorkedTasksData = @json($formattedWorkedTasksData);  // Counts
    const formattedUserStatusData = @json($formattedUserStatusData);
    var postedTasksStatusStatuses = @json($postedTasksStatusStatuses);  // Counts
    var postedTasksStatusStatusesData = @json($postedTasksStatusStatusesData);  // Counts
    var workedTasksStatusStatuses = @json($workedTasksStatusStatuses);  // Counts
    var workedTasksStatusStatusesData = @json($workedTasksStatusStatusesData);  // Counts
</script>

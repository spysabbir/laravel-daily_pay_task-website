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
                        <h6 class="card-title mb-0">Total User</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2">{{ $totalData['totalUsers'] }}</h3>
                            <div class="d-flex align-items-baseline">
                                <p class="text-success">
                                    <span>{{ $totalData['activeUsers'] }}</span>
                                    <i data-feather="arrow-up" class="icon-sm mb-1"></i>Active
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
                        <h6 class="card-title mb-0">Totat Post Task</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2">{{ $totalData['totalPostTask'] }}</h3>
                            <div class="d-flex align-items-baseline">
                                <p class="text-danger">
                                    <span>{{ $totalData['runningPostTasks'] }}</span>
                                    <i data-feather="arrow-down" class="icon-sm mb-1"></i>Running
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
    <div class="col-12 col-xl-12 grid-margin stretch-card">
        <div class="card overflow-hidden">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-4 mb-md-3">
                    <h6 class="card-title mb-0">Revenue</h6>
                </div>
                <div class="row align-items-start">
                    <div class="col-md-7">
                        <p class="text-muted tx-13 mb-3 mb-md-0">Revenue is the income that a business has from its normal business activities, usually from the sale of goods and services to customers.</p>
                    </div>
                </div>
                <div id="revenueChart" ></div>
            </div>
        </div>
    </div>
</div> <!-- row -->

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
    <div class="col-lg-5 col-xl-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline">
                    <h6 class="card-title mb-0">Cloud storage</h6>
                </div>
                <div id="storageChart"></div>
                <div class="row mb-3">
                    <div class="col-6 d-flex justify-content-end">
                        <div>
                            <label class="d-flex align-items-center justify-content-end tx-10 text-uppercase fw-bolder">Total Deposit <span class="p-1 ms-1 rounded-circle bg-secondary"></span></label>
                            <h5 class="fw-bolder mb-0 text-end">{{ $totalData['totalDeposit'] }}</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div>
                            <label class="d-flex align-items-center tx-10 text-uppercase fw-bolder"><span class="p-1 me-1 rounded-circle bg-primary"></span> Total Withdraw</label>
                            <h5 class="fw-bolder mb-0">{{ $totalData['totalWithdraw'] }}</h5>
                        </div>
                    </div>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary">Upgrade storage</button>
                </div>
            </div>
        </div>
    </div>
</div> <!-- row -->

<div class="row">
    <div class="col-lg-5 col-xl-4 grid-margin grid-margin-xl-0 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Inbox</h6>
                </div>
                <div class="d-flex flex-column">
                    <a href="javascript:;" class="d-flex align-items-center border-bottom pb-3">
                        <div class="me-3">
                            <img src="https://via.placeholder.com/35x35" class="rounded-circle wd-35" alt="user">
                        </div>
                        <div class="w-100">
                            <div class="d-flex justify-content-between">
                                <h6 class="text-body mb-2">Leonardo Payne</h6>
                                <p class="text-muted tx-12">12.30 PM</p>
                            </div>
                            <p class="text-muted tx-13">Hey! there I'm available...</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 col-xl-8 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Recent Post Task</h6>
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

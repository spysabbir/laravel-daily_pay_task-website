@php
    $seoSetting = App\Models\SeoSetting::first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- SEO -->
    <meta name="title" content="{{ $seoSetting->title }}">
    <meta name="description" content="{{ $seoSetting->description }}">
	<meta name="author" content="{{ $seoSetting->author }}">
	<meta name="keywords" content="{{ $seoSetting->keywords }}">
    <meta name='robots' content='index,follow'>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="Website">
    <meta property="og:site_name" content="{{ $seoSetting->og_site_name }}">
    <meta property="og:url" content="{{ $seoSetting->og_url }}">
    <meta property="og:title" content="{{ $seoSetting->title }}">
    <meta property="og:description" content="{{ $seoSetting->description }}">
    <meta property="og:image" content="{{ asset('uploads/setting_photo') }}/{{ $seoSetting->image }}">
    <meta property="og:image:alt" content="{{ $seoSetting->image_alt }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter -->
    <meta property="twitter:card" content="{{ $seoSetting->twitter_card }}">
    <meta property="twitter:site" content="{{ $seoSetting->twitter_site }}">
    <meta property="twitter:title" content="{{ $seoSetting->title }}">
    <meta property="twitter:description" content="{{ $seoSetting->description }}">
    <meta property="twitter:image" content="{{ asset('uploads/setting_photo') }}/{{ $seoSetting->image }}">
    <meta property="twitter:image:alt" content="{{ $seoSetting->image_alt }}">
    <meta property="twitter:image:width" content="1200">
    <meta property="twitter:image:height" content="630">
    <!-- End SEO -->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
	<title>{{ get_site_settings('site_name') }}- @yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('uploads/setting_photo') }}/{{ get_site_settings('site_favicon') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <!-- End fonts -->

	<!-- core:css -->
	<link rel="stylesheet" href="{{ asset('template') }}/vendors/core/core.css">
	<!-- endinject -->

	<!-- Plugin css for this page -->
	<link rel="stylesheet" href="{{ asset('template') }}/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('template') }}/vendors/datatable/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="{{ asset('template') }}/vendors/datatable/css/buttons.dataTables.min.css">
	<link rel="stylesheet" href="{{ asset('template') }}/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="{{ asset('template') }}/vendors/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('template') }}/vendors/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" href="{{ asset('template') }}/vendors/lightbox2/css/lightbox.min.css"  />
	<!-- End plugin css for this page -->

	<!-- inject:css -->
	<link rel="stylesheet" href="{{ asset('template') }}/fonts/feather-font/css/iconfont.css">
	<link rel="stylesheet" href="{{ asset('template') }}/vendors/flag-icon-css/css/flag-icon.min.css">
	<!-- endinject -->

    <!-- Layout styles -->
        <link rel="stylesheet" href="{{ asset('template') }}/css/demo2/style.css">
    <!-- End layout styles -->

    <!-- toastr css -->
    <link rel="stylesheet" href="{{ asset('template') }}/vendors/toastr/toastr.css" >
    <!-- End toastr css -->
</head>
<body>
	<div class="main-wrapper">
        <!-- sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="{{ Auth::user()->user_type === 'Backend' ? route('backend.dashboard') : route('dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('uploads/setting_photo') }}/{{ get_site_settings('site_logo') }}" alt="{{ get_site_settings('site_name') }} logo">
                </a>
                <div class="sidebar-toggler not-active">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="sidebar-body">
                @include('layouts.navigation')
            </div>
        </nav>
        <!-- end sidebar -->

        <!-- main -->
        <div class="page-wrapper">

            <!-- navbar -->
            <nav class="navbar">
                <a href="#" class="sidebar-toggler">
                    <i data-feather="menu"></i>
                </a>
                <div class="navbar-content">
                    <div class="search-form">
						{{-- <form action="" method="GET">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <button type="submit" class="btn btn-dark"><i data-feather="search"></i></button>
                                </div>
                                <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
                            </div>
                        </form> --}}
					</div>
                    @if (auth()->user()->user_type === 'Frontend')
                    <div class="d-xl-flex py-xl-3">
                        <div class="badge bg-primary mx-1" id="deposit_balance_div">
                            <h5 class="">Deposit Balance: <strong class="bg-dark px-1 rounded">{{ get_site_settings('site_currency_symbol') }} {{ auth::user()->deposit_balance }}</strong></h5>
                        </div>
                        <div class="badge bg-success mx-1" id="withdraw_balance_div">
                            <h5 class="">Withdraw Balance: <strong class="bg-dark px-1 rounded">{{ get_site_settings('site_currency_symbol') }} {{ auth::user()->withdraw_balance }}</strong></h5>
                        </div>
                    </div>
                    @endif
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="mail"></i>
                            </a>
                            <div class="dropdown-menu p-0" aria-labelledby="messageDropdown">
                                @php
                                    if (Auth::user()->isFrontendUser()) {
                                        $supports = App\Models\Support::where('status', 'Unread')->where('receiver_id', Auth::user()->id)->get();
                                    } else {
                                        $supports = App\Models\Support::where('status', 'Unread')->where('receiver_id', 1)->get();
                                    }
                                @endphp
                                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                    <p>{{ $supports->count() }} New Messages</p>
                                </div>
                                <div class="p-1">
                                    @if ($supports->count() > 0)
                                        <div style="max-height: 500px; overflow-y: auto;">
                                            @forelse ($supports as $support)
                                                <a href="{{ Auth::user()->isFrontendUser() ? route('support') : route('backend.support') }}" class="dropdown-item d-flex align-items-center py-2">
                                                    <div class="me-3">
                                                        <img class="wd-30 ht-30 rounded-circle" src="{{ asset('uploads/profile_photo') }}/{{ $support->sender->profile_photo }}" alt="user">
                                                    </div>
                                                    <div class="d-flex justify-content-between flex-grow-1">
                                                        <div class="me-4">
                                                            <p>{{ $support->message }}</p>
                                                            <p class="tx-12 text-info">{{ $support->photo ? 'Attachment' : '' }}</p>
                                                        </div>
                                                        <p class="tx-12 text-muted">{{ $support->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </a>
                                            @empty
                                                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                                    <div class="flex-grow-1 me-2">
                                                        <p class="text-center">No new messages</p>
                                                    </div>
                                                </a>
                                            @endforelse
                                        </div>
                                    @else
                                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                            <div class="flex-grow-1 me-2">
                                                <p class="text-center">No new messages</p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                    <a href="{{ Auth::user()->isFrontendUser() ? route('support') : route('backend.support') }}">View all</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            @php
                                $verification = App\Models\Verification::where('status', 'Pending')->count();
                                $deposit = App\Models\Deposit::where('status', 'Pending')->count();
                                $withdraw = App\Models\Withdraw::where('status', 'Pending')->count();
                                $postTask = App\Models\PostTask::where('status', 'Pending')->count();
                                $proofTask = App\Models\ProofTask::where('status', 'Review')->count();
                                $report = App\Models\Report::where('status', 'Pending')->count();
                                $backend_notification = $verification + $deposit + $withdraw + $postTask + $proofTask + $report;
                            @endphp
                            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="bell"></i>
                                @if ((Auth::user()->isFrontendUser() && Auth::user()->unreadNotifications->count() > 0) || (!Auth::user()->isFrontendUser() && $backend_notification > 0))
                                <div class="indicator">
                                    <div class="circle"></div>
                                </div>
                                @endif
                            </a>
                            <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
                                @if (Auth::user()->isFrontendUser())
                                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                    <p class="text-info mx-2">{{ Auth::user()->unreadNotifications->count() }} New Notifications</p>
                                    <a href="{{ route('notification.read.all') }}" class="text-warning mx-2">Clear all</a>
                                </div>
                                <div class="p-1">
                                    @if (Auth::user()->unreadNotifications->count() > 0)
                                        <div style="max-height: 500px; overflow-y: auto;">
                                            @foreach (Auth::user()->unreadNotifications as $notification)
                                                <a href="{{ route('notification.read', $notification->id) }}" class="dropdown-item d-flex align-items-center py-2">
                                                    <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                                        @if (class_basename($notification->type) === 'DepositNotification' || class_basename($notification->type) === 'WithdrawNotification' || class_basename($notification->type) === 'BonusNotification')
                                                            <i class="icon-sm text-white" data-feather="dollar-sign"></i>
                                                        @else
                                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1 me-2">
                                                        <p>
                                                            <strong>{{ $notification->data['title'] }}</strong>
                                                        </p>
                                                        <p class="tx-12 text-muted">{{ $notification->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                            <div class="flex-grow-1 me-2">
                                                <p class="text-center">No new notifications</p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                    <a href="{{ route('notification') }}">View all</a>
                                </div>
                                @else
                                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                    <p class="text-info mx-2">{{ $backend_notification }} New Notifications</p>
                                </div>
                                <div class="p-1">
                                    @if ($verification > 0)
                                    <a href="{{ route('backend.verification.request') }}" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>
                                                <strong>Verification Request</strong>
                                            </p>
                                            <p class="tx-12 text-muted">{{ $verification }} Pending</p>
                                        </div>
                                    </a>
                                    @endif
                                    @if ($deposit > 0)
                                    <a href="{{ route('backend.deposit.request') }}" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>
                                                <strong>Deposit Request</strong>
                                            </p>
                                            <p class="tx-12 text-muted">{{ $deposit }} Pending</p>
                                        </div>
                                    </a>
                                    @endif
                                    @if ($withdraw > 0)
                                    <a href="{{ route('backend.withdraw.request') }}" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>
                                                <strong>Withdraw Request</strong>
                                            </p>
                                            <p class="tx-12 text-muted">{{ $withdraw }} Pending</p>
                                        </div>
                                    </a>
                                    @endif
                                    @if ($postTask > 0)
                                    <a href="{{ route('backend.posted_task_list.pending') }}" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>
                                                <strong>Post Task Request</strong>
                                            </p>
                                            <p class="tx-12 text-muted">{{ $postTask }} Pending</p>
                                        </div>
                                    </a>
                                    @endif
                                    @if ($proofTask > 0)
                                    <a href="{{ route('backend.worked_task_list.reviewed') }}" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>
                                                <strong>Proof Task Request</strong>
                                            </p>
                                            <p class="tx-12 text-muted">{{ $proofTask }} Reviewed</p>
                                        </div>
                                    </a>
                                    @endif
                                    @if ($report > 0)
                                    <a href="{{ route('backend.report_list.pending') }}" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>
                                                <strong>Report User Request</strong>
                                            </p>
                                            <p class="tx-12 text-muted">{{ $report }} Pending</p>
                                        </div>
                                    </a>
                                    @endif
                                    @if ($backend_notification === 0)
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="flex-grow-1 me-2">
                                            <p class="text-center">No new notifications</p>
                                        </div>
                                    </a>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="wd-30 ht-30 rounded-circle" src="{{ asset('uploads/profile_photo') }}/{{ Auth::user()->profile_photo }}" alt="profile">
                            </a>
                            <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                                <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                                    <div class="mb-3">
                                        <img class="wd-80 ht-80 rounded-circle" src="{{ asset('uploads/profile_photo') }}/{{ Auth::user()->profile_photo }}" alt="">
                                    </div>
                                    <div class="text-center">
                                        <p class="tx-16 fw-bolder">{{ Auth::user()->name }}</p>
                                        <p class="tx-12 text-muted">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                                <ul class="list-unstyled p-1">
                                    <li class="dropdown-item p-0">
                                        <a href="{{ Auth::user()->user_type === 'Backend' ? route('backend.profile.edit') : route('profile.edit') }}" class="text-body ms-0 d-block p-2">
                                            <i class="me-2 icon-md" data-feather="user"></i>
                                            <span>Profile Edit</span>
                                        </a>
                                    </li>
                                    <li class="dropdown-item p-0">
                                        <a href="{{ Auth::user()->user_type === 'Backend' ? route('backend.profile.setting') : route('profile.setting') }}" class="text-body ms-0 d-block p-2">
                                            <i class="me-2 icon-md" data-feather="settings"></i>
                                            <span>Profile Setting</span>
                                        </a>
                                    </li>
                                    <li class="dropdown-item p-0">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a href="{{ route('logout') }}"  onclick="event.preventDefault(); this.closest('form').submit();" class="text-body ms-0 d-block p-2">
                                                <i class="me-2 icon-md" data-feather="log-out"></i>
                                                <span>Log Out</span>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- end navbar -->

            <!-- content -->
            <div class="page-content">

                <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
                    <div>
                        <h4 class="mb-3 mb-md-0">Welcome to @yield('title')</h4>
                    </div>
                    <div class="d-flex align-items-center flex-wrap text-nowrap">
                        <div class="input-group date datepicker wd-200 me-2 mb-2 mb-md-0">
                            <span class="input-group-text input-group-addon bg-transparent border-primary">
                            <i data-feather="calendar" class=" text-primary"></i></span>
                            <input type="text" class="form-control border-primary bg-transparent" value="{{ now()->format('d F, Y') }}" readonly>
                        </div>
                    </div>
                </div>

                @yield('content')

            </div>
            <!-- end content -->

            <!-- footer -->
            <footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
                <p class="text-muted mb-1 mb-md-0">Copyright © {{ date('Y') }} <a href="{{ config('app.url') }}" target="_blank">{{ get_site_settings('site_name') }}</a>.</p>
                <p class="text-muted">Handcrafted With <i class="mb-1 text-primary ms-1 icon-sm" data-feather="heart"></i></p>
            </footer>
            <!-- end footer -->

        </div>
        <!-- end main -->
	</div>

	<!-- core:js -->
	<script src="{{ asset('template') }}/vendors/core/core.js"></script>
    <!-- end core:js -->

	<!-- Plugin js for all pages -->
    <script src="{{ asset('template') }}/vendors/chartjs/Chart.min.js"></script>
    <script src="{{ asset('template') }}/vendors/jquery.flot/jquery.flot.js"></script>
    <script src="{{ asset('template') }}/vendors/jquery.flot/jquery.flot.resize.js"></script>
    <script src="{{ asset('template') }}/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('template') }}/vendors/apexcharts/apexcharts.min.js"></script>

    <script src="{{ asset('template') }}/vendors/datatable/js/jquery.dataTables.js"></script>
    <script src="{{ asset('template') }}/vendors/datatable/js/dataTables.bootstrap5.js"></script>
    <script src="{{ asset('template') }}/vendors/datatable/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('template') }}/vendors/datatable/js/buttons.colVis.min.js"></script>
    <script src="{{ asset('template') }}/vendors/datatable/js/jszip.min.js"></script>
    <script src="{{ asset('template') }}/vendors/datatable/js/buttons.html5.min.js"></script>
    <script src="{{ asset('template') }}/vendors/datatable/js/buttons.print.min.js"></script>

	<script src="{{ asset('template') }}/vendors/select2/select2.min.js"></script>
    <script src="{{ asset('template') }}/vendors/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{ asset('template') }}/vendors/toastr/toastr.min.js"></script>

    <script src="{{ asset('template') }}/vendors/jquery-steps/jquery.steps.min.js"></script>

    <script src="{{ asset('template') }}/vendors/lightbox2/js/lightbox.min.js"></script>
	<!-- End plugin js for all pages -->

	<!-- inject:js -->
	<script src="{{ asset('template') }}/vendors/feather-icons/feather.min.js"></script>
	<script src="{{ asset('template') }}/js/template.js"></script>
	<!-- end inject:js -->

	<!-- Custom js for all pages -->
    <script src="{{ asset('template') }}/js/dashboard-dark.js"></script>
    <script src="{{ asset('template') }}/js/datepicker.js"></script>

    <script src="{{ asset('template') }}/js/data-table.js"></script>
	<script src="{{ asset('template') }}/js/select2.js"></script>

    <script src="{{ asset('template') }}/js/lightbox.js"></script>

    <script src="{{ asset('template') }}/js/chat.js"></script>

    <script src="{{ asset('template') }}/js/chartjs-dark.js"></script>
    <script src="{{ asset('template') }}/js/apexcharts-dark.js"></script>
	<!-- End custom js for all page -->

    <!-- Page wise script -->
    @yield('script')
    <!-- End page wise script -->

    <!-- Toastr -->
    <script>
        $(document).ready(function() {
            @if(Session::has('message'))
                var type = "{{ Session::get('alert-type', 'info') }}";
                switch(type){
                    case 'info':
                        toastr.info("{{ Session::get('message') }}");
                        break;
                    case 'warning':
                        toastr.warning("{{ Session::get('message') }}");
                        break;
                    case 'success':
                        toastr.success("{{ Session::get('message') }}");
                        break;
                    case 'error':
                        toastr.error("{{ Session::get('message') }}");
                        break;
                }
            @endif
        });
    </script>
    <!-- End Toastr -->

    @vite('resources/js/app.js')
</body>
</html>

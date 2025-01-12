@extends('layouts.template_master')

@section('title', 'Profile Setting')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="position-relative">
                <figure class="overflow-hidden mb-0 d-flex justify-content-center">
                    <img src="{{ asset('template/images/others/profile_cover.jpg') }}" class="rounded-top" alt="profile cover">
                </figure>
                <div class="row w-100 px-2 px-md-4 mt-n4">
                    <div class="col-xl-5 col-lg-12 my-2 d-flex">
                        <img class="wd-70 rounded-circle" id="userProfilePhotoPreview" src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" alt="profile">
                        <div>
                            <h4 class="ms-3 text-info">Name: {{ $user->name }}</h4>
                            <h5 class="ms-3 text-info">Email: {{ $user->email }}</h5>
                            <h5 class="ms-3 text-info">Joined: {{ $user->created_at->format('j F, Y  h:i:s A') }}</h5>
                            <h5 class="ms-3 text-info">Last Active: {{ date('j F, Y  h:i:s A', strtotime($user->last_login_at)) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-12 my-2 d-flex justify-content-center align-items-center flex-wrap">
                        @php
                            $statusClasses = [
                                'Active' => 'btn-primary',
                                'Inactive' => 'btn-info',
                                'Blocked' => 'btn-warning',
                            ];
                            $status = $user->status;
                            $buttonClass = $statusClasses[$status] ?? 'btn-danger';
                        @endphp
                        <button class="btn {{ $buttonClass }} btn-xs m-1 btn-icon-text">
                            Account Status: {{ $status }}
                        </button>
                        @if (Auth::user()->isFrontendUser())
                            <button class="btn btn-info btn-xs m-1 btn-icon-text">
                                Verification Status: {{ $verification->status ?? 'Not Submitted' }}
                            </button>
                            @if ($user->hasVerification('Approved'))
                                <button class="btn btn-success btn-xs m-1 btn-icon-text">
                                    Rating Given: Task: {{ $ratingGiven->count() }} | Avg: {{ round($ratingGiven->avg('rating')) ?? 0 }} <i class="fa-solid fa-star text-warning"></i>
                                </button>
                                <button class="btn btn-success btn-xs m-1 btn-icon-text">
                                    Rating Received: Task: {{ $ratingReceived->count() }} | Avg: {{ round($ratingReceived->avg('rating')) ?? 0 }} <i class="fa-solid fa-star text-warning"></i>
                                </button>
                                <button class="btn btn-warning btn-xs m-1 btn-icon-text">
                                    Report Received: {{ $reportUserCount }} Profile | {{ $reportPostTaskCount }} Post Task | {{ $reportProofTaskCount }} Proof Task
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center p-3 rounded-bottom">
                <ul class="d-flex align-items-center m-0 p-0">
                    <li class="d-flex align-items-center active">
                        <i class="me-1 icon-md text-primary" data-feather="columns"></i>
                        <span class="pt-1px  text-primary">
                            Check your profile details.
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row profile-body justify-content-center">
    <div class="col-lg-12 grid-margin">
        <div class="card rounded mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Ip Address Statuses</h6>
                <small class="text-muted">Note: Last 5 IP addresses are shown here.</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Ip</th>
                                <th>Device Type</th>
                                <th>Updated Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userDevices as $userDevice)
                            <tr>
                                <td>{{ $userDevice->ip }}</td>
                                <td>{{ $userDevice->device_type }}</td>
                                <td>{{ date('j M, Y  h:i:s A', strtotime($userDevice->updated_at)) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->isFrontendUser())
    <div class="col-lg-12 grid-margin">
        <div class="card rounded mb-3">
            <div class="card-header">
                <h6 class="card-title">Account Statuses</h6>
                <p class="text-info">
                    Note: If your account is blocked maximum {{ get_default_settings('user_max_blocked_time_for_banned') }} times, eventually your account will be permanently banned.
                </p>
                <div class="d-flex justify-content-center align-items-center flex-wrap mt-2">
                    <h4 class="text-warning mb-2 mx-2">Total Blocked: {{ $userStatuses->where('status', 'Blocked')->count() }}</h4>
                    <h4 class="text-danger mb-2 mx-2">Total Banned: {{ $userStatuses->where('status', 'Banned')->count() }}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Blocked Time</th>
                                <th>Duration</th>
                                <th>Resolved Request Time</th>
                                <th>Resolved Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($userStatuses as $status)
                            <tr>
                                <td>{{ $status->status }}</td>
                                <td>{{ $status->reason }}</td>
                                <td>{{ date('j M, Y  h:i:s A', strtotime($status->created_at)) }}</td>
                                <td>{{ $status->blocked_duration }} Hours</td>
                                <td>{{ $status->blocked_resolved_request_at ? date('j M, Y  h:i:s A', strtotime($status->blocked_resolved_request_at)) : 'Not Request' }}</td>
                                <td>{{ $status->resolved_at ? date('j M, Y  h:i:s A', strtotime($status->resolved_at)) : 'Not Resolved' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="50" class="text-center text-info">No status found!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-12 grid-margin">
        <div class="card rounded mb-3">
            <div class="card-header">
                <h6 class="card-title">Delete Account</h6>
                <h2 class="text-info">
                    Are you sure you want to delete your account?
                </h2>
            </div>
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <p class="text-light mb-3">
                        Hi User, If you delete this account, you will not be able to reopen this account and this account details and balance will be permanently deleted. Please enter the account password to confirm that you want to permanently delete this account then press the Delete Account button. Contact us if you face any problems.
                    </p>

                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Account Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="userPassword" name="account_password" placeholder="Account Password" required>
                        @error('account_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-danger text-white me-2 mb-2 mb-md-0">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

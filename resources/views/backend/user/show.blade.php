@extends('layouts.template_master')

@section('title', 'User Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Profile Details - Id: {{ $user->id }}</h3>
                <div class="text-center mb-2">
                    @if ($user->status == 'Active')
                        <span class="badge bg-success">Account Status: Active</span>
                    @elseif ($user->status == 'Inactive')
                        <span class="badge bg-info">Account Status: Inactive</span>
                    @elseif ($user->status == 'Blocked')
                        <span class="badge bg-warning">Account Status: Blocked</span>
                    @else
                        <span class="badge bg-danger">Account Status: Banned</span>
                    @endif
                </div>
                @if ($userVerification)
                <div class="text-center mb-2">
                    @if ($userVerification->status == 'Approved')
                        <span class="badge bg-success">Id Verification Status: Approved</span>
                    @elseif ($userVerification->status == 'Rejected')
                        <span class="badge bg-danger">Id Verification Status: Rejected</span>
                    @else
                        <span class="badge bg-primary">Id Verification Status: Pending</span>
                    @endif
                </div>
                @endif
                <div class="d-flex justify-content-center align-items-center flex-wrap mb-2">
                    <span class="badge bg-primary m-1">Deposit Balance: {{ get_site_settings('site_currency_symbol') }} {{ $depositBalance }}</span>
                    <span class="badge bg-primary m-1">Withdraw Balance: {{ get_site_settings('site_currency_symbol') }} {{ $withdrawBalance }}</span>
                    <span class="badge bg-warning text-dark m-1">Hold Balance: {{ get_site_settings('site_currency_symbol') }} {{ $holdBalance }}</span>
                </div>
                <div class="text-center mb-2">
                    <span class="badge bg-danger">Report Received: {{ $reportsReceived }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Profile Photo</th>
                                <th>
                                    <img src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" alt="Profile Photo" class="img-thumbnail" width="120">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Full Name</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>{{ $user->username ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>{{ $user->date_of_birth ? date('d M, Y', strtotime($user->date_of_birth)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ $user->gender ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>{{ $user->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Bio</td>
                                <td>{{ $user->bio ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Referral Code </td>
                                <td>{{ $user->referral_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Referred By</td>
                                <td>{{ $user->referred_by ? $user->referrer->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Email Verified At</td>
                                <td>{{ $user->email_verified_at ? date('d M, Y  h:i:s A', strtotime($user->email_verified_at)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Last Login At</td>
                                <td>{{ $user->last_login_at ? date('d M, Y  h:i:s A', strtotime($user->last_login_at)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $user->created_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $user->updated_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Deleted By</td>
                                <td>{{ $user->deleted_by ? $user->deletedBy->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Deleted At</td>
                                <td>{{ $user->deleted_at ? date('d M, Y  h:i:s A', strtotime($user->deleted_at)) : 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Total Deposit Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $pendingDeposit }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
                <div class="card bg-success text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $approvedDeposit }}</h4>
                        <p class="card-text">Approved</p>
                    </div>
                </div>
                <div class="card bg-danger text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $rejectedDeposit }}</h4>
                        <p class="card-text">Rejected</p>
                    </div>
                </div>
                <div class="card bg-primary text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $transferDepositBalance }}</h4>
                        <p class="card-text">Deposit Balance Transfer</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Total Withdraw Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $pendingWithdraw }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
                <div class="card bg-success text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $approvedWithdraw }}</h4>
                        <p class="card-text">Approved</p>
                    </div>
                </div>
                <div class="card bg-danger text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $rejectedWithdraw }}</h4>
                        <p class="card-text">Rejected</p>
                    </div>
                </div>
                <div class="card bg-primary text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ get_site_settings('site_currency_symbol') }} {{ $transferWithdrawBalance }}</h4>
                        <p class="card-text">Withdraw Balance Transfer</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Posted Task Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card bg-info text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $pendingPostedTask }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
                <div class="card bg-success text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $runningPostedTask }}</h4>
                        <p class="card-text">Running</p>
                    </div>
                </div>
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $rejectedPostedTask }}</h4>
                        <p class="card-text">Rejected</p>
                    </div>
                </div>
                <div class="card bg-danger text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $canceledPostedTask }}</h4>
                        <p class="card-text">Canceled</p>
                    </div>
                </div>
                <div class="card bg-primary text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $pausedPostedTask }}</h4>
                        <p class="card-text">Paused</p>
                    </div>
                </div>
                <div class="card bg-secondary text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $completedPostedTask }}</h4>
                        <p class="card-text">Completed</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Posted Task Proof Submit Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card bg-primary text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $pendingPostedTaskProofSubmit }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
                <div class="card bg-success text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $approvedPostedTaskProofSubmit }}</h4>
                        <p class="card-text">Approved</p>
                    </div>
                </div>
                <div class="card bg-danger text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $rejectedPostedTaskProofSubmit }}</h4>
                        <p class="card-text">Rejected</p>
                    </div>
                </div>
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $reviewedPostedTaskProofSubmit }}</h4>
                        <p class="card-text">Reviewed</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Worked Task Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card bg-primary text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $pendingWorkedTask }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
                <div class="card bg-success text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $approvedWorkedTask }}</h4>
                        <p class="card-text">Approved</p>
                    </div>
                </div>
                <div class="card bg-danger text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $rejectedWorkedTask }}</h4>
                        <p class="card-text">Rejected</p>
                    </div>
                </div>
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $reviewedWorkedTask }}</h4>
                        <p class="card-text">Reviewed</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Report Send Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card bg-info text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $reportsSendPending }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
                <div class="card bg-danger text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $reportsSendFalse }}</h4>
                        <p class="card-text">False</p>
                    </div>
                </div>
                <div class="card bg-success text-dark">
                    <div class="card-body">
                        <h4 class="card-title">{{ $reportsSendReceived }}</h4>
                        <p class="card-text">Received</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between flex-wrap">
                <h3 class="card-title">User Status Details</h3>
                <span>Total Blocked: {{ $userStatuses->where('status', 'Blocked')->count() }}</span>
                <span>Total Banned: {{ $userStatuses->where('status', 'Banned')->count() }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Duration</th>
                                <th>Created By</th>
                                <th>Created Time</th>
                                <th>Resolved Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($userStatuses as $userStatuse)
                            <tr>
                                <td>
                                    @if ($userStatuse->status == 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($userStatuse->status == 'Inactive')
                                        <span class="badge bg-dark">Inactive</span>
                                    @elseif ($userStatuse->status == 'Blocked')
                                        <span class="badge bg-warning">Blocked</span>
                                    @else
                                        <span class="badge bg-danger">Banned</span>
                                    @endif
                                </td>
                                <td>{{ $userStatuse->reason }}</td>
                                <td>{{ $userStatuse->blocked_duration ? $userStatuse->blocked_duration . ' hours' : 'N/A' }}</td>
                                <td>{{ $userStatuse->created_by ? $userStatuse->createdBy->name : 'N/A' }}</td>
                                <td>{{ date('j M, Y  h:i:s A', strtotime($userStatuse->created_at)) }}</td>
                                <td>{{ $userStatuse->resolved_at ? date('j M, Y  h:i:s A', strtotime($userStatuse->resolved_at)) : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="50" class="text-center text-info">No blocked status found!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between flex-wrap">
                <h3 class="card-title">User Device Details</h3>
                <span>Total Ip: {{ $userDevices->count() }}</span>
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
                                <td>{{ $userDevice->ip_address }}</td>
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
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
    });
</script>
@endsection


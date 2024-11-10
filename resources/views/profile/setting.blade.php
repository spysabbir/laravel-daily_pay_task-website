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
                <div class="d-flex justify-content-between align-items-center position-absolute top-90 w-100 px-2 px-md-4 mt-n4">
                    <div class="d-flex">
                        <img class="wd-70 rounded-circle" src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" alt="profile">
                        <div>
                            <h4 class="ms-3">{{ $user->name }}</h4>
                            <h6 class="ms-3">{{ $user->email }}</h6>
                        </div>
                    </div>
                    <div class="d-none d-md-block">
                        @php
                            $statusClasses = [
                                'Active' => 'btn-primary',
                                'Inactive' => 'btn-info',
                                'Blocked' => 'btn-warning',
                            ];
                            $status = $user->status;
                            $buttonClass = $statusClasses[$status] ?? 'btn-danger';
                        @endphp
                        <button class="btn {{ $buttonClass }} btn-icon-text">
                            Account Status: {{ $status }}
                        </button>

                        @if (Auth::user()->isFrontendUser())
                            <button class="btn btn-info btn-icon-text">
                                Verification Status: {{ $verification->status ?? 'Not Submitted' }}
                            </button>
                            @if ($user->hasVerification('Approved'))
                                <button class="btn btn-success btn-icon-text">
                                    Rating Given: {{ $ratingGiven->count() }} Task | {{ round($ratingGiven->avg('rating')) ?? 0 }} <i class="fa-solid fa-star text-warning"></i>
                                </button>
                                <button class="btn btn-success btn-icon-text">
                                    Rating Received: {{ $ratingReceived->count() }} Task | {{ round($ratingReceived->avg('rating')) ?? 0 }} <i class="fa-solid fa-star text-warning"></i>
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
                        <span class="pt-1px d-none d-md-block text-primary">
                            Update your account's profile information.
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row profile-body">
    <div class="col-xl-6 col-lg-12 grid-margin">
        <div class="card rounded">
            <div class="card-header">
                <h4 class="card-title">Update Password</h4>
                <p class="text-info">Note: The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('password.update') }}" class="forms-sample">
                    @csrf
                    @method('put')
                    <div class="mb-3">
                        <label for="update_current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="update_current_password" name="current_password" placeholder="Current Password" required>
                        @error('current_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="update_new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="update_new_password" name="password" placeholder="New Password" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="update_password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="update_password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                        @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary text-white me-2 mb-2 mb-md-0">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if (Auth::user()->isFrontendUser())
    <div class="col-xl-6 col-lg-12 grid-margin">
        <div class="card rounded mb-3">
            <div class="card-header">
                <h6 class="card-title">Blocked Statuses</h6>
                <p class="text-info">
                    Note: If your account is blocked maximum {{ get_default_settings('user_max_blocked_time') }} times, eventually your account will be permanently banned.
                </p>
            </div>
            <div class="card-body">
                <h3 class="text-danger text-center mb-2">Total Blocked: {{ $blockedStatuses->count() }}</h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Reason</th>
                                <th>Blocked Time</th>
                                <th>Duration</th>
                                <th>Resolved Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blockedStatuses as $blockedStatus)
                            <tr>
                                <td>{{ $blockedStatus->reason }}</td>
                                <td>{{ $blockedStatus->created_at->format('d M, Y h:i A') }}</td>
                                <td>{{ $blockedStatus->blocked_duration }} Hours</td>
                                <td>{{ $blockedStatus->blocked_resolved->format('d M, Y h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-info">No blocked status found!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card rounded mb-3">
            <div class="card-header">
                <h6 class="card-title">Delete Account</h6>
                <h2 class="text-info">
                    Are you sure you want to delete your account?
                </h2>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <p class="text-light mb-3">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
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

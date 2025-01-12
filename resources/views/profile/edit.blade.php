@extends('layouts.template_master')

@section('title', 'Profile Edit')

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
                            <h5 class="ms-3 text-info">Active Status:
                                @if (\Carbon\Carbon::parse($user->last_login_at)->gt(\Carbon\Carbon::now()->subMinutes(5)))
                                <span class="text-success">Online</span>
                                @else
                                <span class="text-info">{{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}</span>
                                @endif
                            </h5>
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
                            Update your account's profile information.
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row profile-body">
    <!-- left wrapper start -->
    <div class="col-xl-7 col-lg-12 mt-2 left-wrapper">
        <div class="card rounded">
            <div class="card-header">
                <h4 class="card-title"> Update Profile Information</h4>
                <span class="text-primary">Note: </span> <span class="text-muted">Fields marked with <span class="text-danger">*</span> are required.</span>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('profile.update') }}" class="forms-sample" enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="mb-3">
                        <label for="userProfilePhoto" class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" id="userProfilePhoto" name="profile_photo" accept=".jpg, .jpeg, .png">
                        <small class="text-info d-block mt-2">* Profile photo must be a valid image file (jpeg, jpg, png) and the file size must be less than 2MB.</small>
                        <span class="text-danger" id="userProfilePhotoError"></span>
                        @error('profile_photo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name <span class="text-danger">*</span> <span class="text-primary">(As per your verification document)</span></label>
                        <input type="text" class="form-control" id="fullName" name="name" value="{{ old('name', $user->name) }}" placeholder="Name" {{ $user->isFrontendUser() && $user->hasVerification('Pending') || $user->hasVerification('Approved') ? 'readonly' : '' }} required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userName" class="form-label">Username</label>
                        <input type="text" class="form-control" id="userName" name="username" value="{{ old('username', $user->username) }}" placeholder="Username">
                        <small class="text-info d-block mt-2">* Username must be unique. The username can only contain lowercase letters and numbers.</small>
                        @error('username')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userPhone" class="form-label">Phone Number</label>
                        <input type="number" class="form-control" id="userPhone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone Number">
                        <small class="text-info d-block mt-2">* The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).</small>
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userDateOfBirth" class="form-label">Date of Birth <span class="text-danger">*</span> <span class="text-primary">(As per your verification document)</span></label>
                        <input type="date" class="form-control" id="userDateOfBirth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}" {{ $user->isFrontendUser() && $user->hasVerification('Pending') || $user->hasVerification('Approved') ? 'readonly' : '' }} required>
                        @error('date_of_birth')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small class="text-danger d-block" id="userDateOfBirthError"></small>
                    </div>
                    <div class="mb-3">
                        <div>
                            <label for="userDateOfBirth" class="form-label d-block">Gender <span class="text-danger">*</span> <span class="text-primary">(As per your verification document)</span></label>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Male" name="gender" id="Male" @checked(old('gender', $user->gender) === 'Male') {{ $user->gender !== 'Male' && $user->isFrontendUser() && ($user->hasVerification('Pending') || $user->hasVerification('Approved')) ? 'disabled' : '' }} required>
                                <label class="form-check-label" for="Male">
                                    Male
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Female" name="gender" id="Female" @checked(old('gender', $user->gender) === 'Female') {{ $user->gender !== 'Female' && $user->isFrontendUser() && ($user->hasVerification('Pending') || $user->hasVerification('Approved')) ? 'disabled' : '' }} required>
                                <label class="form-check-label" for="Female">
                                    Female
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Other" name="gender" id="Other" @checked(old('gender', $user->gender) === 'Other') {{ $user->gender !== 'Other' && $user->isFrontendUser() && ($user->hasVerification('Pending') || $user->hasVerification('Approved')) ? 'disabled' : '' }} required>
                                <label class="form-check-label" for="Other">
                                    Other
                                </label>
                            </div>
                        </div>
                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userBio" class="form-label">Bio</label>
                        <textarea class="form-control" id="userBio" name="bio" rows="4" placeholder="Bio">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary text-white me-2 mb-2 mb-md-0">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- left wrapper end -->
    <!-- middle wrapper start -->
    <div class="col-xl-5 col-lg-12 mt-2 middle-wrapper">
        <div class="card rounded">
            <div class="card-header">
                <h4 class="card-title">Update Password</h4>
                <p class="text-info">Note: The password must be at least 8 characters long and contain upper- and lower-case letters, numbers, and symbols.</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('password.update') }}" class="forms-sample">
                    @csrf
                    @method('put')
                    <div class="mb-3">
                        <label for="update_current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="update_current_password" name="current_password" placeholder="Current Password" required>
                            <button type="button" class="input-group-text input-group-addon toggle-password" data-target="update_current_password">
                                <span class="icon">🔒</span>
                            </button>
                        </div>
                        @error('current_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="update_new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="update_new_password" name="password" placeholder="New Password" required>
                            <button type="button" class="input-group-text input-group-addon toggle-password" data-target="update_new_password">
                                <span class="icon">🔒</span>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="update_password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="update_password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                            <button type="button" class="input-group-text input-group-addon toggle-password" data-target="update_password_confirmation">
                                <span class="icon">🔒</span>
                            </button>
                        </div>
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
    <!-- middle wrapper end -->
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('.icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.textContent = '👁️'; // Change to "eye" icon
                } else {
                    input.type = 'password';
                    icon.textContent = '🔒'; // Change to "eye-off" icon
                }
            });
        });

        // Set the max date of birth to today
        const dateInput = document.getElementById('userDateOfBirth');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('max', today);
        dateInput.addEventListener('change', function () {
            if (this.value > today) {
                document.getElementById('userDateOfBirthError').textContent = 'Future dates are not allowed';
                this.value = '';
            } else {
                document.getElementById('userDateOfBirthError').textContent = '';
            }
        });
    });
</script>

<script>
    $(document).ready(function(){
        // Profile Image Preview
        document.getElementById('userProfilePhoto').addEventListener('change', function() {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file && allowedTypes.includes(file.type)) {
                if (file.size > maxSize) {
                    $('#userProfilePhotoError').text('File size is too large. Max size is 2MB.');
                    this.value = ''; // Clear file input
                    // Hide preview image
                    $('#userProfilePhotoPreview').hide();
                } else {
                    $('#userProfilePhotoError').text('');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#userProfilePhotoPreview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                $('#userProfilePhotoError').text('Please select a valid image file (jpeg, jpg, png).');
                this.value = ''; // Clear file input
                // Hide preview image
                $('#userProfilePhotoPreview').hide();
            }
        });
    })
</script>
@endsection

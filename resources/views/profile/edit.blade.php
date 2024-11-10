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
                <div class="d-flex justify-content-between align-items-center position-absolute top-90 w-100 px-2 px-md-4 mt-n4">
                    <div class="d-flex">
                        <img class="wd-70 rounded-circle" id="userProfilePhotoPreview" src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" alt="profile">
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
    <!-- left wrapper start -->
    <div class="d-none d-md-block col-md-5 left-wrapper">
        <div class="card rounded mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">About</h6>
            </div>
            <div class="card-body">
                <div>
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Username:</label>
                    <p class="text-muted">
                        {{ $user->username ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Phone:</label>
                    <p class="text-muted">
                        {{ $user->phone ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Date of Birth:</label>
                    <p class="text-muted">
                        {{ $user->date_of_birth ? date('j F, Y', strtotime($user->date_of_birth)) : 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Gender:</label>
                    <p class="text-muted">
                        {{ $user->gender ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Bio:</label>
                    <p class="text-muted">
                        {{ $user->bio ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Last Login:</label>
                    <p class="text-muted">
                        {{ date('j F, Y  h:i:s A', strtotime($user->last_login_at)) }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Joined:</label>
                    <p class="text-muted">
                        {{ $user->created_at->format('j F, Y  h:i:s A') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="card rounded mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Ip Address</h6>
                <small class="text-muted">Note: latest 5 IP addresses are shown here.</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Ip</th>
                                <th>Device</th>
                                <th>Updated Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userDetails as $userDetail)
                            <tr>
                                <td>{{ $userDetail->ip }}</td>
                                <td>{{ $userDetail->device }}</td>
                                <td>{{ date('j M, Y  h:i:s A', strtotime($userDetail->updated_at)) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- left wrapper end -->
    <!-- middle wrapper start -->
    <div class="col-md-7 middle-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
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
                                <label for="userProfilePhoto" class="form-label">Profile Photo <small class="text-info">* Profile photo must be a valid image file (jpeg, jpg, png) and the file size must be less than 2MB.</small></label>
                                <input type="file" class="form-control" id="userProfilePhoto" name="profile_photo" accept=".jpg, .jpeg, .png">
                                <span class="text-danger" id="userProfilePhotoError"></span>
                                @error('profile_photo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="userName" class="form-label">Full Name <span class="text-danger">*</span> <span class="text-primary">(As per your verification document)</span></label>
                                <input type="text" class="form-control" id="userName" name="name" value="{{ old('name', $user->name) }}" placeholder="Name" {{ $user->isFrontendUser() && $user->hasVerification('Pending') || $user->hasVerification('Approved') ? 'readonly' : '' }} required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="userName" class="form-label">Username <small class="text-info">* Username must be unique. The username can only contain lowercase letters and numbers.</small></label>
                                <input type="text" class="form-control" id="userName" name="username" value="{{ old('username', $user->username) }}" placeholder="Username">
                                @error('username')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="userPhone" class="form-label">Phone Number <small class="text-info">* The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).</small></label>
                                <input type="number" class="form-control" id="userPhone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone Number">
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
        </div>
    </div>
    <!-- middle wrapper end -->
</div>
@endsection

@section('script')
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

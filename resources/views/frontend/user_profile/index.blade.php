@extends('layouts.template_master')

@section('title', 'User Profile')

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
                            <h4 class="ms-3 text-dark">Name: {{ $user->name }}</h4>
                            <p class="ms-3 text-info">Active Status:
                                @if (\Carbon\Carbon::parse($user->last_login_at)->gt(\Carbon\Carbon::now()->subMinutes(5)))
                                <span class="text-success">Online</span>
                                @else
                                <span class="text-info">{{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom border-start border-end p-3">
                <div class="d-flex align-items-center">
                    <i class="me-1 icon-md text-primary" data-feather="columns"></i>
                    <span class="pt-1px d-none d-md-block text-primary">
                        Profile Information
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center mx-2" data-bs-toggle="modal" data-bs-target=".reportModal">
                        <i class="icon-md" data-feather="message-circle"></i>
                        <span class="d-none d-md-block ms-1">Report User</span>
                    </button>
                    <!-- Report Modal -->
                    <div class="modal fade reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reportModalLabel">Report User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="reportForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="User">
                                    <div class="modal-body">
                                        <div class="alert alert-warning mb-3">
                                            <strong>Notice: Report only if the user violates the community guidelines. False reporting may your account suspended.</strong>
                                            <strong class="d-block mt-2 text-info">
                                                Note: You already reported this user <span class="text-danger">{{ $reportUserCount }}</span> times.
                                            </strong>
                                        </div>
                                        <div class="mb-3">
                                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="reason" name="reason" placeholder="Reason"></textarea>
                                            <span class="text-danger error-text reason_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="photo" class="form-label">Photo <span class="text-info">* Optional</span></label>
                                            <input type="file" class="form-control" id="photo" name="photo">
                                            <span class="text-danger error-text photo_error d-none"></span>
                                            <img src="" alt="Photo" id="photoPreview" class="mt-2" style="display: none; width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('block.unblock.user', $user->id) }}" class="btn btn-{{ $blocked ? 'danger' : 'warning' }} btn-sm d-flex align-items-center">
                        <i class="icon-md" data-feather="shield"></i>
                        <span class="d-none d-md-block ms-1">
                            {{ $blocked ? 'Unblock User' : 'Block User' }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row profile-body">
    <div class="col-md-4">
        <div class="card rounded">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">About</h6>
                </div>
                <hr>
                <div>
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Username:</label>
                    <p class="text-muted">
                        {{ $user->username ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Bio:</label>
                    <p class="text-muted">
                        {{$user->bio ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Last Login:</label>
                    <p class="text-muted">
                        {{ date('j-F, Y  h:i:s A', strtotime($user->last_login_at)) ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Joined:</label>
                    <p class="text-muted">
                        {{ $user->created_at->format('j-F, Y  h:i:s A') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Posted Task Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center text-secondary mb-2">
                            <h6 class="card-title">Now Running Tasks</h6>
                            <strong>{{ $nowPostTaskRunningCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-secondary mb-2">
                            <h6 class="card-title">Total Posted Approved Tasks</h6>
                            <strong>{{ $totalPostTaskApprovedCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Total Posted Tasks Proof</h6>
                            <strong>{{ $totalPastedTaskProofCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-info mb-2">
                            <h6 class="card-title">Total Pending Tasks Proof</h6>
                            <strong>( {{ $totalPastedTaskProofCount != 0 ? round(($totalPendingTasksProofCount / $totalPastedTaskProofCount) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalPendingTasksProofCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-success mb-2">
                            <h6 class="card-title">Total Approved Tasks Proof</h6>
                            <strong>( {{ $totalPastedTaskProofCount != 0 ? round(($totalApprovedTasksProofCount / $totalPastedTaskProofCount) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalApprovedTasksProofCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-danger mb-2">
                            <h6 class="card-title">Total Rejected Tasks Proof</h6>
                            <strong>( {{ $totalPastedTaskProofCount != 0 ? round(($totalRejectedTasksProofCount / $totalPastedTaskProofCount) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalRejectedTasksProofCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-warning mb-2">
                            <h6 class="card-title">Total Reviewed Tasks Proof</h6>
                            <strong>( {{ $totalPastedTaskProofCount != 0 ? round(($totalReviewedTasksProofCount / $totalPastedTaskProofCount) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalReviewedTasksProofCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-primary mb-2">
                            <h6 class="card-title">Pending Reviewed Tasks Proof</h6>
                            <strong>( {{ $totalPastedTaskProofCount != 0 ? round(($nowReviewedTasksProofCount / $totalPastedTaskProofCount) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $nowReviewedTasksProofCount }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Worked Task Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Total Worked Tasks</h6>
                            <span class="text-white">{{ $totalWorkedTask }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-info mb-2">
                            <h6 class="card-title">Total Pending  Tasks</h6>
                            <strong>( {{ $totalWorkedTask != 0 ? round(($totalPendingWorkedTask / $totalWorkedTask) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalPendingWorkedTask }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-success mb-2">
                            <h6 class="card-title">Total Approved Tasks</h6>
                            <strong>( {{ $totalWorkedTask != 0 ? round(($totalApprovedWorkedTask / $totalWorkedTask) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalApprovedWorkedTask }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-danger mb-2">
                            <h6 class="card-title">Total Rejected Tasks</h6>
                            <strong>( {{ $totalWorkedTask != 0 ? round(($totalRejectedWorkedTask / $totalWorkedTask) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalRejectedWorkedTask }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-warning mb-2">
                            <h6 class="card-title">Total Reviewed Tasks</h6>
                            <strong>( {{ $totalWorkedTask != 0 ? round(($totalReviewedWorkedTask / $totalWorkedTask) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $totalReviewedWorkedTask }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-primary mb-2">
                            <h6 class="card-title">Pending Reviewed Tasks</h6>
                            <strong>( {{ $totalWorkedTask != 0 ? round(($nowReviewedWorkedTask / $totalWorkedTask) * 100, 2) : 0 }} % )</strong>
                            <strong>{{ $nowReviewedWorkedTask }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Photo Preview
        $(document).on('change', '#photo', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#photoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Report User
        $('#reportForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('report.send', $user->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.'+prefix+'_error').text(val[0]);
                        });
                    } else {
                        $('.reportModal').modal('hide');
                        $('#reportForm')[0].reset();
                        toastr.success('User reported successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>
@endsection

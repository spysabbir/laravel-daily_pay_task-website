<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Verification Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>User ID</td>
                                <td>{{ $verification->user->id }}</td>
                            </tr>
                            <tr>
                                <td>Full Name</td>
                                <td>{{ $verification->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $verification->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>{{ $verification->user->date_of_birth ? date('d M, Y', strtotime($verification->user->date_of_birth)) : 'Not Found' }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ $verification->user->gender ?? 'Not Found' }}</td>
                            </tr>
                            <tr>
                                <td>Id Type</td>
                                <td>{{ $verification->id_type }}</td>
                            </tr>
                            <tr>
                                <td>ID Number</td>
                                <td>{{ $verification->id_number }}</td>
                            </tr>
                            <tr>
                                <td>Submitted Date</td>
                                <td>{{ $verification->created_at->format('d M, Y h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>
                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"></li>
                        </ol>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <a href="{{ asset('uploads/verification_photo') }}/{{ $verification->id_front_image  }}" data-lightbox="gallery" data-title="Id Front Image">
                                    <img src="{{ asset('uploads/verification_photo') }}/{{ $verification->id_front_image }}" style="max-height: 400px;" class="d-block w-100" alt="Id Front Image">
                                </a>
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 class="mb-2"><strong class="badge bg-dark">Id Front Image</strong></h5>
                                    <strong><a href="{{ asset('uploads/verification_photo') }}/{{ $verification->id_front_image }}" target="_blank">View Full Image</a></strong>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <a href="{{ asset('uploads/verification_photo') }}/{{ $verification->id_with_face_image  }}" data-lightbox="gallery" data-title="Id With Face Image">
                                    <img src="{{ asset('uploads/verification_photo') }}/{{ $verification->id_with_face_image }}" style="max-height: 400px;" class="d-block w-100" alt="Id With Face Image">
                                </a>
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 class="mb-2"><strong class="badge bg-dark">Id With Face Image</strong></h5>
                                    <strong><a href="{{ asset('uploads/verification_photo') }}/{{ $verification->id_with_face_image }}" target="_blank">View Full Image</a></strong>
                                </div>
                            </div>
                        </div>
                        <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-primary" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </a>
                        <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-primary" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @if ($verification->status == 'Approved')
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Approved!</h4>
                <p>This verification request has been approved by <strong>{{ $verification->approvedBy->name }}</strong> at <strong>{{ date('d M, Y  h:i:s A', strtotime($verification->approved_at)) }}</strong></p>
            </div>
        @else
        <div class="mb-3">
            @if ($verification->rejected_by)
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">Previously Rejected!</h4>
                    <p>This verification request has been rejected by <strong>{{ $verification->rejectedBy->name }}</strong> at <strong>{{ date('d M, Y  h:i:s A', strtotime($verification->rejected_at)) }}</strong></p>
                    <p><strong>Rejected Reason:</strong> {{ $verification->rejected_reason }}</p>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">Pending!</h4>
                    <p>This verification request is pending for approval.</p>
                </div>
            @endif
        </div>
        @can('verification.request.check')
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">
                    Same IP Users
                </h4>
            </div>
            <div class="card-body">
                @forelse ($sameIpUsers as $sameIpUser)
                    <a href="{{ route('backend.user.show', encrypt($sameIpUser->id)) }}" class="text-danger" target="_blank">
                        {{ $sameIpUser->id }} - {{ $sameIpUser->name }}
                    </a> <br>
                @empty
                    <p class="text-success">No same IP users found!</p>
                @endforelse
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Change Status
                </h4>
            </div>
            <div class="card-body">
                <form class="forms-sample" id="editForm">
                    @csrf
                    <input type="hidden" id="verification_id" value="{{ $verification->id }}">
                    <div class="mb-3">
                        <label for="verification_status" class="form-label">Verification Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="verification_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3" id="verification_rejected_reason_div" style="display: none;">
                        <label for="verification_rejected_reason" class="form-label">Rejected Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="verification_rejected_reason" name="rejected_reason" rows="4" placeholder="Rejected Reason">Submitted information not matching with your profile information. Please check and try again.</textarea>
                        <span class="text-danger error-text update_rejected_reason_error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
        @endcan
        @endif
    </div>
</div>

<script>
    // verification_rejected_reason_div hide/show
    $(document).on('change', '#verification_status', function () {
        var status = $(this).val();
        if (status === 'Rejected') {
            $('#verification_rejected_reason_div').show();
        } else {
            $('#verification_rejected_reason_div').hide();
        }
    });
</script>

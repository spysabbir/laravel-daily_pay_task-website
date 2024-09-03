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
                                <td>Full Name</td>
                                <td>{{ $verification->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>{{ $verification->user->date_of_birth }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ $verification->user->gender }}</td>
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
                                <td>Id Front Image</td>
                                <td>
                                    <img src="{{ asset('uploads/verification_photo') }}/{{ $verification->id_front_image }}" alt="Id Front Image" style="width: 120px; height: 120px">
                                    <a class="mx-2" href="{{ asset('uploads/verification_photo') }}/{{ $verification->id_front_image }}" target="_blank">View Full Image</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Id With Face Image</td>
                                <td>
                                    <img src="{{ asset('uploads/verification_photo') }}/{{ $verification->id_with_face_image }}" alt="Id With Face Image" style="width: 120px; height: 120px">
                                    <a class="mx-2" href="{{ asset('uploads/verification_photo') }}/{{ $verification->id_with_face_image }}" target="_blank">View Full Image</a>                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @if ($verification->status == 'Approved')
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Approved!</h4>
                <p>This verification request has been approved by <strong>{{ $verification->approvedBy->name }}</strong> at <strong>{{ date('F j, Y  H:i:s A', strtotime($verification->approved_at)) }}</strong></p>
            </div>
        @else
        <div class="mb-3">
            @if ($verification->rejected_by)
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">Previously Rejected!</h4>
                    <p>This verification request has been rejected by <strong>{{ $verification->rejectedBy->name }}</strong> at <strong>{{ date('F j, Y  H:i:s A', strtotime($verification->rejected_at)) }}</strong></p>
                    <p><strong>Rejected Reason:</strong> {{ $verification->rejected_reason }}</p>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">Pending!</h4>
                    <p>This verification request is pending for approval.</p>
                </div>
            @endif
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
                        <label for="verification_status" class="form-label">Verification Status</label>
                        <select class="form-select" id="verification_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3" id="verification_rejected_reason_div" style="display: none;">
                        <label for="verification_rejected_reason" class="form-label">Rejected Reason</label>
                        <textarea class="form-control" id="verification_rejected_reason" name="rejected_reason" rows="4" placeholder="Rejected Reason"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
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

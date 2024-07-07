<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">NID Verification Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Full Name</td>
                                <td>{{ $nidVerification->user->name }}</td>
                            </tr>
                            <tr>
                                <td>NID Number</td>
                                <td>{{ $nidVerification->nid_number }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>{{ $nidVerification->nid_date_of_birth }}</td>
                            </tr>
                            <tr>
                                <td>Nid Front Image</td>
                                <td>
                                    <img src="{{ asset('uploads/nid_verification_photo') }}/{{ $nidVerification->nid_front_image }}" alt="Nid Front Image" style="width: 120px; height: 120px">
                                    <a class="mx-2" href="{{ asset('uploads/nid_verification_photo') }}/{{ $nidVerification->nid_front_image }}" target="_blank">View Full Image</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Nid With Face Image</td>
                                <td>
                                    <img src="{{ asset('uploads/nid_verification_photo') }}/{{ $nidVerification->nid_with_face_image }}" alt="Nid With Face Image" style="width: 120px; height: 120px">
                                    <a class="mx-2" href="{{ asset('uploads/nid_verification_photo') }}/{{ $nidVerification->nid_with_face_image }}" target="_blank">View Full Image</a>                                </td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td>{{ $nidVerification->remarks ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Rejected By</td>
                                <td>{{ $nidVerification->rejectedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Rejected At</td>
                                <td>{{ $nidVerification->rejected_at ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Change Status
                </h4>
            </div>
            <div class="card-body">
                <form class="forms-sample" id="editForm">
                    @csrf
                    <input type="hidden" id="verification_id" value="{{ $nidVerification->id }}">
                    <div class="mb-3">
                        <label for="verification_status" class="form-label">Verification Status</label>
                        <select class="form-select" id="verification_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="verification_remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="verification_remarks" name="remarks" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </form>
            </div>
        </div>
    </div>
</div>

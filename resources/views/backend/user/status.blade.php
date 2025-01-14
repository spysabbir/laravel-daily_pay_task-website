<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Status - {{ $user->status }}</h3>
                <h5 class="text-warning">Total Blocked: {{ $userStatuses->where('status', 'Blocked')->count() ?? 0 }}</h5>
                <h5 class="text-danger">Total Banned: {{ $userStatuses->where('status', 'Banned')->count() ?? 0 }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Blocked Duration</th>
                                <th>Resolved Date</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($userStatuses as $status)
                                <tr>
                                    <td>
                                        @if ($status->status == 'Active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif ($status->status == 'Blocked')
                                            <span class="badge bg-warning">Blocked</span>
                                        @elseif ($status->status == 'Banned')
                                            <span class="badge bg-danger">Banned</span>
                                        @endif
                                    </td>
                                    <td>{{ $status->reason }}</td>
                                    <td>{{ $status->blocked_duration ? $status->blocked_duration . ' hours' : 'N/A' }}</td>
                                    <td>{{ $status->resolved_at ? date('d M, Y h:i:s A', strtotime($status->resolved_at)) : 'N/A' }}</td>
                                    <td>{{ $status->createdBy->name }}</td>
                                    <td>{{ date('d M, Y h:i:s A', strtotime($status->created_at)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="50" class="text-center">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="mb-3 text-center">
            <div class="alert alert-warning" role="alert">
                <strong>Deposit Balance:</strong> {{ $depositBalance }}<br>
                <strong>Withdraw Balance:</strong> {{ $withdrawBalance }}<br>
                <strong>Hold Balance:</strong> {{ $holdBalance }}<br>
                <strong>Deposit Requests - Pending:</strong> {{ $depositRequests }}<br>
                <strong>Withdraw Requests - Pending:</strong> {{ $withdrawRequests }}<br>
                <strong>Posted Tasks Requests - Pending:</strong> {{ $postedTasksRequests }}<br>
                <strong>Posted Tasks Proof Submit Requests - Pending & Reviewed:</strong> {{ $postedTasksProofSubmitRequests }}<br>
                <strong>Worked Tasks Requests - Reviewed:</strong> {{ $workedTasksRequests }}<br>
                <strong>Report Requests - Pending:</strong> {{ $reportRequestsPending }}<br>
            </div>
        </div>
        <form class="forms-sample" id="statusForm">
            @csrf
            <input type="hidden" id="user_id" value="{{ $user->id }}">
            <div class="mb-3">
                <label for="user_status" class="form-label">User Status <span class="text-danger">*</span></label>
                <select class="form-select" id="user_status" name="status">
                    <option value="">-- Select Status --</option>
                    @if ($user->status != 'Active')
                        <option value="Active">Active</option>
                    @endif
                    @if ($user->status != 'Blocked')
                        <option value="Blocked">Blocked</option>
                    @endif
                    @if ($user->status != 'Banned')
                        <option value="Banned">Banned</option>
                    @endif
                </select>
                <span class="text-danger error-text update_status_error"></span>
            </div>
            <div class="mb-3" id="blocked_duration_div" style="display: none;">
                <label for="blocked_duration" class="form-label">Blocked Duration (in hours)</label>
                <input type="number" class="form-control" id="blocked_duration" name="blocked_duration" placeholder="Enter blocked duration">
                <span class="text-danger error-text update_blocked_duration_error"></span>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Write your reason"></textarea>
                <span class="text-danger error-text update_reason_error"></span>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#user_status').change(function() {
            var status = $(this).val();
            if (status == 'Blocked') {
                $('#blocked_duration_div').show();
            } else {
                $('#blocked_duration_div').hide();
            }
        });
    });
</script>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Status - {{ $user->status }}</h3>
                <h4 class="text-primary">Total Blocked: {{ $userStatuses->where('status', 'Blocked')->count() ?? 0 }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-info table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Blocked Duration</th>
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
                                    <td>{{ $status->blocked_duration ? date('d-M-Y h:i A', strtotime($status->blocked_duration)) : 'N/A' }}</td>
                                    <td>{{ $status->createdBy->name }}</td>
                                    <td>{{ date('d-M-Y h:i A', strtotime($status->created_at)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <form class="forms-sample" id="statusForm">
            @csrf
            <input type="hidden" id="user_id" value="{{ $user->id }}">
            <div class="mb-3">
                <div class="row">
                    <div class="col-lg-6">
                        <label for="user_status" class="form-label">User Status</label>
                        <select class="form-select" id="user_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Active">Active</option>
                            <option value="Blocked">Blocked</option>
                            <option value="Banned">Banned</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="col-lg-6" id="blocked_duration_div" style="display: none;">
                        <label for="blocked_duration" class="form-label">Blocked Duration</label>
                        <input type="datetime-local" class="form-control" id="blocked_duration" name="blocked_duration">
                        <span class="text-danger error-text update_blocked_duration_error"></span>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
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

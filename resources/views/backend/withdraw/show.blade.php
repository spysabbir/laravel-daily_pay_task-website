<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Withdraw Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>User ID</td>
                                <td>{{ $withdraw->user->id }}</td>
                            </tr>
                            <tr>
                                <td>Full Name</td>
                                <td>{{ $withdraw->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $withdraw->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td>{{ $withdraw->type }}</td>
                            </tr>
                            <tr>
                                <td>Withdraw Method</td>
                                <td>{{ $withdraw->method }}</td>
                            </tr>
                            <tr>
                                <td>Withdraw Number</td>
                                <td>{{ $withdraw->number }}</td>
                            </tr>
                            <tr>
                                <td>Withdraw Amount</td>
                                <td>
                                    <span class="badge bg-primary">{{ get_site_settings('site_currency_symbol') . ' ' .$withdraw->amount }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Payable Amount</td>
                                <td>
                                    <span class="badge bg-primary">{{ get_site_settings('site_currency_symbol') . ' ' .$withdraw->payable_amount }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Submitted Date</td>
                                <td>{{ $withdraw->created_at->format('d M Y h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @if ($withdraw->status == 'Approved')
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Approved!</h4>
                <p>This withdraw request has been approved by <strong>{{ $withdraw->approvedBy->name }}</strong> at <strong>{{ date('d M, Y  h:i:s A', strtotime($withdraw->approved_at)) }}</strong></p>
            </div>
        @elseif ($withdraw->status == 'Rejected')
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Rejected!</h4>
                <p>This withdraw request has been rejected by <strong>{{ $withdraw->rejectedBy->name }}</strong> at <strong>{{ date('d M, Y  h:i:s A', strtotime($withdraw->rejected_at)) }}</strong></p>
                <p><strong>Rejected Reason:</strong> {{ $withdraw->rejected_reason }}</p>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Pending!</h4>
                <p>This withdraw request is pending for approval.</p>
            </div>
            @can('withdraw.request.check')
            <div class="alert alert-warning mb-3">
                <strong>Warning!</strong> Till now <span class="badge bg-{{ $reportsPending > 0 ? 'danger' : 'primary' }}">{{ $reportsPending }}</span> reports are pending this user. Check <a href="{{ route('backend.report.pending') }}" class="text-primary" target="_blank">report</a> panel and take action then approved this withdraw request.
            </div>
            <div class="alert alert-warning mb-3">
                <strong>Warning!</strong> Same withdraw number users list:
                <br>
                @forelse ($sameNumberUserIds as $user)
                    <a href="{{ route('backend.user.show', encrypt($user->id)) }}" class="badge bg-danger" target="_blank">Id: {{ $user->id }} - Name: {{ $user->name }}</a> <br>
                @empty
                    <span class="badge bg-primary">No user found</span>
                @endforelse
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
                        <input type="hidden" id="withdraw_id" value="{{ $withdraw->id }}">
                        <div class="mb-3">
                            <label for="withdraw_status" class="form-label">Withdraw Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="withdraw_status" name="status">
                                <option value="">-- Select Status --</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <span class="text-danger error-text update_status_error"></span>
                        </div>
                        <div class="mb-3" id="rejected_reason_div" style="display: none;">
                            <label for="withdraw_rejected_reason" class="form-label">Rejected Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="withdraw_rejected_reason" name="rejected_reason" rows="4" placeholder="Rejected Reason"></textarea>
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
    $(document).ready(function() {
        $('#withdraw_status').change(function() {
            var status = $(this).val();
            if (status == 'Rejected') {
                $('#rejected_reason_div').show();
            } else {
                $('#rejected_reason_div').hide();
            }
        });
    });
</script>


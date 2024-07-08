<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Deposit Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Full Name</td>
                                <td>{{ $deposit->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Deposit Amount</td>
                                <td>{{ $deposit->amount }}</td>
                            </tr>
                            <tr>
                                <td>Deposit Method</td>
                                <td>{{ $deposit->method }}</td>
                            </tr>
                            <tr>
                                <td>Deposit Number</td>
                                <td>{{ $deposit->number }}</td>
                            </tr>
                            <tr>
                                <td>Transaction ID</td>
                                <td>{{ $deposit->transaction_id }}</td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td>{{ $deposit->remarks ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $deposit->created_at }}</td>
                            </tr>
                            <tr>
                                <td>Rejected By</td>
                                <td>{{ $deposit->rejectedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Rejected At</td>
                                <td>{{ $deposit->rejected_at ?? 'N/A' }}</td>
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
                    <input type="hidden" id="deposit_id" value="{{ $deposit->id }}">
                    <div class="mb-3">
                        <label for="deposit_status" class="form-label">Deposit Status</label>
                        <select class="form-select" id="deposit_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="deposit_remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="deposit_remarks" name="remarks" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

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
                                <td>Full Name</td>
                                <td>{{ $withdraw->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Withdraw Amount</td>
                                <td>{{ $withdraw->amount }}</td>
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
                                <td>Payable Amount</td>
                                <td>{{ $withdraw->payable_amount }}</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $withdraw->created_at }}</td>
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
                    <input type="hidden" id="withdraw_id" value="{{ $withdraw->id }}">
                    <div class="mb-3">
                        <label for="withdraw_status" class="form-label">Withdraw Status</label>
                        <select class="form-select" id="withdraw_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="withdraw_remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="withdraw_remarks" name="remarks" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

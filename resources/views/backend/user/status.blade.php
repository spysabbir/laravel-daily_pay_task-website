{{-- <form class="forms-sample" id="statusForm">
    @csrf
    <input type="hidden" id="user_id">
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
            <div class="col-lg-6">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="datetime-local" class="form-control" id="expiry_date" name="expiry_date" placeholder="Expiry Date">
                <span class="text-danger error-text update_expiry_date_error"></span>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label for="reason" class="form-label">Reason</label>
        <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Write your reason"></textarea>
        <span class="text-danger error-text update_reason_error"></span>
    </div>
</form> --}}

{{ $user }}

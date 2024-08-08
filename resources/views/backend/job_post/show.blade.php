<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Job Post Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>User Name</td>
                                <td>{{ $jobPost->user->name }}</td>
                            </tr>
                            <tr>
                                <td>User Email</td>
                                <td>{{ $jobPost->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>{{ $jobPost->category->name }}</td>
                            </tr>
                            <tr>
                                <td>Sub Category</td>
                                <td>{{ $jobPost->subCategory->name }}</td>
                            </tr>
                            <tr>
                                <td>Child Category</td>
                                <td>{{ $jobPost->child_category_id ? $jobPost->childCategory->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Need Worker</td>
                                <td>{{ $jobPost->need_worker }}</td>
                            </tr>
                            <tr>
                                <td>Worker Charge</td>
                                <td>{{ $jobPost->worker_charge }}</td>
                            </tr>
                            <tr>
                                <td>Extra Screenshots</td>
                                <td>{{ $jobPost->extra_screenshots }}</td>
                            </tr>
                            <tr>
                                <td>Boosted Time</td>
                                <td>{{ $jobPost->job_boosted_time }}</td>
                            </tr>
                            <tr>
                                <td>Running Day</td>
                                <td>{{ $jobPost->job_running_day }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Change Status
                </h4>
            </div>
            <div class="card-body">
                <form class="forms-sample" id="editForm">
                    @csrf
                    <input type="hidden" id="job_post_id" value="{{ $jobPost->id }}">
                    <div class="mb-3">
                        <label for="job_title" class="form-label">Job Title</label>
                        <input type="text" class="form-control" id="job_title" value="{{ $jobPost->title }}" name="title">
                        <span class="text-danger error-text update_title_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="job_description" class="form-label">Job Description</label>
                        <textarea class="form-control" id="job_description" name="description" rows="4">{{ $jobPost->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="job_required_proof" class="form-label">Job Required Proof</label>
                        <textarea class="form-control" id="job_required_proof" name="required_proof" rows="4">{{ $jobPost->required_proof }}</textarea>
                    </div>
                    <div class="mb-3 ">
                        <label for="job_post_status" class="form-label">Job Post Status</label>
                        <select class="form-select" id="job_post_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Running">Running</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3 d-none" id="rejection_reason_div">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"></textarea>
                        <span class="text-danger error-text update_rejection_reason_error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

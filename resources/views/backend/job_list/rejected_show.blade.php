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
                                <td>{{ $jobPost->worker_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                            </tr>
                            <tr>
                                <td>Extra Screenshots</td>
                                <td>{{ $jobPost->extra_screenshots }}</td>
                            </tr>
                            <tr>
                                <td>Boosted Time</td>
                                <td>
                                    @if($jobPost->boosted_time < 60)
                                        {{ $jobPost->boosted_time }} minute{{ $jobPost->boosted_time > 1 ? 's' : '' }}
                                    @elseif($jobPost->boosted_time >= 60)
                                        {{ round($jobPost->boosted_time / 60, 1) }} hour{{ round($jobPost->boosted_time / 60, 1) > 1 ? 's' : '' }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Running Day</td>
                                <td>{{ $jobPost->running_day }} Days</td>
                            </tr>
                            <tr>
                                <td>Job Cost</td>
                                <td>{{ $jobPost->charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                            </tr>
                            <tr>
                                <td>Site Cost <span class="text-info">( {{ get_default_settings('job_posting_charge_percentage') }} % )</span></td>
                                <td>{{ $jobPost->site_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                            </tr>
                            <tr>
                                <td>Total Cost</td>
                                <td>{{ $jobPost->total_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $jobPost->created_at->format('d-m-Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $jobPost->updated_at->format('d-m-Y h:i:s A') }}</td>
                            </tr>
                            @if ($jobPost->rejected_at)
                            <tr class="text-warning">
                                <td>Rejected By</td>
                                <td>{{ $jobPost->rejectedBy->name }}</td>
                            </tr>
                            <tr class="text-warning">
                                <td>Rejected At</td>
                                <td>{{ date('d-m-Y h:i:s A', strtotime($jobPost->rejected_at)) }}</td>
                            </tr>
                            <input type="hidden" id="set_rejection_reason" value="{{ $jobPost->rejection_reason }}">
                            @endif
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
                    @if ($jobPost->thumbnail)
                    <div class="mb-3">
                        <label for="job_thumbnail" class="form-label">Job Thumbnail <span class="text-danger">* Required</span></label>
                        <img src="{{ asset('uploads/job_thumbnail_photo') }}/{{ $jobPost->thumbnail }}" alt="Job Thumbnail" class="img-fluid">
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="job_title" class="form-label">Job Title <span class="text-danger">* Required</span></label>
                        <input type="text" class="form-control" id="job_title" value="{{ $jobPost->title }}" name="title">
                        <span class="text-danger error-text update_title_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="job_description" class="form-label">Job Description <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="job_description" name="description" rows="4">{{ $jobPost->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="job_required_proof" class="form-label">Job Required Proof <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="job_required_proof" name="required_proof" rows="4">{{ $jobPost->required_proof }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="job_additional_note" class="form-label">Job Additional Note <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="job_additional_note" name="additional_note" rows="4">{{ $jobPost->additional_note }}</textarea>
                    </div>
                    <div class="mb-3 ">
                        <label for="job_post_status" class="form-label">Job Post Status <span class="text-danger">* Required</span></label>
                        <select class="form-select" id="job_post_status" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="Running">Running</option>
                            <option value="Rejected" selected>Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3" id="rejection_reason_div">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" placeholder="Write the reason for rejection">{{ $jobPost->rejection_reason }}</textarea>
                        <span class="text-danger error-text update_rejection_reason_error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

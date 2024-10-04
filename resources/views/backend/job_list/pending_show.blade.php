<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Job Post Details - Id: {{ $jobPost->id }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>User Id</td>
                                <td>{{ $jobPost->user->id }}</td>
                            </tr>
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
                                <td>Each Worker Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $jobPost->worker_charge }}</span></td>
                            </tr>
                            <tr>
                                <td>
                                    Screenshots
                                </td>
                                <td>
                                    Free: 1 + Extra: {{ $jobPost->extra_screenshots }} = Total: {{ 1 + $jobPost->extra_screenshots }} Screenshot{{ $jobPost->extra_screenshots + 1 > 1 ? 's' : '' }} <br>
                                    <span class="text-primary">( Charge: {{ $jobPost->extra_screenshots }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('job_posting_additional_screenshot_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->extra_screenshots * get_default_settings('job_posting_additional_screenshot_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Boosted Time
                                </td>
                                <td>
                                    @if($jobPost->boosted_time < 60)
                                        {{ $jobPost->boosted_time }} Minute{{ $jobPost->boosted_time > 1 ? 's' : '' }} <br>
                                    @elseif($jobPost->boosted_time >= 60)
                                        {{ round($jobPost->boosted_time / 60, 1) }} Hour{{ round($jobPost->boosted_time / 60, 1) > 1 ? 's' : '' }} <br>
                                    @endif
                                    <span class="text-primary">( Charge: {{ $jobPost->boosted_time }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('job_posting_boosted_time_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->boosted_time / 15 * get_default_settings('job_posting_boosted_time_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Running Day
                                </td>
                                <td>{{ $jobPost->running_day }} Days</td>
                            </tr>
                            <tr>
                                <td>Job Charge</td>
                                <td>
                                    <span class="text-primary">( 50 * {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->worker_charge }} ) + {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->extra_screenshots * get_default_settings('job_posting_additional_screenshot_charge') }} + {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->boosted_time / 15 * get_default_settings('job_posting_boosted_time_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->charge }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Site Charge
                                </td>
                                <td>
                                    <span class="text-primary">{{ get_site_settings('site_currency_symbol') }} {{ $jobPost->charge }} * {{ get_default_settings('job_posting_charge_percentage') }} % = {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->site_charge }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Total Charge</td>
                                <td>
                                    <span class="text-primary">{{ get_site_settings('site_currency_symbol') }} {{ $jobPost->charge }} + {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->site_charge }} = {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->total_charge }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $jobPost->created_at->format('d-m-Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $jobPost->updated_at->format('d-m-Y h:i:s A') }}</td>
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
                @if ($jobPost->rejected_at)
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Warning!</strong> This job post has been previously rejected. <br>
                    <span>Rejected By: {{ $jobPost->rejectedBy->name }}</span> <br>
                    <span>Rejected At: {{ date('d-m-Y h:i:s A', strtotime($jobPost->rejected_at)) }}</span> <br>
                    <span>Rejection Reason: {{ $jobPost->rejection_reason }}</span> <br>
                </div>
                @endif
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
                            <option value="Rejected">Rejected</option>
                        </select>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div class="mb-3 d-none" id="rejection_reason_div">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" placeholder="Write the reason for rejection"></textarea>
                        <span class="text-danger error-text update_rejection_reason_error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

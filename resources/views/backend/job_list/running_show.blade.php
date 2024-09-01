<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Job Post User Details</h4>
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
                    Job Details
                </h4>
            </div>
            <div class="card-body">
                @if ($jobPost->thumbnail)
                <div class="mb-3">
                    <img src="{{ asset('uploads/job_thumbnail_photo') }}/{{ $jobPost->thumbnail }}" alt="Job Thumbnail" class="img-fluid">
                </div>
                @endif
                <div class="mb-3">
                    <strong>Job Title: </strong>
                    <p>{{ $jobPost->title }}</p>
                </div>
                <div class="mb-3">
                    <strong>Job Description: </strong>
                    <p>{{ $jobPost->description }}</p>
                </div>
                <div class="mb-3">
                    <strong>Job Required Proof: </strong>
                    <p>{{ $jobPost->required_proof }}</p>
                </div>
                <div class="mb-3">
                    <strong>Job Additional Note: </strong>
                    <p>{{ $jobPost->additional_note }}</p>
                </div>
                <div class="mb-3">
                    <strong>Approved By</strong>
                    <p>{{ $jobPost->approvedBy->name }}</p>
                </div>
                <div class="mb-3">
                    <strong>Approved At</strong>
                    <p>{{ date('d-m-Y h:i:s A', strtotime($jobPost->approved_at)) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Job Category</th>
                    <td>{{ $jobPost->category->name }}</td>
                </tr>
                <tr>
                    <th>Job Sub Category</th>
                    <td>{{ $jobPost->subCategory->name }}</td>
                </tr>
                @if ($jobPost->child_category_id )
                <tr>
                    <th>Job Child Category</th>
                    <td>{{ $jobPost->childCategory->name }}</td>
                </tr>
                @endif
                @if ($jobPost->thumbnail)
                <tr>
                    <th>Job Thumbnail</th>
                    <td>
                        <img src="{{ asset('uploads/job_thumbnail_photo') }}/{{ $jobPost->thumbnail }}" alt="Job Thumbnail" class="img-fluid">
                    </td>
                </tr>
                @endif
                <tr>
                    <th>Job Title</th>
                    <td>{{ $jobPost->title }}</td>
                </tr>
                <tr>
                    <th>Job Description</th>
                    <td>{{ $jobPost->description }}</td>
                </tr>
                <tr>
                    <th>Job Required Proof</th>
                    <td>{{ $jobPost->required_proof }}</td>
                </tr>
                <tr>
                    <th>Job Additional Note</th>
                    <td>{{ $jobPost->additional_note }}</td>
                </tr>
                <tr>
                    <th>Job Created At</th>
                    <td>{{ $jobPost->created_at->format('D d-M-Y H:i:s A') }}</td>
                </tr>
                <tr>
                    <th>Approved By</th>
                    <td>{{ $jobPost->approvedBy->name }}</td>
                </tr>
                <tr>
                    <th>Approved At</th>
                    <td>{{ date('D d-M-Y H:i:s A', strtotime($jobPost->approved_at)) }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Job Id</th>
                    <td>{{ $jobPost->id }}</td>
                </tr>
                <tr>
                    <th>User</th>
                    <td>{{ $jobPost->user->name }}</td>
                </tr>
                <tr>
                    <th>Job Need Worker</th>
                    <td>{{ $jobPost->need_worker }}</td>
                </tr>
                <tr>
                    <th>Worker Charge</th>
                    <td>{{ $jobPost->worker_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
                <tr>
                    <th>Extra Screenshots</th>
                    <td>{{ $jobPost->extra_screenshots }}</td>
                </tr>
                <tr>
                    <th>Boosted Time</th>
                    <td>
                        @if($jobPost->boosted_time < 60)
                            {{ $jobPost->boosted_time }} minute{{ $jobPost->boosted_time > 1 ? 's' : '' }}
                        @elseif($jobPost->boosted_time >= 60)
                            {{ round($jobPost->boosted_time / 60, 1) }} hour{{ round($jobPost->boosted_time / 60, 1) > 1 ? 's' : '' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Running Day</th>
                    <td>{{ $jobPost->running_day }} Days</td>
                </tr>
                <tr>
                    <th>Charge</th>
                    <td>{{ $jobPost->charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
                <tr>
                    <th>Site Charge <span class="text-info">( {{ get_default_settings('job_posting_charge_percentage') }} % )</span></th>
                    <td>{{ $jobPost->site_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
                <tr>
                    <th>Total Charge</th>
                    <td>{{ $jobPost->total_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

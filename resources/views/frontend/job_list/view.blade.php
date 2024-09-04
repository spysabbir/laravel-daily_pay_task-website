<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Job Post Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Category</td>
                                <td>{{ $jobPost->category->name }}</td>
                            </tr>
                            <tr>
                                <td>Sub Category</td>
                                <td>{{ $jobPost->subCategory->name }}</td>
                            </tr>
                            @if ($jobPost->child_category_id)
                            <tr>
                                <td>Child Category</td>
                                <td>{{ $jobPost->childCategory->name }}</td>
                            </tr>
                            @endif
                            @if ($jobPost->thumbnail)
                            <tr>
                                <td>Thumbnail</td>
                                <td>
                                    <img src="{{ asset('uploads/job_thumbnail_photo') }}/{{ $jobPost->thumbnail }}" alt="thumbnail" style="width: 280px; height:280px">
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td>Job Title</td>
                                <td>{{ $jobPost->title }}</td>
                            </tr>
                            <tr>
                                <td>Job Description</td>
                                <td>{{ $jobPost->description }}</td>
                            </tr>
                            <tr>
                                <td>Required Proof</td>
                                <td>{{ $jobPost->required_proof }}</td>
                            </tr>
                            <tr>
                                <td>Additional Note</td>
                                <td>{{ $jobPost->additional_note }}</td>
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
</div>

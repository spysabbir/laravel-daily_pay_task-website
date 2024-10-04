<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Job Post Details - Id: {{ $jobPost->id }}</h4>
            </div>
            <div class="card-body">
                <div>
                    @php
                        $proofSubmitted = App\Models\JobProof::where('job_post_id', $jobPost->id)->count();
                        $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $jobPost->need_worker) * 100, 2) : 100;
                        $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                    @endphp
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $proofStyleWidth }}%" aria-valuenow="{{ $proofSubmitted }}" aria-valuemin="0" aria-valuemax="{{ $row->need_worker }}">{{ $proofSubmitted / $row->need_worker }}</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Category</td>
                                <td><span class="badge bg-primary">{{ $jobPost->category->name }}</span></td>
                            </tr>
                            <tr>
                                <td>Sub Category</td>
                                <td><span class="badge bg-primary">{{ $jobPost->subCategory->name }}</span></td>
                            </tr>
                            @if ($jobPost->child_category_id)
                            <tr>
                                <td>Child Category</td>
                                <td><span class="badge bg-primary">{{ $jobPost->childCategory->name }}</span></td>
                            </tr>
                            @endif
                            @if ($jobPost->thumbnail)
                            <tr>
                                <td>Thumbnail</td>
                                <td>
                                    <img src="{{ asset('uploads/job_thumbnail_photo') }}/{{ $jobPost->thumbnail }}" alt="thumbnail" style="width: 280px; height: 280px">
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
                                <td>Each Worker Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $jobPost->worker_charge }}</span></td>
                            </tr>
                            <tr>
                                <td>
                                    Screenshots <br>
                                    <span class="text-info">Note: Additional screenshot charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('job_posting_additional_screenshot_charge') }}. <br>
                                    You get 1 screenshot for free.
                                </td>
                                <td>
                                    Free: 1 + Extra: {{ $jobPost->extra_screenshots }} = Total: {{ 1 + $jobPost->extra_screenshots }} Screenshot{{ $jobPost->extra_screenshots + 1 > 1 ? 's' : '' }} <br>
                                    <span class="text-primary">( Charge: {{ $jobPost->extra_screenshots }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('job_posting_additional_screenshot_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->extra_screenshots * get_default_settings('job_posting_additional_screenshot_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Boosted Time <br>
                                    <span class="text-info">Every 15 minutes boost Charges {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('job_posting_boosted_time_charge') }}. <br>
                                    When the job is boosted, it will be shown at the top of the job list.</span>
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
                                    Running Day <br>
                                    <span class="text-info">When running day is over the job will be closed automatically.</span>
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
                                    Site Charge <br>
                                    <span class="text-info">( {{ get_default_settings('job_posting_charge_percentage') }} % )</span>
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
</div>

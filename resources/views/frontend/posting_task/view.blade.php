<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Post Task Details - Id: {{ $postTask->id }}</h4>
            </div>
            <div class="card-body">
                @if ($postTask->status == 'Pending')
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Warning!</strong> Your task is pending. Please wait for approval.
                    </div>
                @elseif ($postTask->status == 'Canceled')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Warning!</strong> Your task is canceled. Please check the reason.
                        <p class="mt-3">
                            <strong>Canceled At:</strong> {{ date('d-m-Y h:i:s A', strtotime($postTask->canceled_at)) }} <br>
                            <strong>Canceled Reason:</strong> {{ $postTask->cancellation_reason }}
                        </p>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Category</td>
                                <td><span class="badge bg-primary">{{ $postTask->category->name }}</span></td>
                            </tr>
                            <tr>
                                <td>Sub Category</td>
                                <td><span class="badge bg-primary">{{ $postTask->subCategory->name }}</span></td>
                            </tr>
                            @if ($postTask->child_category_id)
                            <tr>
                                <td>Child Category</td>
                                <td><span class="badge bg-primary">{{ $postTask->childCategory->name }}</span></td>
                            </tr>
                            @endif
                            @if ($postTask->thumbnail)
                            <tr>
                                <td>Thumbnail</td>
                                <td>
                                    <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" alt="thumbnail" style="width: 280px; height: 280px">
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td>Task Title</td>
                                <td>{{ $postTask->title }}</td>
                            </tr>
                            <tr>
                                <td>Task Description</td>
                                <td>{{ $postTask->description }}</td>
                            </tr>
                            <tr>
                                <td>Required Proof</td>
                                <td>{{ $postTask->required_proof }}</td>
                            </tr>
                            <tr>
                                <td>Additional Note</td>
                                <td>{{ $postTask->additional_note }}</td>
                            </tr>
                            <tr>
                                <td>Work Needed</td>
                                <td>{{ $postTask->work_needed }}</td>
                            </tr>
                            <tr>
                                <td>Earnings From Work</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->earnings_from_work }}</span></td>
                            </tr>
                            <tr>
                                <td>
                                    Screenshots <br>
                                    <span class="text-info">Note: Additional screenshot charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_screenshot_charge') }}. <br>
                                    You get 1 screenshot for free.
                                </td>
                                <td>
                                    Free: 1 + Extra: {{ $postTask->extra_screenshots }} = Total: {{ 1 + $postTask->extra_screenshots }} Screenshot{{ $postTask->extra_screenshots + 1 > 1 ? 's' : '' }} <br>
                                    <span class="text-primary">( Charge: {{ $postTask->extra_screenshots }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_screenshot_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->extra_screenshots * get_default_settings('task_posting_additional_screenshot_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Boosted Time <br>
                                    <span class="text-info">Every 15 minutes boost Charges {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosted_time_charge') }}. <br>
                                    When the task is boosted, it will be shown at the top of the task list.</span>
                                </td>
                                <td>
                                    @if($postTask->boosted_time < 60)
                                        {{ $postTask->boosted_time }} Minute{{ $postTask->boosted_time > 1 ? 's' : '' }} <br>
                                    @elseif($postTask->boosted_time >= 60)
                                        {{ round($postTask->boosted_time / 60, 1) }} Hour{{ round($postTask->boosted_time / 60, 1) > 1 ? 's' : '' }} <br>
                                    @endif
                                    <span class="text-primary">( Charge: {{ $postTask->boosted_time }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosted_time_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->boosted_time / 15 * get_default_settings('task_posting_boosted_time_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Running Day <br>
                                    <span class="text-info">When running day is over the task will be closed automatically.</span>
                                </td>
                                <td>{{ $postTask->running_day }} Days</td>
                            </tr>
                            <tr>
                                <td>Task Charge</td>
                                <td>
                                    <span class="text-primary">( 50 * {{ get_site_settings('site_currency_symbol') }} {{ $postTask->earnings_from_work }} ) + {{ get_site_settings('site_currency_symbol') }} {{ $postTask->extra_screenshots * get_default_settings('task_posting_additional_screenshot_charge') }} + {{ get_site_settings('site_currency_symbol') }} {{ $postTask->boosted_time / 15 * get_default_settings('task_posting_boosted_time_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Site Charge <br>
                                    <span class="text-info">( {{ get_default_settings('task_posting_charge_percentage') }} % )</span>
                                </td>
                                <td>
                                    <span class="text-primary">{{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }} * {{ get_default_settings('task_posting_charge_percentage') }} % = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->site_charge }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Total Charge</td>
                                <td>
                                    <span class="text-primary">{{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }} + {{ get_site_settings('site_currency_symbol') }} {{ $postTask->site_charge }} = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->total_charge }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $postTask->created_at->format('d-m-Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $postTask->updated_at->format('d-m-Y h:i:s A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

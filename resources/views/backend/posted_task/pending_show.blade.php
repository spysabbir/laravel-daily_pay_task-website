<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Post Task Details - Id: {{ $postTask->id }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>User Id</td>
                                <td>{{ $postTask->user->id }}</td>
                            </tr>
                            <tr>
                                <td>User Name</td>
                                <td>{{ $postTask->user->name }}</td>
                            </tr>
                            <tr>
                                <td>User Email</td>
                                <td>{{ $postTask->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>{{ $postTask->category->name }}</td>
                            </tr>
                            <tr>
                                <td>Sub Category</td>
                                <td>{{ $postTask->subCategory->name }}</td>
                            </tr>
                            <tr>
                                <td>Child Category</td>
                                <td>{{ $postTask->child_category_id ? $postTask->childCategory->name : 'N/A' }}</td>
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
                                    Screenshots
                                </td>
                                <td>
                                    Free: 1 + Extra: {{ $postTask->extra_screenshots }} = Total: {{ 1 + $postTask->extra_screenshots }} Screenshot{{ $postTask->extra_screenshots + 1 > 1 ? 's' : '' }} <br>
                                    <span class="text-primary">( Charge: {{ $postTask->extra_screenshots }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_screenshot_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->extra_screenshots * get_default_settings('task_posting_additional_screenshot_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Boosted Time
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
                                    Running Day
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
                                    Site Charge
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
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Change Status
                </h4>
            </div>
            <div class="card-body">
                @if ($postTask->rejected_at)
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Warning!</strong> This task post has been previously rejected. <br>
                    <span>Rejected By: {{ $postTask->rejectedBy->name }}</span> <br>
                    <span>Rejected At: {{ date('d-m-Y h:i:s A', strtotime($postTask->rejected_at)) }}</span> <br>
                    <span>Rejection Reason: {{ $postTask->rejection_reason }}</span> <br>
                </div>
                @endif
                <form class="forms-sample" id="editForm">
                    @csrf
                    <input type="hidden" id="post_task_id" value="{{ $postTask->id }}">
                    @if ($postTask->thumbnail)
                    <div class="mb-3">
                        <label for="task_thumbnail" class="form-label">Task Thumbnail <span class="text-danger">* Required</span></label>
                        <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" alt="Task Thumbnail" class="img-fluid">
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="task_title" class="form-label">Task Title <span class="text-danger">* Required</span></label>
                        <input type="text" class="form-control" id="task_title" value="{{ $postTask->title }}" name="title">
                        <span class="text-danger error-text update_title_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="task_description" class="form-label">Task Description <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="task_description" name="description" rows="4">{{ $postTask->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="task_required_proof" class="form-label">Task Required Proof <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="task_required_proof" name="required_proof" rows="4">{{ $postTask->required_proof }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="task_additional_note" class="form-label">Task Additional Note <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="task_additional_note" name="additional_note" rows="4">{{ $postTask->additional_note }}</textarea>
                    </div>
                    <div class="mb-3 ">
                        <label for="task_post_status" class="form-label">Post Task Status <span class="text-danger">* Required</span></label>
                        <select class="form-select" id="task_post_status" name="status">
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

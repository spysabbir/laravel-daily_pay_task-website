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
                                <td>Required Proof Photo</td>
                                <td>
                                    Free: {{ $postTask->required_proof_photo >= 1 ? 1 : 0 }} + Additional: {{ $postTask->required_proof_photo >= 1 ? $postTask->required_proof_photo - 1 : 0 }} <br>
                                    = Total: {{ $postTask->required_proof_photo }} Required Proof Photo{{ $postTask->required_proof_photo > 1 ? 's' : '' }} <br>
                                    <span class="text-primary">( Charge: {{ $postTask->required_proof_photo >= 1 ? $postTask->required_proof_photo - 1 : 0 }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_required_proof_photo_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->required_proof_photo >= 1 ? $postTask->required_proof_photo - 1 : 0) * get_default_settings('task_posting_additional_required_proof_photo_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Boosted Time</td>
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
                                <td>Work Duration</td>
                                <td>
                                    Free: 3 Days + Additional: {{ $postTask->work_duration - 3 }} Days <br>
                                    = Total: {{ $postTask->work_duration }} Days <br>
                                    <span class="text-primary">( Charge: {{ $postTask->work_duration - 3 }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_work_duration_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->work_duration - 3) * get_default_settings('task_posting_additional_work_duration_charge') }} )</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Task Charge</td>
                                <td>
                                    <span class="text-primary">( {{ $postTask->work_needed }} * {{ get_site_settings('site_currency_symbol') }} {{ $postTask->earnings_from_work }} ) + {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->required_proof_photo >= 1 ? $postTask->required_proof_photo - 1 : 0) * get_default_settings('task_posting_additional_required_proof_photo_charge') }} + {{ get_site_settings('site_currency_symbol') }} {{ $postTask->boosted_time / 15 * get_default_settings('task_posting_boosted_time_charge') }} + {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->work_duration - 3) * get_default_settings('task_posting_boosted_time_charge') }} <br>
                                    = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }}</span>
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
                                <td>{{ $postTask->created_at->format('d M,Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $postTask->updated_at->format('d M,Y h:i:s A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Warning!</strong> This task post has been paused. <br>
            <span>Paused By: {{ $postTask->pausedBy->name }}</span> <br>
            <span>Paused At: {{ date('d M, Y h:i:s A', strtotime($postTask->paused_at)) }}</span> <br>
            <span>Pausing Reason: {{ $postTask->pausing_reason }}</span> <br>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Task Details
                </h4>
            </div>
            <div class="card-body">
                @if ($postTask->thumbnail)
                <div class="mb-3">
                    <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" alt="Task Thumbnail" class="img-fluid">
                </div>
                @endif
                <div class="mb-3">
                    <strong>Task Title: </strong>
                    <p>{{ $postTask->title }}</p>
                </div>
                <div class="mb-3">
                    <strong>Task Description: </strong>
                    <p>{{ $postTask->description }}</p>
                </div>
                <div class="mb-3">
                    <strong>Task Required Proof Answer: </strong>
                    <p>{{ $postTask->required_proof_answer }}</p>
                </div>
                <div class="mb-3">
                    <strong>Task Additional Note: </strong>
                    <p>{{ $postTask->additional_note }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

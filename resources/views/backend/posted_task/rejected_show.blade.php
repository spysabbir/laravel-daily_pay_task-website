<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Post Task Details</h4>
                <div class="text-primary">
                    Id: {{ $postTask->id }}, Status : {{ $postTask->status }}
                </div>
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
                                <td>Required Proof Photo</td>
                                <td>
                                    Free: {{ $postTask->required_proof_photo >= 1 ? 1 : 0 }} <br>
                                    Additional: {{ $postTask->required_proof_photo >= 1 ? $postTask->required_proof_photo - 1 : 0 }} <br>
                                    Total: {{ $postTask->required_proof_photo }} Required Proof Photo{{ $postTask->required_proof_photo > 1 ? 's' : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Required Proof Photo Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->required_proof_photo_charge }}</td>
                            </tr>
                            <tr>
                                <td>Worker Needed</td>
                                <td>{{ $postTask->worker_needed }}</td>
                            </tr>
                            <tr>
                                <td>Income Of Each Worker</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->income_of_each_worker }}</td>
                            </tr>
                            <tr>
                                <td>Task Cost</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->sub_cost }}</td>
                            </tr>
                            <tr>
                                <td>Site Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->site_charge }}</td>
                            </tr>
                            <tr>
                                <td>Boosting Time</td>
                                <td>
                                    @if($postTask->total_boosting_time < 60)
                                        Total: {{ $postTask->total_boosting_time }} Minute{{ $postTask->total_boosting_time > 1 ? 's' : '' }}
                                    @elseif($postTask->total_boosting_time >= 60)
                                        Total: {{ round($postTask->total_boosting_time / 60, 1) }} Hour{{ round($postTask->total_boosting_time / 60, 1) > 1 ? 's' : '' }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Total Boosting Time Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->boosting_time_charge }}</td>
                            </tr>
                            <tr>
                                <td>Work Duration</td>
                                <td>
                                    Default: 3 Days <br>
                                    Additional: {{ $postTask->total_work_duration - 3 }} Days <br>
                                    Total: {{ $postTask->total_work_duration }} Days
                                </td>
                            </tr>
                            <tr>
                                <td>Work Duration Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->work_duration_charge }}</td>
                            </tr>
                            <tr>
                                <td>Total Cost</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->total_cost }}</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $postTask->created_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $postTask->updated_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Warning!</strong> This task post has been rejected. <br>
            <span>Rejected By: {{ $postTask->rejectedBy->name }}</span> <br>
            <span>Rejected At: {{ date('d M, Y h:i:s A', strtotime($postTask->rejected_at)) }}</span> <br>
            <span>Rejection Reason: {{ $postTask->rejection_reason }}</span> <br>
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

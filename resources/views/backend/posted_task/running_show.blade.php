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
                                <td>Working Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->working_charge }}</td>
                            </tr>
                            <tr>
                                <td>Site Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->site_charge }}</td>
                            </tr>
                            <tr>
                                <td>Task Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }}</td>
                            </tr>
                            <tr>
                                <td>Boosting Time</td>
                                <td>
                                    @if($postTask->boosting_time < 60)
                                        Last: {{ $postTask->boosting_time }} Minute{{ $postTask->boosting_time > 1 ? 's' : '' }}
                                    @elseif($postTask->boosting_time >= 60)
                                        Last: {{ round($postTask->boosting_time / 60, 1) }} Hour{{ round($postTask->boosting_time / 60, 1) > 1 ? 's' : '' }}
                                    @endif
                                    <br>
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
                                    Additional: {{ $postTask->work_duration - 3 }} Days <br>
                                    Total: {{ $postTask->work_duration }} Days
                                </td>
                            </tr>
                            <tr>
                                <td>Work Duration Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->work_duration_charge }}</td>
                            </tr>
                            <tr>
                                <td>Total Charge</td>
                                <td>{{ get_site_settings('site_currency_symbol') }} {{ $postTask->total_charge }}</td>
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
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Change Status
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Approved!</strong> This task post has been approved by <strong>{{ $postTask->approvedBy->name }}</strong> at <strong>{{ date('d M, Y h:i:s A', strtotime($postTask->approved_at)) }}</strong>
                </div>
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
                        <label for="task_required_proof_answer" class="form-label">Task Required Proof Answer <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="task_required_proof_answer" name="required_proof_answer" rows="4">{{ $postTask->required_proof_answer }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="task_additional_note" class="form-label">Task Additional Note <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="task_additional_note" name="additional_note" rows="4">{{ $postTask->additional_note }}</textarea>
                    </div>
                    <div class="mb-3 ">
                        <input type="hidden" value="Running" name="status">
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

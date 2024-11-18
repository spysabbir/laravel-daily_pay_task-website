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
                                    Free: 3 Days <br>
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
        @if ($postTask->status == 'Pending')
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Warning!</strong> Your task is pending. Please wait for approval. <br>
                <strong>Submited At:</strong> {{ date('d M, Y h:i:s A', strtotime($postTask->created_at)) }} <br>
            </div>
        @else
            @if ($postTask->status == 'Running')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Success!</strong> Your task is approved. Please start your work. <br>
                <strong>Approved At:</strong> {{ date('d M, Y h:i:s A', strtotime($postTask->approved_at)) }} <br>
            </div>
            @endif
            @if ($postTask->status == 'Canceled')
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Warning!</strong> Your task is canceled. Please check the reason.
                <div class="mt-3">
                    <strong>Canceled By:</strong> {{ $postTask->canceledBy->user_type == 'Backend' ? 'Admin' : $postTask->canceledBy->name }} <br>
                    <strong>Canceled At:</strong> {{ date('d M, Y h:i:s A', strtotime($postTask->canceled_at)) }} <br>
                    <strong>Cancellation Reason:</strong> {{ $postTask->cancellation_reason }}
                </div>
            </div>
            @endif
            @if ($postTask->status == 'Paused')
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Warning!</strong> Your task is paused. Please wait for Resume.
                <div class="mt-3">
                    <strong>Paused By:</strong> {{ $postTask->pausedBy->user_type == 'Backend' ? 'Admin' : $postTask->pausedBy->name }} <br>
                    <strong>Paused At:</strong> {{ date('d M, Y h:i:s A', strtotime($postTask->paused_at)) }} <br>
                    <strong>Pausing Reason:</strong> {{ $postTask->pausing_reason }}
                </div>
            </div>
            @endif
            @if ($postTask->status == 'Completed')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Success!</strong> Your task is completed. Please check the proof.
                <strong>Completed At:</strong> {{ date('d M, Y h:i:s A', strtotime($postTask->completed_at)) }} <br>
            </div>
            @endif
            <div class="border p-1 m-1">
                @php
                    $proofSubmittedCount = $proofSubmitted->count();
                    $proofStyleWidth = $proofSubmittedCount != 0 ? round(($proofSubmittedCount / $postTask->worker_needed) * 100, 2) : 100;
                    $progressBarClass = $proofSubmittedCount == 0 ? 'primary' : 'success';
                @endphp
                <p class="mb-1"><strong class="text-info">Proof Status: </strong> <span class="text-success">Submit: {{ $proofSubmittedCount }}</span>, Need: {{ $postTask->worker_needed }}</p>
                <div class="progress position-relative">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $proofStyleWidth }}%" aria-valuenow="{{ $proofSubmittedCount }}" aria-valuemin="0" aria-valuemax="{{ $postTask->worker_needed }}"></div>
                    <span class="position-absolute w-100 text-center">{{ $proofSubmittedCount }} / {{ $postTask->worker_needed }}</span>
                </div>
            </div>
            <div class="border p-1 m-1">
                @php
                    $pendingProofCount = $proofSubmitted->where('status', 'Pending')->count();
                    $approvedProofCount = $proofSubmitted->where('status', 'Approved')->count();
                    $rejectedProofCount = $proofSubmitted->where('status', 'Rejected')->count();
                    $reviewedProofCount = $proofSubmitted->where('status', 'Reviewed')->count();

                    $totalProof = $approvedProofCount + $rejectedProofCount + $reviewedProofCount + $pendingProofCount;

                    $pendingProofStyleWidth = $totalProof != 0 ? round(($pendingProofCount / $totalProof) * 100, 2) : 100;
                    $approvedProofStyleWidth = $totalProof != 0 ? round(($approvedProofCount / $totalProof) * 100, 2) : 100;
                    $rejectedProofStyleWidth = $totalProof != 0 ? round(($rejectedProofCount / $totalProof) * 100, 2) : 100;
                    $reviewedProofStyleWidth = $totalProof != 0 ? round(($reviewedProofCount / $totalProof) * 100, 2) : 100;
                @endphp
                <p class="mb-1"><strong class="text-info">Check Status: </strong> <span class="text-primary">Pending: {{ $pendingProofCount }}</span>, <span class="text-success">Approved: {{ $approvedProofCount }}</span>, <span class="text-danger">Rejected: {{ $rejectedProofCount }}</span>, <span class="text-warning">Reviewed: {{ $reviewedProofCount }}</span></p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $pendingProofStyleWidth }}%" aria-valuenow="{{ $pendingProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $pendingProofCount }} / {{ $totalProof }}</div>
                    <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: {{ $approvedProofStyleWidth }}%" aria-valuenow="{{ $approvedProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $approvedProofCount }} / {{ $totalProof }}</div>
                    <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" style="width: {{ $rejectedProofStyleWidth }}%" aria-valuenow="{{ $rejectedProofStyleWidth }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $rejectedProofCount }} / {{ $totalProof }}</div>
                    <div class="progress-bar bg-warning progress-bar-striped" role="progressbar" style="width: {{ $reviewedProofStyleWidth }}%" aria-valuenow="{{ $reviewedProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $reviewedProofCount }} / {{ $totalProof }}</div>
                </div>
            </div>
            <div class="border p-1 m-1">
                <strong class="text-info">Charge Status: </strong>,
                <span class="text-secondary">Waiting: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->worker_needed) * ($postTask->worker_needed - $proofSubmitted->count()), 2) }}</span>,
                <span class="text-primary">Pending: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->worker_needed) * $pendingProof, 2) }}</span>,
                <span class="text-success">Payment: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->worker_needed) * $approvedProof, 2) }}</span>,
                <span class="text-danger">Refund: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->worker_needed) * $finallyRejectedProof, 2) }}</span>,
                <span class="text-warning">Hold: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->worker_needed) *  $waitingRejectedProof, 2) }}</span>
            </div>
        @endif
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

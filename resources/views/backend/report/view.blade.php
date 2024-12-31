<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Report ID: {{ $report->id }}</h3>
        <h3 class="card-title">Report Type: {{ $report->type }}</h3>
        <h3 class="card-title">Report Status: {{ $report->status }}</h3>
        <h3 class="card-title">Report Date: {{ $report->created_at->format('d M, Y h:i A') }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <strong>Reported User ID: {{ $report->reported->id }}</strong><br>
                    <strong>Reported User Name: {{ $report->reported->name }}</strong><br>
                </div>
                <div class="mb-3">
                    <strong>Report By User ID: {{ $report->reportedBy->id }}</strong><br>
                    <strong>Report By User Name: {{ $report->reportedBy->name }}</strong><br>
                </div>
                <div class="mb-3">
                    @if ($report->post_task_id)
                        <div class="border p-2">
                            <strong class="mb-2">Post Task Id:</strong> {{ $report->post_task_id }}<br>
                            <strong class="mb-2">Post Task Status:</strong> {{ $report->postTask->status }}<br>
                        </div>
                    @endif
                    @if ($report->proof_task_id)
                        <div class="border p-2">
                            <strong class="mb-2">Proof Task Id:</strong> {{ $report->proof_task_id }}<br>
                            <strong class="mb-2">Proof Task Status:</strong> {{ $report->proofTask->status }}<br>
                        </div>
                    @endif
                    <p class="my-2">Reason: {!! nl2br(e($report->reason)) !!}</p><br>
                    @if ($report->photo)
                    <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="Report Photo" class="img-fluid">
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                @if ($report->status == 'Pending')
                <form class="forms-sample" id="replyForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Select Status</option>
                            <option value="False">False</option>
                            <option value="Received">Received</option>
                        </select>
                        <span class="text-danger error-text status_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="reply" class="form-label">Reply <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply" name="reply" rows="4" placeholder="Reply"></textarea>
                        <span class="text-danger error-text reply_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="reply_photo" class="form-label">Reply Photo <span class="text-info">Optional</span></label>
                        <input type="file" class="form-control" id="reply_photo" name="reply_photo" accept=".jpg, .jpeg, .png">
                        <span class="text-danger error-text reply_photo_error d-block"></span>
                        <img src="" alt="Photo" id="photoPreview" class="mt-2" style="display: none; width: 100px; height: 100px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                @else
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Reply</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Reply:</strong> {{ $report_reply->reply }}<br>
                            @if ($report_reply->reply_photo)
                                <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid my-3">
                            @endif
                            <strong>Replied By:</strong> {{ $report_reply->resolvedBy->name }}<br>
                            <strong>Replied At:</strong> {{ date('d M, Y h:i A', strtotime($report_reply->resolved_at)) }}<br>
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#status').change(function (e) {
            e.preventDefault();

            var report_id = $('input[name="report_id"]').val();

            if ($(this).val() == 'False') {
                $('#reply').val('Hi, We are reviewing your report but the report has been regard false so please check carefully before sending it to us. Alert: If your report over and over false your account will suspended so do not send false evidence to us for checking, Thanks. (Report ID: ' + report_id + ')');
            } else {
                $('#reply').val('Hi, we are reviewing your report. We received your report. We will investigate and take necessary actions, thank you. (Report ID: ' + report_id + ')');
            }
        });
    });
</script>




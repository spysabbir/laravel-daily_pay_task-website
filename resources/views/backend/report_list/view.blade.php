<div class="card">
    <div class="card-header">
        <h3 class="card-title">Type: {{ $report->type }}</h3>
        <h3 class="card-title">Status - {{ $report->status }}</h3>
        <br>
        @if ($report->post_task_id)
        <strong class="mb-2">Post Task:</strong> {{ $report->post_task_id }}<br>
        @endif
        @if ($report->proof_task_id)
        <strong class="mb-2">Proof Task:</strong> {{ $report->proof_task_id }}<br>
        @endif    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <h4 class="card-title">Reported ID: {{ $report->reported->id }}</h4>
                    <h4 class="card-title">Reported User: {{ $report->reported->name }}</h4>
                    <p class="mb-2">Reason: {{ $report->reason }}</p>
                    @if ($report->photo)
                    <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="Report Photo" class="img-fluid">
                    @endif
                </div>
                <div>
                    <strong>Reported By: {{ $report->reportedBy->name }}</strong><br>
                    <strong>Reported At: {{ $report->created_at->format('d M, Y h:i A') }}</strong>
                </div>
            </div>
            <div class="col-lg-6">
                @if ($report->status == 'Pending')
                <form class="forms-sample" id="replyForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                    <div class="mb-3">
                        <label for="reply" class="form-label">Reply <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply" name="reply" rows="4" placeholder="Reply"></textarea>
                        <span class="text-danger error-text reply_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="reply_photo" class="form-label">Reply Photo</label>
                        <input type="file" class="form-control" id="reply_photo" name="reply_photo" accept=".jpg, .jpeg, .png">
                        <span class="text-danger error-text reply_photo_error d-block"></span>
                        <img src="" alt="Photo" id="photoPreview" class="mt-2" style="display: none; width: 100px; height: 100px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                @else
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Resolved</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Reply:</strong> {{ $report_reply->reply }}<br>
                            @if ($report_reply->reply_photo)
                            <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid my-3">
                            @endif
                            <strong>Resolved By:</strong> {{ $report_reply->resolvedBy->name }}<br>
                            <strong>Resolved_at:</strong> {{ date('d M, Y h:i A', strtotime($report_reply->resolved_at)) }}<br>
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>




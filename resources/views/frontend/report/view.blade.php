<div class="card">
    <div class="card-header">
        <strong>Report Id - {{ $report->id }}</strong>
        <br>
        <strong>Report Type - {{ $report->type }}</strong>
        <br>
        <strong>Report Status - {{ $report->status }}</strong>
    </div>
    <div class="card-body">
        <p class="card-text">
            <strong class="mb-2">Report Date:</strong> {{ $report->created_at->format('d M, Y h:i:s A') }}<br>
            <strong class="mb-2">Reported User Name:</strong> {{ $report->reported->name }}<br>
            @if ($report->post_task_id)
                <div class="border p-2">
                    <strong class="mb-2">Reported Post Task Id:</strong> {{ $report->post_task_id }}<br>
                    <strong class="mb-2">Reported Post Task Status:</strong> {{ $report->postTask->status }}<br>
                </div>
            @endif
            @if ($report->proof_task_id)
                <div class="border p-2">
                    <strong class="mb-2">Reported Proof Task Id:</strong> {{ $report->proof_task_id }}<br>
                    <strong class="mb-2">Reported Proof Task Status:</strong> {{ $report->proofTask->status }}<br>
                </div>
            @endif
            <strong class="mb-2">Reason:</strong> {!! nl2br(e($report->reason)) !!}<br>
            @if ($report->photo)
                <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="Report" class="img-fluid mt-3">
            @endif
        </p>
        @if ($report->status == 'False')
        <div class="card mt-3">
            <div class="card-header">
                <h5>False</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong class="mb-2">Reply At:</strong> {{ date('d M, Y h:i:s A', strtotime($report_reply->replied_at)) }}<br>
                    <strong class="mb-2">Reply:</strong> {!! nl2br(e($report_reply->reply)) !!}<br>
                    @if ($report_reply->reply_photo)
                        <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid mt-3">
                    @endif
                </p>
            </div>
        </div>
        @elseif ($report->status == 'Received')
        <div class="card mt-3">
            <div class="card-header">
                <h5>Received</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong class="mb-2">Reply At:</strong> {{ date('d M, Y h:i:s A', strtotime($report_reply->replied_at)) }}<br>
                    <strong class="mb-2">Reply:</strong> {!! nl2br(e($report_reply->reply)) !!}<br>
                    @if ($report_reply->reply_photo)
                        <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid mt-3">
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>
</div>


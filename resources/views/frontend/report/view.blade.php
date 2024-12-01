<div class="card">
    <div class="card-header">
        <strong>Type - {{ $report->type }}</strong>
        <br>
        <strong>Status - {{ $report->status }}</strong>
    </div>
    <div class="card-body">
        <div>
            <strong class="mb-2">Reported User:</strong> {{ $report->reported->name }}<br>
            @if ($report->post_task_id)
                <strong class="mb-2">Reported Post Task Id:</strong> {{ $report->post_task_id }}<br>
            @endif
            @if ($report->proof_task_id)
                <strong class="mb-2">Reported Proof Task Id:</strong> {{ $report->proof_task_id }}<br>
            @endif
        </div>
        <p class="card-text">
            <strong class="mb-2">Reason:</strong> {{ $report->reason }}<br>
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
            @if ($report->photo)
                <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="Report" class="img-fluid mt-3">
            @endif
            <strong class="mb-2">Reported Date:</strong> {{ $report->created_at->format('d M, Y h:i:s A') }}<br>
        </p>
        @if ($report->status == 'Resolved')
        <div class="card mt-3">
            <div class="card-header">
                <h5>Resolved</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong class="mb-2">Reply:</strong> {{ $report_reply->reply }}<br>
                    @if ($report_reply->reply_photo)
                        <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid mt-3">
                    @endif
                    <strong class="mb-2">Resolved At:</strong> {{ date('d M, Y h:i:s A', strtotime($report_reply->resolved_at)) }}<br>
                </p>
            </div>
        </div>
        @endif
    </div>
</div>


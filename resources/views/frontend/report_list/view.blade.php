<div class="card">
    <div class="card-header">
        <strong>Type - {{ $report->type }}</strong>
        <br>
        <strong>Status: {{ $report->status }}</strong>
    </div>
    <div class="card-body">
        <h4 class="card-title">Reported User: {{ $report->reported->name }}</h4>
        <p class="card-text">
            <strong>Reason:</strong> {{ $report->reason }}<br>
            @if ($report->post_task_id)
            <strong>Post Task:</strong> {{ $report->post_task_id }}<br>
            @endif
            @if ($report->proof_task_id)
            <strong>Proof Task:</strong> {{ $report->proof_task_id }}<br>
            @endif
            @if ($report->photo)
            <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="Report" class="img-fluid">
            @endif
            <strong>Reported At:</strong> {{ $report->created_at->format('d-F-Y h:i A') }}<br>
        </p>
        @if ($report->status == 'Resolved')
        <div class="card mt-3">
            <div class="card-header">
                <h5>Resolved</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong>Reply:</strong> {{ $report_reply->reply }}<br>
                    @if ($report->reply_photo)
                    <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid">
                    @endif
                    <strong>Resolved_at:</strong> {{ date('d-F-Y h:i A', strtotime($report_reply->resolved_at)) }}<br>
                </p>
            </div>
        </div>
        @endif
    </div>
</div>


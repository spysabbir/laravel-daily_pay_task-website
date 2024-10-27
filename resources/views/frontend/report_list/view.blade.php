<div class="card">
    <div class="card-header">
        <strong>Type - {{ $report->type }}</strong>
        <br>
        <strong>Status: {{ $report->status }}</strong>
        <br>
        @if ($report->post_task_id)
        <a href="{{ route('find_task.details', encrypt($report->post_task_id)) }}" target="_blank" title="View Task" class="btn btn-info btn-sm">
            View Task
        </a>
        @endif
        @if ($report->proof_task_id)
        <strong class="mb-2">Proof Task:</strong> {{ $report->proof_task_id }}<br>
        @endif
    </div>
    <div class="card-body">
        <h4 class="card-title">Reported User: {{ $report->reported->name }}</h4>
        <p class="card-text">
            <strong class="mb-2">Reason:</strong> {{ $report->reason }}<br>
            <strong class="mb-2">Reported At:</strong> {{ $report->created_at->format('d-F-Y h:i A') }}<br>
            @if ($report->photo)
            <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="Report" class="img-fluid mt-3">
            @endif
        </p>
        @if ($report->status == 'Resolved')
        <div class="card mt-3">
            <div class="card-header">
                <h5>Resolved</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong class="mb-2">Reply:</strong> {{ $report_reply->reply }}<br>
                    <strong class="mb-2">Resolved At:</strong> {{ date('d-F-Y h:i A', strtotime($report_reply->resolved_at)) }}<br>
                    @if ($report->reply_photo)
                    <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid mt-3">
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>
</div>


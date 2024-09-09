<div class="card">
    <div class="card-header">
        <h5>Status - {{ $report->status }}</h5>
    </div>
    <div class="card-body">
        <h4 class="card-title">Reported User: {{ $report->reported->name }}</h4>
        @if ($report->photo)
        <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="{{ $report->reason }}" class="img-fluid">
        @endif
        <p class="card-text">
            <strong>Reason:</strong> {{ $report->reason }}<br>
            <strong>Reported At:</strong> {{ $report->created_at }}<br>
        </p>
        @if ($report->status == 'Resolved')
        <div class="card mt-3">
            <div class="card-header">
                <h5>Resolved</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong>Reply:</strong> {{ $report_reply->reply }}<br>
                    <strong>Resolved_at:</strong> {{ date('d-m-Y', strtotime($report_reply->resolved_at)) }}<br>
                </p>
            </div>
        </div>
        @endif
    </div>
</div>


<div class="card">
    <div class="card-header">
        <h5>Status - {{ $report->status }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <h4 class="card-title">Reported User: {{ $report->reported->name }}</h4>
                    @if ($report->photo)
                    <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="{{ $report->reason }}" class="img-fluid">
                    @endif
                    <p>Reason: {{ $report->reason }}</p>
                </div>
                <div>
                    <strong>Reported By: {{ $report->reportedBy->name }}</strong><br>
                    <strong>Reported At: {{ $report->created_at }}</strong>
                </div>
            </div>
            <div class="col-lg-6">
                @if ($report->status == 'Pending')
                <form class="forms-sample" id="replyForm">
                    @csrf
                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                    <div class="mb-3">
                        <label for="reply" class="form-label">Reply</label>
                        <textarea class="form-control" id="reply" name="reply" rows="4" placeholder="Reply"></textarea>
                        <span class="text-danger error-text reply_error"></span>
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
                            <strong>Resolved_at:</strong> {{ date('d-m-Y', strtotime($report_reply->resolved_at)) }}<br>
                            <strong>Resolved By:</strong> {{ $report_reply->resolvedBy->name }}<br>
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>




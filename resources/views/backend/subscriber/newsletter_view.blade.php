<div class="card">
    <div class="card-body">
        <h5 class="mb-2"><strong>Subject:</strong> {{ $newsletter->subject }}</h5>
        <h5 class="mb-2"><strong>Content:</strong> {{ $newsletter->content }}</h5>
        <h5 class="mb-2"><strong>Status:</strong> {{ $newsletter->status }}</h5>
        <h5 class="mb-2"><strong>Sent At:</strong> {{ date('d M, Y h:i A', strtotime($newsletter->sent_at)) }}</h5>
        <h5 class="mb-2"><strong>Created By:</strong> {{ $newsletter->createdby->name }}</h5>
        <h5 class="mb-2"><strong>Created At:</strong> {{ $newsletter->created_at->format('d M, Y h:i A') }}</h5>
    </div>
</div>


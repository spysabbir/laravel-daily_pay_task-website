<div class="card">
    <div class="card-body">
        <h4 class="card-title">Question: {{ $faq->question }}</h4>
        <p class="card-text">Answer: {{ $faq->answer }}</p>
        <hr>
        <span>Status: {{ $faq->status }}</span> <br>
        <span>Created by: {{ $faq->createdBy->name }}</span> <br>
        <span>Created at: {{ $faq->created_at->format('j M, Y h:i A') }}</span> <br>
        <span>Updated by: {{ $faq->updated_by ? $faq->updatedBy->name : 'N/A' }}</span> <br>
        <span>Updated at: {{ $faq->updated_at->format('j M, Y h:i A') }}</span> <br>
        <span>Deleted by: {{ $faq->deleted_by ? $faq->deletedBy->name : 'N/A' }}</span> <br>
        <span>Deleted at: {{ $faq->deleted_at ? $faq->deleted_at->format('j M, Y h:i A') : 'N/A' }}</span> <br>
    </div>
</div>


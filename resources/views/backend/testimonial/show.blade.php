<div class="card">
    <div class="card-body">
        <img class="mb-3" src="{{ asset('uploads/testimonial_photo') }}/{{ $testimonial->photo }}" alt="{{ $testimonial->name }}" class="img-thumbnail" style="width: 120px; height: 120px;">
        <h4 class="card-title">Name: {{ $testimonial->name }}</h4>
        <h5 class="card-title">Designation: {{ $testimonial->designation }}</h5>
        <p class="card-text">Comment: {{ $testimonial->comment }}</p>
        <hr>
        <span>Status: {{ $testimonial->status }}</span> <br>
        <span>Created by: {{ $testimonial->createdBy->name }}</span> <br>
        <span>Created at: {{ $testimonial->created_at->format('j M, Y h:i A') }}</span> <br>
        <span>Updated by: {{ $testimonial->updated_by ? $testimonial->updatedBy->name : 'N/A' }}</span> <br>
        <span>Updated at: {{ $testimonial->updated_at->format('j M, Y h:i A') }}</span> <br>
        <span>Deleted by: {{ $testimonial->deleted_by ? $testimonial->deletedBy->name : 'N/A' }}</span> <br>
        <span>Deleted at: {{ $testimonial->deleted_at ? $testimonial->deleted_at->format('j M, Y h:i A') : 'N/A' }}</span> <br>
    </div>
</div>


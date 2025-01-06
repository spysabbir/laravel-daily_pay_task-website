<div class="row">
    <div class="col-lg-8">
        <div class="mb-3 border p-2">
            <h4 class="mb-2">Post Task Additional Note:</h4>
            <div>
                {!! nl2br(e($proofTask->postTask->additional_note)) !!}
            </div>
        </div>
        <div class="mb-3 border p-2">
            <h4>Proof Answer:</h4>
            <div>
                {{ $proofTask->proof_answer }}
            </div>
        </div>
        @if (!json_decode($proofTask->proof_photos))
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading text-center">This task does not require any proof photo.</h4>
            </div>
        @else
        <div class="mb-3">
            <h4>Proof Image:</h4>
            <div id="backend-single-lightgallery" class="image-grid">
                @foreach (json_decode($proofTask->proof_photos) as $photo)
                <a href="" class="" data-src="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" data-sub-html="<h4>Proof Task Photo {{ $loop->iteration }}</h4>">
                    <img class="proof-image my-3" src="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" alt="Proof Task Photo {{ $loop->iteration }}">
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="mb-3">
            <h4>Proof Task Information:</h4>
            <div class="mb-2 border p-2">
                <p><strong>Proof Task Id:</strong> {{ $proofTask->id }}</p>
                <p><strong>Submited Date:</strong> {{ $proofTask->created_at->format('d M, Y h:i A') }}</p>
            </div>
        </div>
        <div class="mb-3">
            <h4>User Information:</h4>
            <div class="mt-2 border p-2">
                <p><strong>User Id:</strong> {{ $proofTask->user->id }}</p>
                <p><strong>User Name:</strong> {{ $proofTask->user->name }}</p>
                <p><strong>User Ip:</strong> {{ $proofTask->user_ip }}</p>
            </div>
        </div>
        @if ($proofTask->status == 'Pending')
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Pending</h4>
                <p>This task proof is pending for approval or rejection.</p>
            </div>
        @elseif ($proofTask->status == 'Approved')
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Approved!</h4>
            <p>Proof Task has been approved.</p>
            <hr>
            <h5>
                <strong>Rating:</strong>
                @if (!$proofTask->rating)
                    <span class="text-danger">Not Rated</span>
                @else
                @for ($i = 0; $i < $proofTask->rating->rating; $i++)
                <i class="fa-solid fa-star text-warning"></i>
                @endfor
                @endif
                </h5>
            <h5>
                <strong>Bonus:</strong>
                @if (!$proofTask->bonus)
                    <span class="text-danger">No Bonus</span>
                @else
                {{ get_site_settings('site_currency_symbol') }} {{ $proofTask->bonus->amount }}
                @endif
            </h5>
            <hr>
            <p>Approved At: {{ date('d M, Y h:i A', strtotime($proofTask->approved_at)) }}</p>
            <p>Approved By: {{ $proofTask->approvedBy->user_type == 'Backend' ? 'Admin' : $proofTask->approvedBy->name }}</p>
        </div>
        @elseif ($proofTask->status == 'Rejected')
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Rejected!</h4>
            <p>Proof Task has been rejected.</p>
            <hr>
            <p><strong>Rejected Reason:</strong> {{ $proofTask->rejected_reason }}</p>
            @if ($proofTask->rejected_reason_photo)
                <strong>Rejected Reason Photo: </strong>
                <a href="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" target="_blank">
                    <img src="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" class="img-fluid" alt="Rejected Reason Photo">
                </a>
            @endif
            <p>Rejected At: {{ date('d M, Y h:i A', strtotime($proofTask->rejected_at)) }}</p>
            <p>Rejected By: {{ $proofTask->rejectedBy->user_type == 'Backend' ? 'Admin' : $proofTask->rejectedBy->name }}</p>
        </div>
        @elseif ($proofTask->status == 'Reviewed')
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Rejected!</h4>
            <p>Proof Task has been rejected.</p>
            <hr>
            <p><strong>Rejected Reason:</strong> {{ $proofTask->rejected_reason }}</p>
            @if ($proofTask->rejected_reason_photo)
                <strong>Rejected Reason Photo: </strong>
                <a href="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" target="_blank">
                    <img src="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" class="img-fluid" alt="Rejected Reason Photo">
                </a>
            @endif
            <p>Rejected At: {{ date('d M, Y h:i A', strtotime($proofTask->rejected_at)) }}</p>
            <p>Rejected By: {{ $proofTask->rejectedBy->user_type == 'Backend' ? 'Admin' : $proofTask->rejectedBy->name }}</p>
        </div>
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Reviewed!</h4>
            <p>Proof Task has been reviewed.</p>
            <hr>
            <p><strong>Reviewed Reason:</strong> {{ $proofTask->reviewed_reason }}</p>
            <p>Reviewed At: {{ date('d M, Y h:i A', strtotime($proofTask->reviewed_at)) }}</p>
        </div>
        @endif
    </div>
</div>

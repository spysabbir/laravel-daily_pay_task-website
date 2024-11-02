<div id="proofTaskListPendingCarousel" class="carousel slide" data-bs-ride="carousel">
    <ol class="carousel-indicators">
        @foreach ($proofTaskListPending as $proofTask)
            <li data-bs-target="#proofTaskListPendingCarousel" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
        @endforeach
    </ol>
    <div class="carousel-inner">
        @foreach ($proofTaskListPending as $proofTask)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-proof-id="{{ $proofTask->id }}">
                <div class="mb-3">
                    <h4>Task Proof Information:</h4>
                    <div class="mb-2 border p-2">
                        <strong>Task Proof Id:</strong> {{ $proofTask->id }}
                    </div>
                    <h4>User Information:</h4>
                    <div class="mb-2 border p-2">
                        <strong>User Id:</strong> {{ $proofTask->user->id }}, <strong>User Name:</strong> {{ $proofTask->user->name }}, <strong>User Ip:</strong> {{ $proofTask->user->userDetail->ip }}
                    </div>
                    <h4>Proof Answer:</h4>
                    <div class="mb-2 border p-2">
                        {{ $proofTask->proof_answer }}
                    </div>
                </div>
                <div class="mb-3">
                    <h4>Proof Image:</h4>
                    <div class="my-2">
                        <div class="d-flex justify-content-center align-items-center">
                        @foreach (json_decode($proofTask->proof_photos) as $image)
                            <a href="{{ asset('uploads/task_proof_photo') }}/{{ $image }}" target="_blank" class="m-1">
                                <img src="{{ asset('uploads/task_proof_photo') }}/{{ $image }}" style="max-height: 180px; max-width: 180px" alt="Proof Image">
                            </a>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <a class="carousel-control-prev" data-bs-target="#proofTaskListPendingCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </a>
    <a class="carousel-control-next" data-bs-target="#proofTaskListPendingCarousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </a>
</div>

<!-- JavaScript to set the initial and updated values of proof_task_id -->
<script>
    proofTaskListPendingCarousel = new bootstrap.Carousel(document.getElementById('proofTaskListPendingCarousel'), {
        interval: false
    });
</script>

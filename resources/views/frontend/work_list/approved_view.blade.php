<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <h4>Job Title:</h4>
            <div>
                {{ $jobPost->title }}
            </div>
        </div>
        <div class="mb-3">
            <h4>Job Description:</h4>
            <div>
                {{ $jobPost->description }}
            </div>
        </div>
        <div class="mb-3">
            <h4>Job Required Proof:</h4>
            <div>
                {{ $jobPost->required_proof }}
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <h4>Proof Answer:</h4>
            <div>
                {{ $jobProof->proof_answer }}
            </div>
        </div>
        <div class="mb-3">
            <h4>Proof Image:</h4>
            <div class="my-2">
                <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach (json_decode($jobProof->proof_photos) as $photo)
                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach (json_decode($jobProof->proof_photos) as $photo)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ asset('uploads/job_proof_photo') }}/{{ $photo }}" class="d-block w-100" alt="Job Proof Photo">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>Job Proof Photo {{ $loop->iteration }}</h5>
                                    <p>Job Proof Photo {{ $loop->iteration }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </a>
                    <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

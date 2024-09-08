<div class="row">
    <div class="col-lg-8">
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
    <div class="col-lg-4">
        @if ($jobProof->status == 'Pending')
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Pending</h4>
                <p>This job proof is pending for approval.</p>
            </div>
        @elseif ($jobProof->status == 'Rejected')
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Rejected!</h4>
                <p>Job Proof has been rejected.</p>
                <hr>
                <p><strong>Rejected Reason:</strong> {{ $jobProof->rejected_reason }}</p>
                <p>Rejected At: {{ date('d M, Y h:i A', strtotime($jobProof->rejected_at)) }}</p>
                <p>Rejected By: {{ $jobProof->rejectedBy->name }}</p>
            </div>
        @elseif ($jobProof->status == 'Reviewed')
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Rejected!</h4>
            <p>Job Proof has been rejected.</p>
            <hr>
            <p><strong>Rejected Reason:</strong> {{ $jobProof->rejected_reason }}</p>
            <p>Rejected At: {{ date('d M, Y h:i A', strtotime($jobProof->rejected_at)) }}</p>
            <p>Rejected By: {{ $jobProof->rejectedBy->name }}</p>
        </div>
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Reviewed!</h4>
            <p>Job Proof has been reviewed.</p>
            <hr>
            <p><strong>Reviewed Reason:</strong> {{ $jobProof->reviewed_reason }}</p>
            <p>Reviewed At: {{ date('d M, Y h:i A', strtotime($jobProof->reviewed_at)) }}</p>
        </div>
        @endif
        @if ($jobProof->status == 'Reviewed')
        <div class="mt-3">
            <form class="forms-sample" id="editForm">
                @csrf
                <input type="hidden" id="job_proof_id" value="{{ $jobProof->id }}">
                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">* Required</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="status" id="approve" value="Approved">
                            <label class="form-check-label" for="approve">
                                Approved
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="status" id="reject" value="Rejected">
                            <label class="form-check-label" for="reject">
                                Rejected
                            </label>
                        </div>
                    </div>
                    <span class="text-danger error-text update_status_error"></span>
                </div>
                <div id="rejected_div">
                    <div class="mb-3">
                        <label for="rejected_reason" class="form-label">Rejected Reason <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="rejected_reason" name="rejected_reason" rows="3" placeholder="Rejected Reason"></textarea>
                        <span class="text-danger error-text update_rejected_reason_error"></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        // Hide rejected reason div initially
        $('#rejected_div').hide();
        $('input[name="status"]').change(function() {
            $('.update_status_error').text('');
            if ($(this).val() == 'Rejected') {
                $('#rejected_div').show();
            } else {
                $('#rejected_div').hide();
            }
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();
            var id = $('#job_proof_id').val();
            var url = "{{ route('backend.job_proof.check.update', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "PUT",
                data: $(this).serialize(),
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        if (response.status == 401) {
                            $.each(response.error, function(prefix, val){
                                $('span.update_'+prefix+'_error').text(val[0]);
                            })
                        } else {
                            toastr.success('Job Proof has been updated successfully.');
                            $('#allDataTable').DataTable().ajax.reload();
                            $(".viewModal").modal('hide');
                        }
                    }
                },
            });
        })
    });
</script>

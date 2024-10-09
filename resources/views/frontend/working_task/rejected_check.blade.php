<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <h4>Proof Answer:</h4>
            <div>
                {{ $proofTask->proof_answer }}
            </div>
        </div>
        <div class="mb-3">
            <h4>Proof Image:</h4>
            <div class="my-2">
                <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach (json_decode($proofTask->proof_photos) as $photo)
                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach (json_decode($proofTask->proof_photos) as $photo)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" class="d-block w-100" alt="Proof Task Photo">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>Proof Task Photo {{ $loop->iteration }}</h5>
                                    <p>Proof Task Photo {{ $loop->iteration }}</p>
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
    <div class="col-lg-6">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Rejected!</h4>
            <p>Proof Task has been rejected.</p>
            <hr>
            <p class="mb-0"><strong>Rejected Reason:</strong> {{ $proofTask->rejected_reason }}</p>
            <p><strong>Rejected Date:</strong> {{ date('d M Y h:i A', strtotime($proofTask->rejected_date)) }}</p>
        </div>
        <form class="forms-sample" id="editForm">
            @csrf
            <input type="hidden" id="proof_task_id" value="{{ $proofTask->id }}">
            <div class="mb-3">
                <label for="reviewed_reason" class="form-label">Reviewed Reason <span class="text-danger">* Required</span></label>
                <textarea class="form-control" id="reviewed_reason" name="reviewed_reason" rows="3" placeholder="Reviewed Reason"></textarea>
                <small class="text-warning d-block">
                    <strong>Note:</strong> Monthly free review limit is {{ get_default_settings('task_proof_monthly_free_review_time') }}. After that, you will be charged {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_proof_additional_review_charge') }} per review.
                </small>
                <span class="text-danger error-text update_reviewed_reason_error"></span>
            </div>
            <button type="submit" class="btn btn-primary">Reviewed</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();
            var id = $('#proof_task_id').val();
            var url = "{{ route('rejected.working_task.reviewed', ":id") }}";
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
                            toastr.error(response.error);
                        }else{
                            toastr.success('Proof Task Reviewed Successfully.')
                            $('#allDataTable').DataTable().ajax.reload();
                            $(".viewModal").modal('hide');
                        }
                    }
                },
            });
        })
    });
</script>

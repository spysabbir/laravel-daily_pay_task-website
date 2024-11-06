<div class="row">
    <div class="col-lg-7">
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
                                <a href="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" data-lightbox="gallery" data-title="Proof Task Photo {{ $loop->iteration }}">
                                    <img src="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" style="max-height: 400px;" class="d-block w-100" alt="Proof Task Photo {{ $loop->iteration }}">
                                </a>
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 class="mb-2"><strong class="badge bg-dark">Proof Task Photo {{ $loop->iteration }}</strong></h5>
                                    <strong><a href="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" target="_blank">View Full Image</a></strong>
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
    <div class="col-lg-5">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Rejected!</h4>
            <p>Proof Task has been rejected.</p>
            <hr>
            <p class="mb-0"><strong>Rejected Reason:</strong> {{ $proofTask->rejected_reason }}</p>
            @if ($proofTask->rejected_reason_photo)
                <strong>Rejected Reason Photo: </strong>
                <a href="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" target="_blank">
                    <img src="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" class="img-fluid" alt="Rejected Reason Photo">
                </a>
            @endif
            <p><strong>Rejected Date:</strong> {{ date('d M Y h:i A', strtotime($proofTask->rejected_at)) }}</p>
        </div>

        @if (Carbon\Carbon::parse($proofTask->rejected_at)->addHours(get_default_settings('task_proof_status_rejected_charge_auto_refund_time')) > now())
        <h4 class="mb-3">Reviewed Task</h4>
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Reviewed Time Limit!</h4>
            <p>You have to review this task within {{ get_default_settings('task_proof_status_rejected_charge_auto_refund_time') }} hours after that this task review not allowed. This task review time will expire at {{ date('d M Y h:i A', strtotime($proofTask->rejected_at) + (get_default_settings('task_proof_status_rejected_charge_auto_refund_time') * 3600)) }}.</p>
        </div>

        <form class="forms-sample" id="editForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="proof_task_id" value="{{ $proofTask->id }}">
            <div class="mb-3">
                <label for="reviewed_reason" class="form-label">Reviewed Reason <span class="text-danger">* Required</span></label>
                <textarea class="form-control" id="reviewed_reason" name="reviewed_reason" rows="3" placeholder="Reviewed Reason"></textarea>
                <small class="text-warning d-block mt-2">
                    <strong>Note:</strong> Monthly free review limit is {{ get_default_settings('task_proof_monthly_free_review_time') }}. After that, you will be charged {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_proof_additional_review_charge') }} per review.
                </small>
                <span class="text-danger error-text update_reviewed_reason_error"></span>
            </div>
            <div class="mb-3">
                <label for="reviewed_reason_photo" class="form-label">Reviewed Reason Photo <span class="text-info">* Optonal</span></label>
                <input type="file" class="form-control" id="reviewed_reason_photo" name="reviewed_reason_photo" accept=".jpg, .jpeg, .png">
                <small class="text-info d-block">The reviewed reason photo must be jpg, jpeg or png format and less than 2MB.</small>
                <span class="text-danger error-text update_reviewed_reason_photo_error"></span>
                <img id="reviewed_reason_photoPreview" class="mt-2 d-block" style="max-height: 200px; max-width: 200px; display: none;">
            </div>
            <button type="submit" class="btn btn-primary">Reviewed</button>
        </form>
        @else
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Time Expired!</h4>
            <p>Sorry, you can't review this task because the time limit has expired.</p>
        </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {

        // Photo Preview
        $(document).on('change', '#reviewed_reason_photo', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#reviewed_reason_photoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();
            // Disable the submit button to prevent multiple submissions
            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var id = $('#proof_task_id').val();
            var url = "{{ route('rejected.worked_task.reviewed', ":id") }}";
            url = url.replace(':id', id)
            var formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
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
                complete: function() {
                    // Re-enable the submit button after the request completes
                    submitButton.prop("disabled", false).text("Reviewed");
                }
            });
        })
    });
</script>

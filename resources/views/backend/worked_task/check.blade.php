<div class="row">
    <div class="col-lg-8">
        <div class="mb-3">
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
                <p><strong>Proof Id:</strong> {{ $proofTask->id }}</p>
                <p><strong>Submited Date:</strong>{{ $proofTask->created_at->format('d M, Y h:i A') }}</p>
            </div>
        </div>
        <div class="mb-3">
            <h4>User Information:</h4>
            <div class="mt-2 border p-2">
                <p><strong>User Id:</strong> {{ $proofTask->user->id }}</p>
                <p><strong>User Name:</strong> {{ $proofTask->user->name }}</p>
                <p><strong>User Ip:</strong> {{ $proofTask->user->userDetail->ip }}</p>
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
        @if ($proofTask->status == 'Reviewed')
        <div class="mt-3">
            <form class="forms-sample" id="editForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="proof_task_id" value="{{ $proofTask->id }}">
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
                    <div class="mb-3">
                        <label for="rejected_reason_photo" class="form-label">Rejected Reason Photo <span class="text-info">* Optonal</span></label>
                        <input type="file" class="form-control" id="rejected_reason_photo" name="rejected_reason_photo" accept=".jpg, .jpeg, .png">
                        <small class="text-info d-block">The rejected reason photo must be jpg, jpeg or png format and less than 2MB.</small>
                        <span class="text-danger error-text update_rejected_reason_photo_error"></span>
                        <img id="rejected_reason_photoPreview" class="mt-2 d-block" style="max-height: 200px; max-width: 200px; display: none;">
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

        // Photo Preview
        $(document).on('change', '#rejected_reason_photo', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#rejected_reason_photoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var id = $('#proof_task_id').val();
            var url = "{{ route('backend.worked_task_check_update', ":id") }}";
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
                    } else {
                        toastr.success('Proof Task has been updated successfully.');
                        $('#allDataTable').DataTable().ajax.reload();
                        $(".viewModal").modal('hide');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        })
    });
</script>

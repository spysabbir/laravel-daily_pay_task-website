<div class="row">
    <div class="col-lg-8">
        <p class="mb-2"><strong>Reviewed Reason:</strong> {{ $proofTask->reviewed_reason }}</p>
        <p class="mb-2"><strong>Reviewed Date:</strong> {{ date('d M Y h:i A', strtotime($proofTask->reviewed_at)) }}</p>
        @if (json_decode($proofTask->reviewed_reason_photos))
            <strong>Reviewed Reason Photos: </strong>
            <div id="single-lightgallery-reviewed" class="image-grid">
                @foreach (json_decode($proofTask->reviewed_reason_photos) as $photo)
                <a href="" class="" data-src="{{ asset('uploads/task_proof_reviewed_reason_photo') }}/{{ $photo }}" data-sub-html="<h4>Reviewed Reason Photo {{ $loop->iteration }}</h4>">
                    <img class="proof-image my-3" src="{{ asset('uploads/task_proof_reviewed_reason_photo') }}/{{ $photo }}" alt="Reviewed Reason Photo {{ $loop->iteration }}">
                </a>
                @endforeach
            </div>
        @else
            <p class="mb-2"><strong>Reviewed Reason Photos:</strong> Not submitted.</p>
        @endif
    </div>
    <div class="col-lg-4">
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
            var url = "{{ route('backend.worked_task.check_update', ":id") }}";
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
                        if (response.status == 401) {
                            $(".reviewedModal").modal('hide');
                            $('#allDataTable').DataTable().ajax.reload();
                            toastr.info(response.error);
                        } else {
                            toastr.success('Proof Task has been updated successfully.');
                            $('#allDataTable').DataTable().ajax.reload();
                            $(".reviewedModal").modal('hide');
                        }
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        })
    });
</script>

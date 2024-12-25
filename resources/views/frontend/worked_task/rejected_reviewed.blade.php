<div class="row">
    <div class="col-lg-12">
        @if (!$proofTask->reviewed_at)
            @if (Carbon\Carbon::parse($proofTask->rejected_at)->addHours((int) get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')) > now())
            <h4 class="mb-3">Reviewed Task</h4>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Reviewed Time Limit!</h4>
                <p>You have to review this task within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours after that this task review not allowed. This task review time will expire at {{ date('d M Y h:i A', strtotime($proofTask->rejected_at) + (get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') * 3600)) }}.</p>
            </div>

            <form class="forms-sample" id="editForm" enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="proof_task_id" value="{{ $proofTask->id }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="reviewed_reason" class="form-label">Reviewed Reason <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="reviewed_reason" name="reviewed_reason" rows="3" placeholder="Reviewed Reason"></textarea>
                        <small class="text-warning d-block mt-2">
                            <strong>Note:</strong> Explain why you think this task is correct. If you don't provide a valid reason, {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('rejected_worked_task_review_charge') }} will be charged from your account.
                        </small>
                        <span class="text-danger error-text update_reviewed_reason_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reviewed Reason Photos <span class="text-info">* Optional</span></label>
                        <div id="fileUploadContainer">
                            <div class="input-group mb-2 file-upload-row">
                                <input type="file" class="form-control reviewed_reason_photos" name="reviewed_reason_photos[]" accept=".jpg, .jpeg, .png">
                                <button type="button" class="btn btn-danger remove-file-btn">Remove</button>
                            </div>
                        </div>
                        <button type="button" id="addFileBtn" class="btn btn-secondary mb-2">Add More</button>
                        <small class="text-info d-block">Each file must be in jpg, jpeg, or png format and less than 2MB.</small>
                        <span class="text-danger error-text update_reviewed_reason_photos_error"></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Reviewed</button>
            </form>
            @else
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Time Expired!</h4>
                <p>Sorry, you can't review this task because the time limit has expired.</p>
            </div>
            @endif
        @else
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Reviewed!</h4>
                <p>Proof Task has been reviewed.</p>
                <hr>
                <p class="mb-0"><strong>Reviewed Reason:</strong> {{ $proofTask->reviewed_reason }}</p>
                @if ($proofTask->reviewed_reason_photos)
                    <strong>Reviewed Reason Photos: </strong>
                    @foreach (json_decode($proofTask->reviewed_reason_photos) as $photo)
                        <a href="{{ asset('uploads/task_proof_reviewed_reason_photo/' . $photo) }}" target="_blank">
                            <img src="{{ asset('uploads/task_proof_reviewed_reason_photo/' . $photo) }}" class="img-fluid mb-2" style="max-height: 200px;">
                        </a>
                    @endforeach
                @endif
                <p><strong>Reviewed Date:</strong> {{ date('d M Y h:i A', strtotime($proofTask->reviewed_at)) }}</p>
            </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        // Add new file input field
        $("#addFileBtn").click(function () {
            const fileInputRow = `
                <div class="input-group mb-2 file-upload-row">
                    <input type="file" class="form-control reviewed_reason_photos" name="reviewed_reason_photos[]" accept=".jpg, .jpeg, .png">
                    <button type="button" class="btn btn-danger remove-file-btn">Remove</button>
                </div>
            `;
            $("#fileUploadContainer").append(fileInputRow);
        });

        // Remove file input field
        $("body").on("click", ".remove-file-btn", function () {
            $(this).closest(".file-upload-row").remove();
        });

        // Form submission logic
        $("body").on("submit", "#editForm", function (e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            const id = $('#proof_task_id').val();

            $.ajax({
                url: "{{ route('rejected.worked_task.reviewed.send', ':id') }}".replace(':id', id),
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
                            toastr.error(response.error);
                        } else {
                            toastr.success('Proof Task Reviewed Successfully.')
                            $('#allDataTable').DataTable().ajax.reload();
                            $(".viewModal").modal('hide');
                        }
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Reviewed");
                }
            });
        });
    });
</script>

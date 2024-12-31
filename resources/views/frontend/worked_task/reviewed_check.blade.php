<div class="row">
    <div class="col-lg-12">
        @if (!$proofTask->reviewed_at)
            @if (Carbon\Carbon::parse($proofTask->rejected_at)->addHours((int) get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')) > now())
            <h4 class="mb-3">Reviewed Task</h4>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Reviewed Time Limit!</h4>
                <p>You have to review this task within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours after that this task review not allowed. Submitting this task for review will expire at {{ date('d M Y h:i A', strtotime($proofTask->rejected_at) + (get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') * 3600)) }}.</p>
                <small class="text-warning d-block mt-2">
                    <strong>Note:</strong> Explain why you think this task is correct. When you will send this task for review, {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('rejected_worked_task_review_charge') }} will be charged from your withdraw account balance. After submitting this task for review, you will get notification from us within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours. If you don't provide a valid reason for the review your charge will not be refunded and the task will be rejected. And if you provide a valid reason for the review your charge will be refunded and the task will be approved. Only one review is allowed for each task.
                </small>
            </div>
            <form class="forms-sample" id="editForm" enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="proof_task_id" value="{{ $proofTask->id }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="reviewed_reason" class="form-label">Reviewed Reason <span class="text-danger">* Required</span></label>
                        <textarea class="form-control" id="reviewed_reason" name="reviewed_reason" rows="3" placeholder="Reviewed Reason"></textarea>
                        <span class="text-danger error-text update_reviewed_reason_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reviewed Reason Photos <span class="text-info">* Optional</span></label>
                        <div id="fileUploadContainer">
                            <div class="mb-2 file-upload-row">
                                <div class="input-group">
                                    <input type="file" class="form-control reviewed_reason_photos" name="reviewed_reason_photos[]" accept=".jpg, .jpeg, .png">
                                    <button type="button" class="btn btn-danger remove-file-btn">Remove</button>
                                </div>
                                <span class="text-danger error-text update_reviewed_reason_photos_error d-block"></span>
                            </div>
                        </div>
                        <button type="button" id="addFileBtn" class="btn btn-secondary mb-2">Add More</button>
                        <small class="text-info d-block">Note: Each file must be in jpg, jpeg, or png format and less than 2MB and you can only add up to 10 file inputs.</small>
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
                @if ($proofTask->rejectedBy->user_type =='Backend')
                    <p><strong class="text-danger">Your worked task has been finally rejected by the Admin.</strong></p>
                @endif
                <hr>
                <p class="mb-2"><strong>Reviewed Id:</strong> <span class="badge bg-info">{{ $proofTask->postTask->id }}#{{ $proofTask->id }}</span></p>
                <p class="mb-2"><strong>Reviewed Reason:</strong> {{ $proofTask->reviewed_reason }}</p>
                <p class="mb-2"><strong>Reviewed Date:</strong> {{ date('d M Y h:i A', strtotime($proofTask->reviewed_at)) }}</p>
                @if ($proofTask->reviewed_reason_photos)
                    <strong>Reviewed Reason Photos: </strong>
                    <div id="single-lightgallery" class="image-grid">
                        @foreach (json_decode($proofTask->reviewed_reason_photos) as $photo)
                        <a href="" class="" data-src="{{ asset('uploads/task_proof_reviewed_reason_photo') }}/{{ $photo }}" data-sub-html="<h4>Reviewed Reason Photo {{ $loop->iteration }}</h4>">
                            <img class="proof-image my-3" src="{{ asset('uploads/task_proof_reviewed_reason_photo') }}/{{ $photo }}" alt="Reviewed Reason Photo {{ $loop->iteration }}">
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        // Add new file input field
        const maxFileInputs = 10;
        $("#addFileBtn").click(function () {
            const fileInputCount = $("#fileUploadContainer .file-upload-row").length;

            if (fileInputCount >= maxFileInputs) {
                toastr.warning("You can only add up to 10 file inputs."); // Show toastr warning
                return; // Prevent adding more file inputs
            }

            const fileInputRow = `
                <div class="mb-2 file-upload-row">
                    <div class="input-group mb-2">
                        <input type="file" class="form-control reviewed_reason_photos" name="reviewed_reason_photos[]" accept=".jpg, .jpeg, .png">
                        <button type="button" class="btn btn-danger remove-file-btn">Remove</button>
                    </div>
                    <span class="text-danger error-text d-block"></span>
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

            const submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            const id = $('#proof_task_id').val();

            $.ajax({
                url: "{{ route('worked_task.reviewed.send', ':id') }}".replace(':id', id),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status === 400) {
                        $.each(response.error, function (prefix, val) {
                            if (prefix.startsWith("reviewed_reason_photos_")) {
                                // Extract the index from the error key
                                const index = parseInt(prefix.split("_")[3], 10); // Parse the index

                                // Find the corresponding file input row
                                const fileInputRow = $("#fileUploadContainer")
                                    .find(".file-upload-row")
                                    .eq(index); // Select the row by index

                                if (fileInputRow.length > 0) {
                                    if (index === 0) {
                                        // Display the error message in the error-text span
                                        $('span.update_reviewed_reason_photos_error').text(val);
                                    }
                                    // Display the error message in the error-text span
                                    fileInputRow.find("span.error-text").text(val);
                                }
                            } else {
                                // Handle other field errors
                                $('span.update_' + prefix + '_error').text(val);
                            }
                        });
                    } else if (response.status === 401) {
                        toastr.error(response.error);
                    } else {
                        toastr.success('Proof Task Reviewed Successfully.');
                        $('#allDataTable').DataTable().ajax.reload();
                        $(".reviewedModal").modal('hide');
                    }
                },
                complete: function () {
                    submitButton.prop("disabled", false).text("Reviewed");
                }
            });
        });
    });
</script>

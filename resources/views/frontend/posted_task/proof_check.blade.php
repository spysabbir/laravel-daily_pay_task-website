<div class="row">
    <marquee class="mb-3">
        <strong class="text-danger">Warning: If you do not approve or reject the Task Proof within {{ get_default_settings('posted_task_proof_submit_auto_approved_time') }} hours of submitting the worker Task Proof, the Task Proof will be automatically approved. If you reject the task proof then worker can send requesting to us for reviewing task proof within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours. Admin will check the proof and if it is correct then the worker will be paid or if the proof is wrong then the worker will not be paid. Because of this, only Rejected money will be on hold for {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours because you Proof Rejected. If the worker does not request for review within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours, your money will be automatically add your deposit balance after task work duration expire. If the worker request for review within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours, the admin will review the proof within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours. After review checking workers will get feedback notification from admin panel. Please be careful and work well with integrity and don't intentionally reject someone's work.</strong>
    </marquee>
    <div class="col-lg-8">
        <div class="mb-3 border p-2">
            <h4 class="mb-2">Post Task Additional Note:</h4>
            <div>
                {!! nl2br(e($proofTask->postTask->additional_note)) !!}
            </div>
        </div>
        <div class="mb-3 border p-2">
            <h4 class="mb-2">Proof Answer:</h4>
            <div>
                {!! nl2br(e($proofTask->proof_answer)) !!}
            </div>
        </div>
        @if (!json_decode($proofTask->proof_photos))
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading text-center">This task does not require any proof photo.</h4>
            </div>
        @else
        <div class="mb-3">
            <h4 class="mb-2">Proof Image:</h4>
            <div id="single-lightgallery" class="image-grid">
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
        @if ($proofTask->status == 'Approved')
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Approved!</h4>
                <p>Proof Task has been approved.</p>
                <hr>
                <h5>
                    <strong>Rating:</strong>
                    @if (!$rating)
                        <span class="text-danger">Not Rated</span>
                    @else
                        @for ($i = 0; $i < $rating->rating; $i++)
                            <i class="fa-solid fa-star text-warning"></i>
                        @endfor
                    @endif
                </h5>
                <h5>
                    <strong>Bonus:</strong>
                    @if (!$bonus)
                        <span class="text-danger">No Bonus</span>
                    @else
                    {{ get_site_settings('site_currency_symbol') }} {{ $bonus->amount }}
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
            <div class="mt-3">
                <strong>Note: </strong>
                <p class="text-info">Proof Task has been reviewed by Admin and waiting for your action. Please with for Admin's decision. If you have any query, please contact with Admin.</p>
            </div>
        @else
            <div>
                <h4>Update Proof Task Status:</h4>
                <form class="forms-sample border mt-2 p-2" id="proofCheckEditForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="proof_task_id" value="{{ $proofTask->id }}">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">* Required</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="status" id="proof_check_approve" value="Approved">
                                <label class="form-check-label" for="proof_check_approve">
                                    Approved
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="status" id="proof_check_reject" value="Rejected">
                                <label class="form-check-label" for="proof_check_reject">
                                    Rejected
                                </label>
                            </div>
                        </div>
                        <span class="text-danger error-text update_status_error"></span>
                    </div>
                    <div id="proof_check_approved_div">
                        <div class="mb-3 proof_check_rating">
                            <label for="proof_check_rating" class="form-label">Rating (1-5) <span class="text-info">* Optonal</span></label>
                            <div class="rating-box">
                                <div class="stars">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                            <input type="hidden" name="rating" id="proof_check_rating" min="0" max="5">
                            <span class="text-danger error-text update_rating_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="proof_check_bonus" class="form-label">Bonus <span class="text-info">* Optonal</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="proof_check_bonus" name="bonus" min="0" max="{{ get_default_settings('posted_task_proof_submit_user_max_bonus_amount') }}" placeholder="Bonus">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">The bonus field must not be greater than {{ get_default_settings('posted_task_proof_submit_user_max_bonus_amount') }} {{ get_site_settings('site_currency_symbol') }}.</small>
                            <span class="text-danger error-text update_bonus_error"></span>
                        </div>
                    </div>
                    <div id="proof_check_rejected_div">
                        <div class="mb-3">
                            <label for="proof_check_rejected_reason" class="form-label">Rejected Reason <span class="text-danger">* Required</span></label>
                            <textarea class="form-control" id="proof_check_rejected_reason" name="rejected_reason" rows="3" placeholder="Rejected Reason"></textarea>
                            <span class="text-danger error-text update_rejected_reason_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="proof_check_rejected_reason_photo" class="form-label">Rejected Reason Photo <span class="text-info">* Optonal</span></label>
                            <input type="file" class="form-control" id="proof_check_rejected_reason_photo" name="rejected_reason_photo" accept=".jpg, .jpeg, .png">
                            <small class="text-info d-block">The rejected reason photo must be jpg, jpeg or png format and less than 2MB.</small>
                            <span class="text-danger error-text update_rejected_reason_photo_error"></span>
                            <img id="proof_check_rejected_reason_photoPreview" class="mt-2 d-block" style="max-height: 200px; max-width: 200px; display: none;">
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
        // Rating stars
        const stars = document.querySelectorAll(".proof_check_rating .stars i");
        const ratingInput = document.getElementById('proof_check_rating');
        stars.forEach((star, index1) => {
            star.addEventListener("click", () => {
                stars.forEach((star, index2) => {
                    index1 >= index2 ? star.classList.add("active") : star.classList.remove("active");
                });
                ratingInput.value = index1 + 1;
            });
            star.addEventListener("dblclick", () => {
                stars.forEach((star) => {
                    star.classList.remove("active");
                });
                ratingInput.value = 0;
            });
        });

        // Hide rejected reason div initially
        $('#proof_check_approved_div').hide();
        $('#proof_check_rejected_div').hide();
        $('input[name="status"]').change(function() {
            $('.update_status_error').text('');
            if ($(this).val() == 'Rejected') {
                $('#proof_check_approved_div').hide();
                $('#proof_check_rejected_div').show();
                $('#proof_check_bonus').val(0);
                // Reset rating stars
                stars.forEach((star) => {
                    star.classList.remove("active");
                });
                ratingInput.value = 0;
            } else {
                $('#proof_check_approved_div').show();
                $('#proof_check_rejected_div').hide();
                $('#proof_check_rejected_reason').val('');
            }
        });

        // Photo Preview
        $(document).on('change', '#proof_check_rejected_reason_photo', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#proof_check_rejected_reason_photoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Update Data
        $("body").on("submit", "#proofCheckEditForm", function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var id = $('#proof_task_id').val();
            var url = "{{ route('proof_task.check.update', ':id') }}".replace(':id', id);
            var formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status === 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.update_' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        $("#deposit_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                        $("#withdraw_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.withdraw_balance);
                        toastr.success('Proof Task has been updated successfully.');
                        $('#allDataTable').DataTable().ajax.reload();
                        $(".viewSingleTaskProofModal").modal('hide');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>

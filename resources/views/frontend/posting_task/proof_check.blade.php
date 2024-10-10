<div class="row">
    <div class="col-lg-8">
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
    <div class="col-lg-4">
        <div class="mb-3">
            <h4>User Information:</h4>
            <div class="mt-2 border p-2">
                <p><strong>User Id:</strong> {{ $proofTask->user->id }}</p>
                <p><strong>User Name:</strong> {{ $proofTask->user->name }}</p>
                <p><strong>User Email:</strong> {{ $proofTask->user->email }}</p>
                <p><strong>User Ip:</strong> {{ $proofTask->user->userDetail->ip }}</p>
            </div>
        </div>
        @if ($proofTask->status == 'Approved')
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Approved!</h4>
                <p>Proof Task has been approved.</p>
                <hr>
                <h5><strong>Rating:</strong> {{ $proofTask->rating->rating ?? 'N/A' }} <i class="fa-solid fa-star text-warning"></i></h5>
                <h5><strong>Bonus:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $proofTask->bonus->amount ?? 'N/A' }}</h5>
                <hr>
                <p>Approved At: {{ date('d M, Y h:i A', strtotime($proofTask->approved_at)) }}</p>
                <p>Approved By: {{ $proofTask->approvedBy->name }}</p>
            </div>
        @elseif ($proofTask->status == 'Rejected')
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Rejected!</h4>
                <p>Proof Task has been rejected.</p>
                <hr>
                <p><strong>Rejected Reason:</strong> {{ $proofTask->rejected_reason }}</p>
                <p>Rejected At: {{ date('d M, Y h:i A', strtotime($proofTask->rejected_at)) }}</p>
                <p>Rejected By: {{ $proofTask->rejectedBy->name }}</p>
            </div>
        @elseif ($proofTask->status == 'Reviewed')
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Rejected!</h4>
                <p>Proof Task has been rejected.</p>
                <hr>
                <p><strong>Rejected Reason:</strong> {{ $proofTask->rejected_reason }}</p>
                <p>Rejected At: {{ date('d M, Y h:i A', strtotime($proofTask->rejected_at)) }}</p>
                <p>Rejected By: {{ $proofTask->rejectedBy->name }}</p>
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
                <form class="forms-sample border mt-2 p-2" id="editForm">
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
                    <div id="approved_div">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating (1-5) <span class="text-info">* Optonal</span></label>
                            <div class="rating-box">
                                <div class="stars">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                            <input type="hidden" name="rating" id="rating" min="0" max="5">
                            <span class="text-danger error-text update_rating_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="bonus" class="form-label">Bonus <span class="text-info">* Optonal</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="bonus" name="bonus" min="0" max="{{ get_default_settings('task_proof_max_bonus_amount') }}" placeholder="Bonus">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">The bonus field must not be greater than {{ get_default_settings('task_proof_max_bonus_amount') }} {{ get_site_settings('site_currency_symbol') }}.</small>
                            <span class="text-danger error-text update_bonus_error"></span>
                        </div>
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
        // Rating stars
        const stars = document.querySelectorAll(".stars i");
        const ratingInput = document.getElementById('rating');
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
        $('#approved_div').hide();
        $('#rejected_div').hide();
        $('input[name="status"]').change(function() {
            $('.update_status_error').text('');
            if ($(this).val() == 'Rejected') {
                $('#approved_div').hide();
                $('#rejected_div').show();
                $('#bonus').val(0);
                // Reset rating stars
                stars.forEach((star) => {
                    star.classList.remove("active");
                });
                ratingInput.value = 0;
            } else {
                $('#approved_div').show();
                $('#rejected_div').hide();
                $('#rejected_reason').val('');
            }
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e) {
            e.preventDefault();

            // Disable the submit button to prevent multiple submissions
            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var id = $('#proof_task_id').val();
            var url = "{{ route('running.posting_task.proof.check.update', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: "PUT",
                data: $(this).serialize(),
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status === 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.update_' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        if (response.status === 401) {
                            toastr.error(response.error);
                        } else {
                            // Update balance displays
                            $("#deposit_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                            $("#withdraw_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.withdraw_balance);
                            toastr.success('Proof Task has been updated successfully.');
                            $('#allDataTable').DataTable().ajax.reload();
                            $(".viewModal").modal('hide');
                        }
                    }
                },
                complete: function() {
                    // Re-enable the submit button after the request completes
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>

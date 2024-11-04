@extends('layouts.template_master')

@section('title', 'Task Details')

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Task Details</h3>
                <div class="d-flex align-items-center">
                    <h4>Proof Submitted: </h4>
                    <div class="progress mx-1" style="width: 250px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $proofCount == 0 ? 'primary' : 'success' }}" role="progressbar" style="width: {{ $proofCount == 0 ? 100 : round(($proofCount / $taskDetails->work_needed) * 100, 2) }}%" aria-valuenow="{{ $proofCount }}" aria-valuemin="0" aria-valuemax="{{ $taskDetails->work_needed }}">{{ $proofCount }}/{{ $taskDetails->work_needed }}</div>
                    </div>
                </div>
                <h4 class="mx-2">Earnings From Work: <strong class="badge bg-primary">{{ get_site_settings('site_currency_symbol') }} {{ $taskDetails->earnings_from_work }}</strong></h4>
            </div>
            <div class="card-body">
                <h3 class="mb-3">Title: <span class="text-info">{{ $taskDetails->title }}</span></h3>
                @if ($taskDetails->thumbnail)
                <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $taskDetails->thumbnail }}" alt="Thumbnail image for {{ $taskDetails->title }}" class="img-fluid my-3">
                @endif
                <div class="my-2 border p-3 rounded">
                    <div class="border p-2 rounded bg-dark d-flex align-items-center justify-content-between">
                        <div class="p-2">
                            <p>Category: {{ $taskDetails->category->name }}</p>
                            <p>Sub Category: {{ $taskDetails->subcategory->name }}</p>
                            @if ($taskDetails->child_category_id)
                            <p>Child Category: {{ $taskDetails->childcategory->name }}</p>
                            @endif
                        </div>
                        <div>
                            <p>Approved Date: {{ date('d F, Y  H:i A', strtotime($taskDetails->approved_at)) }}</p>
                            <p>Work Duration: {{ $taskDetails->work_duration }} Days</p>
                        </div>
                        <div>
                            <!-- Report Post Task Button -->
                            <button type="button" class="btn btn-info btn-sm d-flex align-items-center mx-2" data-bs-toggle="modal" data-bs-target=".reportPostTaskModal">
                                <i class="icon-md" data-feather="message-circle"></i>
                                <span class="d-none d-md-block ms-1">Report Post Task</span>
                            </button>
                            <!-- Report Post Task Modal -->
                            <div class="modal fade reportPostTaskModal" tabindex="-1" aria-labelledby="reportPostTaskModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reportPostTaskModalLabel">Report Post Task</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        @if ($reportPostTask)
                                        <div class="modal-body">
                                            @if ($reportPostTask->status == 'Pending')
                                            <div class="alert alert-warning">
                                                <strong>Post Task already reported! Please wait for the admin's response.</strong>
                                            </div>
                                            @else
                                            <div class="alert alert-success">
                                                <strong>Post Task report has been resolved! Please check the report panel for more details.</strong>
                                            </div>
                                            @endif
                                            <div>
                                                <p>Report Reason: {{ $reportPostTask->reason }}</p>
                                                <p>Report Date: {{ date('d F, Y  H:i A', strtotime($reportPostTask->created_at)) }}</p>
                                                @if ($reportPostTask->photo)
                                                <p>Report Photo:</p>
                                                <a href="{{ asset('uploads/report_photo') }}/{{ $reportPostTask->photo }}" target="_blank">
                                                    <img src="{{ asset('uploads/report_photo') }}/{{ $reportPostTask->photo }}" alt="Report photo" class="img-fluid my-2" style="width: 180px; height: 180px">
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                        @else
                                        <form class="forms-sample" id="reportPostTaskForm" action="{{ route('report.send', $taskDetails->user_id) }}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="post_task_id" value="{{ $taskDetails->id }}">
                                            <input type="hidden" name="type" value="Post Task">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="reason" name="reason" placeholder="Reason"></textarea>
                                                    <span class="text-danger error-text reason_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="reportPostTaskPhoto" class="form-label">Photo</label>
                                                    <input type="file" class="form-control" id="reportPostTaskPhoto" name="photo" accept=".jpg, .jpeg, .png">
                                                    <span class="text-danger error-text photo_error d-block"></span>
                                                    <img src="" alt="Photo" id="reportPostTaskPhotoPreview" class="mt-2" style="display: none; width: 100px; height: 100px;">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Report</button>
                                            </div>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="my-2 border p-2 rounded">
                        <h4 class="text-primary">Description</h4>
                        <p>{{ $taskDetails->description }}</p>
                    </div>
                    <div class="border p-2 rounded">
                        <h4 class="text-primary">Required Proof</h4>
                        <p>{{ $taskDetails->required_proof }}</p>
                    </div>
                </div>
                <hr>
                <h3 class="mb-3">Submit Proof</h3>
                @if ($proofCount < $taskDetails->work_needed)
                <div class="my-2 border p-3 rounded bg-dark">
                    @if ($taskProofExists)
                    <div class="alert alert-warning">
                        <strong>Proof already submitted!</strong>
                    </div>
                    @else
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="proofTaskForm" action="{{ route('find_task.proof.submit', encrypt($taskDetails->id)) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="proof_answer" class="form-label">Proof Answer <small class="text-danger">* Required </small></label>
                            <textarea class="form-control" id="proof_answer" name="proof_answer" rows="5" placeholder="Write your answer here">{{ old('proof_answer') }}</textarea>
                            <span class="text-danger error-message" id="proof_answer_error"></span>
                            @if ($errors->has('proof_answer'))
                                <span class="text-danger">{{ $errors->first('proof_answer') }}</span>
                            @endif
                        </div>

                        @for ($i = 0; $i < $taskDetails->extra_screenshots + 1; $i++)
                            <div class="mb-3">
                                <label for="proof_photo_{{ $i }}" class="form-label">Proof Photo {{ $i + 1 }} <small class="text-danger">* Required </small></label>
                                <input class="form-control" type="file" id="proof_photo_{{ $i }}" name="proof_photos[]" accept=".jpg, .jpeg, .png">
                                <small class="text-info">* Only jpeg, jpg, png files are allowed. File size must be less than 2 MB.</small>
                                <span class="text-danger error-message d-block" id="proof_photo_{{ $i }}_error"></span>
                                <img src="" alt="Proof Photo {{ $i + 1 }}" class="img-fluid my-2" id="proof_photo_preview_{{ $i }}" style="display: none; width: 180px; height: 180px">
                                @if ($errors->has('proof_photos.' . $i))
                                    <span class="text-danger">{{ $errors->first('proof_photos.' . $i) }}</span>
                                @endif
                            </div>
                        @endfor

                        @if ($errors->has('proof_photos'))
                            <div class="mb-3">
                                <span class="text-danger">{{ $errors->first('proof_photos') }}</span>
                            </div>
                        @endif

                        <div class="my-2 p-2 d-flex align-items-center justify-content-end bg-black">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                    @endif
                </div>
                @else
                <div class="alert alert-warning">
                    <strong>Sorry! This task has been completed.</strong>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-center">
                <h3 class="card-title">Buyer Details</h3>
            </div>
            <div class="card-body">
                <div class="border p-3 text-center mb-3">
                    <img src="{{ asset('uploads/profile_photo') }}/{{ $taskDetails->user->profile_photo }}" alt="Profile photo for {{ $taskDetails->user->name }}" class="mb-3 rounded-circle" width="150px" height="150px">
                    <p>Name: <a href="{{ route('user.profile', encrypt($taskDetails->user->id)) }}" class="text-info">{{ $taskDetails->user->name }}</a></p>
                    <p>Last Active: <span class="text-info">{{ Carbon\Carbon::parse($taskDetails->user->last_login_at)->diffForHumans() }}</span></p>
                    <p>Join Date: <span class="text-info">{{ $taskDetails->user->created_at->format('d M, Y') }}</span></p>
                    <p>Bio: {{ $taskDetails->user->bio ?? 'N/A' }}</p>
                </div>
                <div class="d-flex align-items-center justify-content-between border p-3">
                    <!-- Report User Button -->
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center mx-2" data-bs-toggle="modal" data-bs-target=".reportModal">
                        <i class="icon-md" data-feather="message-circle"></i>
                        <span class="d-none d-md-block ms-1">Report User</span>
                    </button>
                    <!-- Report User Modal -->
                    <div class="modal fade reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reportModalLabel">Report User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="reportForm" action="{{ route('report.send', $taskDetails->user->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="User">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="reason" name="reason" placeholder="Reason"></textarea>
                                            <span class="text-danger error-text reason_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="photo" class="form-label">Photo</label>
                                            <input type="file" class="form-control" id="photo" name="photo" accept=".jpg, .jpeg, .png">
                                            <span class="text-danger error-text photo_error d-block"></span>
                                            <img src="" alt="Photo" id="photoPreview" class="mt-2" style="display: none; width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Block User -->
                    <a href="{{ route('block.unblock.user', $taskDetails->user->id) }}" class="btn btn-{{ $blocked ? 'danger' : 'warning' }} btn-sm d-flex align-items-center">
                        <i class="icon-md" data-feather="shield"></i>
                        <span class="d-none d-md-block ms-1">
                            {{ $blocked ? 'Unblock User' : 'Block User' }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // AJAX Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Client-side validation
        $('#proofTaskForm').submit(function(event) {
            event.preventDefault(); // Prevent default form submission

            let isValid = true;
            $('.error-message').text(''); // Clear previous error messages

            // Check Proof Answer
            if ($('#proof_answer').val().trim() === '') {
                $('#proof_answer_error').text('Proof answer is required.');
                isValid = false;
            }

            // Check Proof Photos
            @for ($i = 0; $i < $taskDetails->extra_screenshots + 1; $i++)
                if ($('#proof_photo_{{ $i }}').val() === '') {
                    $('#proof_photo_{{ $i }}_error').text('Proof photo {{ $i + 1 }} is required.');
                    isValid = false;
                } else {
                    let fileExtension = ['jpeg', 'jpg', 'png'];
                    if ($.inArray($('#proof_photo_{{ $i }}').val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        $('#proof_photo_{{ $i }}_error').text('Only jpeg, jpg, png files are allowed.');
                        isValid = false;
                    }
                    if ($('#proof_photo_{{ $i }}')[0].files[0].size > 2097152) {
                        $('#proof_photo_{{ $i }}_error').text('File size must be less than 2 MB.');
                        isValid = false;
                    }
                }
            @endfor

            if (!isValid) {
                return; // Stop form submission if validation fails
            }

            // Disable the submit button to prevent multiple submissions
            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            // Perform AJAX check for proofCount vs work_needed before submitting the form
            $.ajax({
                url: '{{ route("find_task.proof.submit.limit.check", encrypt($taskDetails->id)) }}', // Adjust with your actual route
                type: 'GET',
                success: function(response) {
                    if (response.canSubmit) {
                        // If the proof count is below the work_needed, submit the form
                        $('#proofTaskForm')[0].submit();
                    } else {
                        // Prevent form submission and show an error if limit is reached
                        toastr.error('Sorry! This task has been completed.');
                    }
                },
                complete: function() {
                    // Re-enable the submit button after the request completes
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        $('textarea[name="proof_answer"]').on('input', function() {
            var proof_answer = $(this).val().trim();
            if (proof_answer.length > 0) {
                $('#proof_answer_error').text(''); // Remove error proof answer when input is valid
            }else{
                $('#proof_answer_error').text('Proof answer is required.');
            }
        });

        // Preview proof photos
        @for ($i = 0; $i < $taskDetails->extra_screenshots + 1; $i++)
            document.getElementById('proof_photo_{{ $i }}').addEventListener('change', function() {
                const file = this.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (file && allowedTypes.includes(file.type)) {
                    if (file.size > maxSize) {
                        $('#proof_photo_{{ $i }}_error').text('File size is too large. Max size is 2MB.');
                        this.value = ''; // Clear file input
                        // Hide preview image
                        $('#proof_photo_preview_{{ $i }}').hide();
                    } else {
                        $('#proof_photo_{{ $i }}_error').text('');
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#proof_photo_preview_{{ $i }}').attr('src', e.target.result).show();
                        };
                        reader.readAsDataURL(file);
                    }
                } else {
                    $('#proof_photo_{{ $i }}_error').text('Please select a valid image file (jpeg, jpg, png).');
                    this.value = ''; // Clear file input
                    // Hide preview image
                    $('#proof_photo_preview_{{ $i }}').hide();
                }
            });
        @endfor

        // Report User Photo Preview
        $(document).on('change', '#photo', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#photoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });
        // Report Post Task Photo Preview
        $(document).on('change', '#reportPostTaskPhoto', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#reportPostTaskPhotoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Unified form submission handler
        $('#reportForm, #reportPostTaskForm').submit(function(event) {
            event.preventDefault();

            // Disable the submit button to prevent multiple submissions
            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var form = $(this); // Get the current form being submitted
            var formData = new FormData(form[0]); // Create FormData from the form

            $.ajax({
                url: form.attr('action'), // Dynamically get the action URL from the form
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    form.find('span.error-text').text(''); // Clear previous error messages inside this form
                },
                success: function(response) {
                    if (response.status === 400) {
                        // Loop through the errors and display them
                        $.each(response.error, function(prefix, val) {
                            form.find('span.' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        // Hide the appropriate modal
                        if (form.is('#reportForm')) {
                            $('.reportModal').modal('hide'); // Hide Report User modal
                            toastr.success('User reported successfully.');
                        } else if (form.is('#reportPostTaskForm')) {
                            $('.reportPostTaskModal').modal('hide'); // Hide Report Post Task modal
                            toastr.success('Post Task reported successfully.');
                        }

                        form[0].reset(); // Reset the form after successful submission
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
@endsection

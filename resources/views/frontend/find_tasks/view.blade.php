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
                            <p>Running Day: {{ $taskDetails->running_day }} Days</p>
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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($taskProofExists)
                    <div class="alert alert-warning">
                        <strong>Proof already submitted!</strong>
                    </div>
                    @else
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
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center mx-2" data-bs-toggle="modal" data-bs-target=".reportModal">
                        <i class="icon-md" data-feather="message-circle"></i>
                        <span class="d-none d-md-block ms-1">Report User</span>
                    </button>
                    <!-- Report Modal -->
                    <div class="modal fade reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reportModalLabel">Report</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="reportForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="reason" class="form-label">Reason</label>
                                            <textarea class="form-control" id="reason" name="reason" placeholder="Reason"></textarea>
                                            <span class="text-danger error-text reason_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="photo" class="form-label">Photo</label>
                                            <input type="file" class="form-control" id="photo" name="photo">
                                            <span class="text-danger error-text photo_error"></span>
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
                    <a href="{{ route('block_user', $taskDetails->user->id) }}" class="btn btn-{{ $blocked ? 'danger' : 'warning' }} btn-sm d-flex align-items-center">
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
        // Client-side validation
        $('#proofTaskForm').submit(function(event) {
            let isValid = true;
            $('.error-message').text('');

            if ($('#proof_answer').val().trim() === '') {
                $('#proof_answer_error').text('Proof answer is required.');
                isValid = false;
            }

            @for ($i = 0; $i < $taskDetails->extra_screenshots + 1; $i++)
                if ($('#proof_photo_{{ $i }}').val() === '') {
                    $('#proof_photo_{{ $i }}_error').text('Proof photo {{ $i + 1 }} is required.');
                    isValid = false;
                }else{
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

            if(isValid){
                if ({{ $proofCount }} >= {{ $taskDetails->work_needed }}) {
                    toastr.error('Sorry! This task has been completed.');
                    isValid = false;
                }
            }

            if (!isValid) {
                event.preventDefault();
            }
        });

        // Preview proof photos
        @for ($i = 0; $i < $taskDetails->extra_screenshots + 1; $i++)
            $('#proof_photo_{{ $i }}').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#proof_photo_preview_{{ $i }}').attr('src', e.target.result).css('display', 'block');
                }
                reader.readAsDataURL(this.files[0]);
            });
        @endfor

        // Report User
        $('#reportForm').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('report_user', $taskDetails->user->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.'+prefix+'_error').text(val[0]);
                        });
                    } else {
                        $('.reportModal').modal('hide');
                        $('#reportForm')[0].reset();
                        toastr.success('User reported successfully.');
                    }
                }
            });
        });
    });
</script>
@endsection

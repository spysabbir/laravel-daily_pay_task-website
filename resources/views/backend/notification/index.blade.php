@extends('layouts.template_master')

@section('title', 'Notification')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div class="text">
                    <h3 class="card-title">Notification List</h3>
                </div>
                <h3>Total: <span id="total_notification_count">0</span></h3>
                <div class="action-btn">
                    {{-- @can('send.notification') --}}
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".createModel"><i data-feather="send"></i> Send Notification</button>
                    {{-- @endcan --}}
                    <!-- Create Modal -->
                    <div class="modal fade createModel select2Model" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModelLabel">Send Notification</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="createForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                                <select class="form-select" id="type" name="type">
                                                    <option value="">Select Type</option>
                                                    <option value="All Employee">All Employee</option>
                                                    <option value="Single Employee">Single Employee</option>
                                                    <option value="All User">All User</option>
                                                    <option value="Single User">Single User</option>
                                                </select>
                                                <span class="text-danger error-text type_error"></span>
                                            </div>
                                            <div class="col-lg-6 mb-3 d-none" id="userDiv">
                                                <label for="user_id" class="form-label">User Name <span class="text-danger">*</span></label>
                                                <select class="form-select js-select2-single" id="user_id" name="user_id" data-width="100%">
                                                    <option value="">-- Select User --</option>
                                                    @foreach ($allUser as $user)
                                                        <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger error-text user_id_error"></span>
                                            </div>
                                            <div class="col-lg-6 mb-3 d-none" id="employeeDiv">
                                                <label for="employee_id" class="form-label">Employee Name <span class="text-danger">*</span></label>
                                                <select class="form-select js-select2-single" id="employee_id" name="user_id" data-width="100%">
                                                    <option value="">-- Select Employee --</option>
                                                    @foreach ($allEmployee as $user)
                                                        <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger error-text user_id_error"></span>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title" placeholder="Title">
                                                <span class="text-danger error-text title_error"></span>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="message" name="message" placeholder="Message"></textarea>
                                                <span class="text-danger error-text message_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Send</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Select type change event for user and employee select option show and hide
        $('#type').change(function () {
            var type = $(this).val();
            if (type === 'Single User') {
                $('#userDiv').removeClass('d-none');
                $('#employeeDiv').addClass('d-none');
            } else if (type === 'Single Employee') {
                $('#employeeDiv').removeClass('d-none');
                $('#userDiv').addClass('d-none');
            } else {
                $('#userDiv').addClass('d-none');
                $('#employeeDiv').addClass('d-none');
            }
        });



        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('backend.send.notification') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        $('.createModel').modal('hide');
                        $('#createForm')[0].reset();
                        toastr.success('Notification Send Successfully');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>
@endsection

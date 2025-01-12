@extends('layouts.template_master')

@section('title', 'Send Notification')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div class="text">
                    <h3 class="card-title">Send Notification List</h3>
                </div>
                <h3>Read: <span id="read_notifications_count">0</span>, Unread: <span id="unread_notifications_count">0</span></h3>
                <div class="action-btn">
                    @can('send.notification.store')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".createModel"><i data-feather="send"></i> Send Notification</button>
                    @endcan
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
                                            <div class="col-lg-6 mb-3">
                                                <div class="d-none" id="userDiv">
                                                    <label for="user_id" class="form-label">User Name <span class="text-danger">*</span></label>
                                                    <select class="form-select js-select2-single" id="user_id" data-width="100%">
                                                        <option value="">-- Select User --</option>
                                                        @foreach ($allUser as $user)
                                                            <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="d-none" id="employeeDiv">
                                                    <label for="employee_id" class="form-label">Employee Name <span class="text-danger">*</span></label>
                                                    <select class="form-select js-select2-single" id="employee_id" data-width="100%">
                                                        <option value="">-- Select Employee --</option>
                                                        @foreach ($allEmployee as $user)
                                                            <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input type="hidden" id="user_id_hidden" name="user_id" value="">
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
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_status" class="form-label">Status</label>
                                <select class="form-select filter_data" id="filter_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="Unread">Unread</option>
                                    <option value="Read">Read</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="All Employee">All Employee</option>
                                    <option value="All User">All User</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User or Employee Id</label>
                                <input type="number" class="form-control filter_data" id="filter_user_id" placeholder="User or Employee Id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Type</th>
                                <th>User ID</th>
                                <th>User Name</th>
                                <th>Title</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">View</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="modalBody">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tbody>
                    </table>
                </div>
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

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.send.notification') }}",
                type: 'GET',
                data: function (d) {
                    d.status = $('#filter_status').val();
                    d.user_id = $('#filter_user_id').val();
                    d.type = $('#filter_type').val();
                },
                dataSrc: function (json) {
                    // Update total notification count
                    $('#read_notifications_count').text(json.readNotificationsCount);
                    $('#unread_notifications_count').text(json.unreadNotificationsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'type', name: 'type' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'title', name: 'title' },
                { data: 'created_at', name: 'created_at'},
                { data: 'status', name: 'status'},
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // User Type Change Event
        $('#type').on('change', function () {
            const selectedType = $(this).val();
            $('#userDiv, #employeeDiv').addClass('d-none');
            $('#user_id, #employee_id').prop('disabled', true).val('').trigger('change');
            $('#user_id_hidden').val('');
            $('.user_id_error').text('');

            if (selectedType === 'Single User') {
                $('#userDiv').removeClass('d-none');
                $('#user_id').prop('disabled', false);
            } else if (selectedType === 'Single Employee') {
                $('#employeeDiv').removeClass('d-none');
                $('#employee_id').prop('disabled', false);
            }
        });
        $('#user_id').on('change', function () {
            $('#user_id_hidden').val($(this).val());
        });
        $('#employee_id').on('change', function () {
            $('#user_id_hidden').val($(this).val());
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('backend.send.notification.store') }}",
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
                        $('#allDataTable').DataTable().ajax.reload();
                        toastr.success('Notification Send Successfully');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.send.notification.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                    $(".viewModal").modal('show');
                },
            });
        });
    });
</script>
@endsection

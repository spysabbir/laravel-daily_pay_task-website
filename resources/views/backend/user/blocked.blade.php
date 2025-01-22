@extends('layouts.template_master')

@section('title', 'User List - Blocked')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title">User List - Blocked</h3>
                <h3>Total: <span id="total_users_count">0</span></h3>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_duplicate_device_check" class="form-label">Duplicate Device Check</label>
                                <select class="form-select filter_data" id="filter_duplicate_device_check">
                                    <option value="">-- Duplicate Device Check --</option>
                                    <option value="Matched">Matched</option>
                                    <option value="Not Matched">Not Matched</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_last_activity" class="form-label">Last Activity</label>
                                <select class="form-select filter_data" id="filter_last_activity">
                                    <option value="">-- Select Last Activity --</option>
                                    <option value="Online">Online</option>
                                    <option value="Offline">Offline</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Deposit Balance</th>
                                <th>Withdraw Balance</th>
                                <th>Hold Balance</th>
                                <th>Total Report</th>
                                <th>Total Block</th>
                                <th>Join Date</th>
                                <th>Last Activity</th>
                                <th>Duplicate Device Check</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Status Modal -->
                            <div class="modal fade statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="statusModalLabel">Status</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="statusModalBody">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Device Modal -->
                            <div class="modal fade deviceModal" tabindex="-1" aria-labelledby="deviceModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deviceModalLabel">Device</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="deviceModalBody">

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
                url: "{{ route('backend.user.blocked') }}",
                type: 'GET',
                data: function (d) {
                    d.user_id = $('#filter_user_id').val();
                    d.duplicate_device_check = $('#filter_duplicate_device_check').val();
                    d.last_activity = $('#filter_last_activity').val();
                },
                dataSrc: function (json) {
                    // Update total deposit count
                    $('#total_users_count').text(json.totalUsersCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'deposit_balance', name: 'deposit_balance' },
                { data: 'withdraw_balance', name: 'withdraw_balance' },
                { data: 'hold_balance', name: 'hold_balance' },
                { data: 'report_count', name: 'report_count' },
                { data: 'block_count', name: 'block_count' },
                { data: 'created_at', name: 'created_at' },
                { data: 'last_activity_at', name: 'last_activity_at' },
                { data: 'duplicate_device_check', name: 'duplicate_device_check' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });
        // Filter Data
        $('.filter_data').keyup(function() {
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Device Data
        $(document).on('click', '.deviceBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.user.device', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#deviceModalBody').html(response);
                },
            });
        });

        // Status Data
        $(document).on('click', '.statusBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.user.status', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#statusModalBody').html(response);
                },
            });
        });

        // Update Data
        $("body").on("submit", "#statusForm", function(e){
            e.preventDefault();

            var id = $('#user_id').val();
            var url = "{{ route('backend.user.status.update', ":id") }}";
            url = url.replace(':id', id)

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        $('#statusForm')[0].reset();
                        $(".statusModal").modal('hide');
                        $('#allDataTable').DataTable().ajax.reload();
                        toastr.success('User status update successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        })

        // Soft Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.user.destroy', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You can bring it back though!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        success: function(response) {
                            $('#allDataTable').DataTable().ajax.reload();
                            $('#trashDataTable').DataTable().ajax.reload();
                            toastr.warning('User soft delete successfully.');
                        }
                    });
                }
            })
        })
    });
</script>
@endsection


@extends('layouts.template_master')

@section('title', 'Posted Task List - Running')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Posted Task List (Running)</h3>
                <h3>Total: <span id="total_posted_tasks_count">0</span></h3>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_posted_task_id" class="form-label">Posted Task Id</label>
                                <input type="number" id="filter_posted_task_id" class="form-control filter_data" placeholder="Search Posted Task Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="runningDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>Task Id</th>
                                <th>Title</th>
                                <th>Proof Submitted</th>
                                {{-- <th>Proof Status</th> --}}
                                <th>Submited At</th>
                                <th>Approved At</th>
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

        // Running Data
        $('#runningDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.posted_task_list.running') }}",
                type: "GET",
                data: function (d) {
                    d.posted_task_id = $('#filter_posted_task_id').val();
                    d.user_id = $('#filter_user_id').val();
                },
                dataSrc: function (json) {
                    // Update total deposit count
                    $('#total_posted_tasks_count').text(json.totalPostedTasksCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'proof_submitted', name: 'proof_submitted' },
                // { data: 'proof_status', name: 'proof_status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').keyup(function(){
            $('#runningDataTable').DataTable().ajax.reload();
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.running.posted_task_view', ":id") }}";
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

        // Edit Data
        $("body").on("submit", "#editForm", function (e) {
            e.preventDefault();

            var id = $('#post_task_id').val();
            var url = "{{ route('backend.posted_task_status_update', ':id') }}";
            url = url.replace(':id', id);

            var form = $(this);
            var formData = new FormData(this);

            var submitButton = form.find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        $('#runningDataTable').DataTable().ajax.reload();
                        $(".viewModal").modal('hide');
                        toastr.success('Task post update successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // Canceled Data
        $(document).on('click', '.canceledBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.running.posted_task_canceled', ":id") }}";
            url = url.replace(':id', id);

            Swal.fire({
                input: "textarea",
                inputLabel: "Cancellation Reason",
                inputPlaceholder: "Type cancellation reason here...",
                inputAttributes: {
                    "aria-label": "Type cancellation reason here..."
                },
                title: 'Are you sure?',
                text: "You want to cancel this task!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel it!',
                preConfirm: () => {
                    const message = Swal.getInput().value;
                    if (!message) {
                        Swal.showValidationMessage('Cancellation Reason is required');
                    }
                    return message;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: { id: id, message: result.value },
                        success: function(response) {
                            if (response.status == 401) {
                                toastr.error(response.error);
                            } else {
                                $('#runningDataTable').DataTable().ajax.reload();
                                toastr.error('Task Canceled Successfully');
                            }
                        },
                    });
                }
            });
        });

        // Paused Data
        $(document).on('click', '.pausedBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.running.posted_task_paused', ":id") }}";
            url = url.replace(':id', id);

            Swal.fire({
                input: "textarea",
                inputLabel: "Pausing Reason",
                inputPlaceholder: "Type pausing reason here...",
                inputAttributes: {
                    "aria-label": "Type pausing reason here..."
                },
                title: 'Are you sure?',
                text: "You want to paused this task!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, paused it!',
                preConfirm: () => {
                    const message = Swal.getInput().value;
                    if (!message) {
                        Swal.showValidationMessage('Pausing Reason is required');
                    }
                    return message;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: { id: id, message: result.value },
                        success: function(response) {
                            if (response.status == 401) {
                                toastr.error(response.error);
                            } else {
                                $('#runningDataTable').DataTable().ajax.reload();
                                toastr.error('Task Paused Successfully');
                            }
                        },
                    });
                }
            });
        });
    });
</script>
@endsection


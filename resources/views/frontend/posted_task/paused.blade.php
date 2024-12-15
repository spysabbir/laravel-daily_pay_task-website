@extends('layouts.template_master')

@section('title', 'Posting Task List - Paused')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Posting Task List - Paused</h3>
                    <p class="card-description text-warning">
                        Note: If your task paused by admin, you can see the reason here. If you want to resume the task, you can contact with admin.
                    </p>
                </div>
                <div>
                    <a href="{{ route('posted_task.list.pending') }}" class="btn btn-primary btn-xs m-1">Pending List</a>
                    <a href="{{ route('posted_task.list.rejected') }}" class="btn btn-danger btn-xs m-1">Rejected List</a>
                    <a href="{{ route('posted_task.list.running') }}" class="btn btn-info btn-xs m-1">Running List</a>
                    <a href="{{ route('posted_task.list.canceled') }}" class="btn btn-warning btn-xs m-1">Canceled List</a>
                    <a href="{{ route('posted_task.list.completed') }}" class="btn btn-success btn-xs m-1">Completed List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task ID</th>
                                <th>Title</th>
                                <th>Boosting Time</th>
                                <th>Proof Submitted</th>
                                <th>Work Duration Expire</th>
                                {{-- <th>Proof Status</th> --}}
                                {{-- <th>Total Cost</th> --}}
                                {{-- <th>Cost Status</th> --}}
                                {{-- <th>Pausing Reason</th> --}}
                                <th>Approved At</th>
                                <th>Paused At</th>
                                <th>Paused By</th>
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
                url: "{{ route('posted_task.list.paused') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'boosting_time', name: 'boosting_time' },
                { data: 'proof_submitted', name: 'proof_submitted' },
                { data: 'work_duration', name: 'work_duration' },
                // { data: 'proof_status', name: 'proof_status' },
                // { data: 'total_cost', name: 'total_cost' },
                // { data: 'charge_status', name: 'charge_status' },
                // { data: 'pausing_reason', name: 'pausing_reason' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'paused_at', name: 'paused_at' },
                { data: 'paused_by', name: 'paused_by' },
                { data: 'action', name: 'action' },
            ]
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('posted_task.view', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);

                    // Show Modal
                    $('.viewModal').modal('show');
                },
            });
        });

        // Resume Data
        $(document).on('click', '.resumeBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('running.posted_task.paused.resume', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to resume this task!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Resume it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(response) {
                            $('#allDataTable').DataTable().ajax.reload();
                            toastr.success('Task Resumed Successfully');
                        }
                    });
                }
            })
        })

        // Canceled Data
        $(document).on('click', '.canceledBtn', function(){

            var id = $(this).data('id');
            var url = "{{ route('running.posted_task.canceled', ":id") }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                method: 'POST',
                data: { id: id, check: true },
                success: function(response) {
                    if (response.status == 400) {
                        toastr.error(response.error);
                    } else {
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
                                            $('#allDataTable').DataTable().ajax.reload();
                                            $("#deposit_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                                            toastr.error('Task Canceled Successfully');
                                        }
                                    },
                                });
                            }
                        });
                    }
                },
            });
        });
    });
</script>
@endsection

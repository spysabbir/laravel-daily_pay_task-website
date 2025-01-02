@extends('layouts.template_master')

@section('title', 'Posted Task List - Pending')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Posted Task List - Pending</h3>
                    <h3>Total: <span id="total_tasks_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi user, below tasks list is your posted task and waiting for approval from admin. If your task is approved you will see it in the running folder and if it is rejected then you will see it in the rejected folder. If your task is here more than {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours then contact us. Also please contact us if you face any problem, thanks.
                    </p>
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
                                <th>Total Cost</th>
                                <th>Submited At</th>
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
                url: "{{ route('posted_task.list.pending') }}",
                dataSrc: function (json) {
                    // Update total task count
                    $('#total_tasks_count').text(json.totalTasksCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'total_cost', name: 'total_cost' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
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

        // Canceled Data
        $(document).on('click', '.canceledBtn', function(){

            var id = $(this).data('id');
            var url = "{{ route('posted_task.canceled', ":id") }}";
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

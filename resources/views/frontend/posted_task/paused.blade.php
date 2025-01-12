@extends('layouts.template_master')

@section('title', 'Posted Task List - Paused')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Posted Task List - Paused</h3>
                    <h3>Total: <span id="total_tasks_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi user, below tasks list is your paused task. If you pause the task you can be resume the task but if the task is paused by admin and want to resume the task need to contact us. Tasks will automatically removed from here after task work duration expires and get return pending money under our policy wise then tasks will move to canceled folder. Also If you facing any problems please contact us.
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
                                <th>Boosting Time</th>
                                <th>Proof Submitted</th>
                                <th>Work Duration Expire</th>
                                {{-- <th>Proof Status</th> --}}
                                {{-- <th>Cost Status</th> --}}
                                <th>Approved At</th>
                                <th>Paused At</th>
                                <th>
                                    <!-- Header Button for Expand/Collapse All -->
                                    <i id="toggleAllRows" class="fas fa-plus-circle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                                    Pausing Reason
                                </th>
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
        const table = $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('posted_task.list.paused') }}",
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
                { data: 'boosting_time', name: 'boosting_time' },
                { data: 'proof_submitted', name: 'proof_submitted' },
                { data: 'work_duration', name: 'work_duration' },
                // { data: 'proof_status', name: 'proof_status' },
                // { data: 'charge_status', name: 'charge_status' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'paused_at', name: 'paused_at' },
                {
                    data: 'pausing_reason',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <i class="fas fa-plus-circle row-toggle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                            <span>${data}</span>
                        `;
                    }
                },
                { data: 'paused_by', name: 'paused_by' },
                { data: 'action', name: 'action' },
            ]
        });

        // Add click event for the header button to expand/collapse all rows
        let allRowsOpen = false;

        // Function to check if all rows are expanded
        function updateGlobalIcon() {
            const rows = table.rows();
            const totalRows = rows.count();
            const openRows = rows.nodes().filter(row => $(row).hasClass('shown')).length;

            if (openRows === totalRows) {
                $('#toggleAllRows').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                allRowsOpen = true;
            } else {
                $('#toggleAllRows').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                allRowsOpen = false;
            }
        }

        // Individual row expand/collapse
        $('#allDataTable tbody').on('click', '.row-toggle', function () {
            const tr = $(this).closest('tr');
            const row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                $(this).removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Fetch proof_answer or any extra data
                const pausing_reason = row.data().pausing_reason_full;
                row.child(`<div class="nested-row">${pausing_reason}</div>`).show();
                tr.addClass('shown');
                $(this).removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }

            // Update the global expand/collapse button icon
            updateGlobalIcon();
        });

        // Global expand/collapse functionality
        $('#toggleAllRows').on('click', function () {
            const icon = $(this);
            const rows = table.rows();

            if (allRowsOpen) {
                // Collapse all rows
                rows.every(function () {
                    if (this.child.isShown()) {
                        this.child.hide();
                        $(this.node()).removeClass('shown');
                        $(this.node()).find('.row-toggle').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                    }
                });
                allRowsOpen = false;
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Expand all rows
                rows.every(function () {
                    const pausing_reason = this.data().pausing_reason_full;
                    if (!this.child.isShown()) {
                        this.child(`<div class="nested-row">${pausing_reason}</div>`).show();
                        $(this.node()).addClass('shown');
                        $(this.node()).find('.row-toggle').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                    }
                });
                allRowsOpen = true;
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }
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
            var url = "{{ route('posted_task.paused.resume', ":id") }}";
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

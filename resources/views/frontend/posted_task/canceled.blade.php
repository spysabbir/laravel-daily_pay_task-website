@extends('layouts.template_master')

@section('title', 'Posted Task List - Canceled')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Posted Task List - Canceled</h3>
                    <h3>Total: <span id="total_tasks_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi user, below tasks list is your canceled task. Tasks will automatically removed from below list after 7 days. Please contact us if you face any problem.
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
                                <th>Proof Submitted</th>
                                {{-- <th>Proof Status</th> --}}
                                {{-- <th>Cost Status</th> --}}
                                <th>
                                    <!-- Header Button for Expand/Collapse All -->
                                    <i id="toggleAllRows" class="fas fa-plus-circle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                                    Cancellation Reason
                                </th>
                                <th>Canceled At</th>
                                <th>Canceled By</th>
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
                url: "{{ route('posted_task.list.canceled') }}",
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
                { data: 'proof_submitted', name: 'proof_submitted' },
                // { data: 'proof_status', name: 'proof_status' },
                // { data: 'charge_status', name: 'charge_status' },
                {
                    data: 'cancellation_reason',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <i class="fas fa-plus-circle row-toggle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                            <span>${data}</span>
                        `;
                    }
                },
                { data: 'canceled_at', name: 'canceled_at' },
                { data: 'canceled_by', name: 'canceled_by' },
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
                const cancellation_reason = row.data().cancellation_reason_full;
                row.child(`<div class="nested-row">${cancellation_reason}</div>`).show();
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
                    const cancellation_reason = this.data().cancellation_reason_full;
                    if (!this.child.isShown()) {
                        this.child(`<div class="nested-row">${cancellation_reason}</div>`).show();
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
    });
</script>
@endsection

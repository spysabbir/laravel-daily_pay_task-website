@extends('layouts.template_master')

@section('title', 'Working Task List - Rejected')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h3 class="card-title">Working Task List - Rejected</h3>
                    <h3>Total: <span id="total_proofs_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi user, Below tasks list has been rejected by buyer or admin panel. If buyer rejects your proof you can send us to review your proof within 24 hours then 10 rupees will be deducted from your withdrawal balance. If you send the task proof to us for review, we will check your task proof. You will be notified after we check your review. If your review proof is correct then you will get refund of review cost in your withdrawal balance but if your review proof is not correct then you will not get refund of review cost. Tasks will be removed from the below list after 7 days. Please contact us if you face any problems.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3 border p-2">
                    <div class="row">
                        <div class="col-xl-3 col-lg-5 mb-3">
                            <div class="form-group">
                                <label class="form-label" for="filter_date">Filter By Date</label>
                                <input type="date" class="form-control filter_data bg-white" id="filter_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task Title</th>
                                <th>Task Rate</th>
                                <th>Submit Date</th>
                                <th>
                                    <!-- Header Button for Expand/Collapse All -->
                                    <i id="toggleAllRows" class="fas fa-plus-circle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                                    Rejected Reason
                                </th>
                                <th>Rejected Date</th>
                                <th>Rejected By</th>
                                <th>
                                    Review Send Expired Date
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">Check</h5>
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
                            <!-- Reviewed Modal -->
                            <div class="modal fade reviewedModal" tabindex="-1" aria-labelledby="reviewedModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reviewedModalLabel">Reviewed</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="reviewedModalBody">

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
                url: "{{ route('worked_task.list.rejected') }}",
                data: function (d) {
                    d.filter_date = $('#filter_date').val();
                },
                dataSrc: function (json) {
                    // Update total proof count
                    $('#total_proofs_count').text(json.totalProofsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'title', name: 'title' },
                { data: 'income_of_each_worker', name: 'income_of_each_worker' },
                { data: 'created_at', name: 'created_at' },
                {
                    data: 'rejected_reason',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <i class="fas fa-plus-circle row-toggle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                            <span>${data}</span>
                        `;
                    }
                },
                { data: 'rejected_at', name: 'rejected_at' },
                { data: 'rejected_by', name: 'rejected_by' },
                { data: 'review_send_expired', name: 'review_send_expired' },
                { data: 'action', name: 'action' }
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
                const rejected_reason = row.data().rejected_reason_full;
                row.child(`<div class="nested-row">${rejected_reason}</div>`).show();
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
                    const rejected_reason = this.data().rejected_reason_full;
                    if (!this.child.isShown()) {
                        this.child(`<div class="nested-row">${rejected_reason}</div>`).show();
                        $(this.node()).addClass('shown');
                        $(this.node()).find('.row-toggle').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                    }
                });
                allRowsOpen = true;
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Check Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('worked_task.check.rejected', ":id") }}";
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

        // Check Reviewed Data
        $(document).on('click', '.reviewedBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('worked_task.check.reviewed', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#reviewedModalBody').html(response);

                    // Destroy the existing LightGallery instance if it exists
                    var lightGalleryInstance = $('#single-lightgallery').data('lightGallery');
                    if (lightGalleryInstance) {
                        lightGalleryInstance.destroy(true); // Pass `true` to completely remove DOM bindings
                    }

                    // Reinitialize LightGallery
                    $('#single-lightgallery').lightGallery({
                        share: false,
                        showThumbByDefault: false,
                        hash: false,
                        mousewheel: false,
                    });

                    // Show Modal
                    $('.reviewedModal').modal('show');
                },
            });
        });

        // Set Date Range
        var today = new Date();
        var beforeSixDays = new Date();
        beforeSixDays.setDate(today.getDate() - 6);
        $('#filter_date').attr('max', today.toISOString().split('T')[0]);
        $('#filter_date').attr('min', beforeSixDays.toISOString().split('T')[0]);

    });
</script>
@endsection

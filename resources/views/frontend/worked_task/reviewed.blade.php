@extends('layouts.template_master')

@section('title', 'Working Task List - Reviewed')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h3 class="card-title">Working Task List - Reviewed</h3>
                    <h3>Total: <span id="total_proofs_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi worker, below tasks list is waiting for approval from admin panel. If your review is approved you will see it in the Approved folder and if it is rejected then you will see it in the rejected folder. After review checking you will get notification from admin panel. If your review is here more than {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours then contact us. Tasks will removed from below list after 7 days. Also please contact us if you face any problem.
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
                                <th>Review Id</th>
                                <th>Task Title</th>
                                <th>Task Rate</th>
                                {{-- <th>Submit Date</th> --}}
                                {{-- <th>Rejected Reason</th> --}}
                                <th>Rejected Date</th>
                                <th>
                                    <!-- Header Button for Expand/Collapse All -->
                                    <i id="toggleAllRows" class="fas fa-plus-circle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                                    Reviewed Reason
                                </th>
                                <th>Reviewed Date</th>
                                <th>Checking Expired Time</th>
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
                url: "{{ route('worked_task.list.reviewed') }}",
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
                { data: 'review_id', name: 'review_id' },
                { data: 'title', name: 'title' },
                { data: 'income_of_each_worker', name: 'income_of_each_worker' },
                // { data: 'created_at', name: 'created_at' },
                // { data: 'rejected_reason', name: 'rejected_reason' },
                { data: 'rejected_at', name: 'rejected_at' },
                {
                    data: 'reviewed_reason',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <i class="fas fa-plus-circle row-toggle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                            <span>${data}</span>
                        `;
                    }
                },
                { data: 'reviewed_at', name: 'reviewed_at' },
                { data: 'checking_expired_time', name: 'checking_expired_time' },
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
                const reviewed_reason = row.data().reviewed_reason_full;
                row.child(`<div class="nested-row">${reviewed_reason}</div>`).show();
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
                    const reviewed_reason = this.data().reviewed_reason_full;
                    if (!this.child.isShown()) {
                        this.child(`<div class="nested-row">${reviewed_reason}</div>`).show();
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
        $(document).on('onCloseAfter.lg', '#single-lightgallery', function () {
            // Remove hash fragment from the URL
            const url = window.location.href.split('#')[0];
            window.history.replaceState(null, null, url);
        });

        // Set Date Range
        var today = new Date();
        var beforeDays = new Date();
        beforeDays.setDate(today.getDate() - 6);
        $('#filter_date').attr('max', today.toISOString().split('T')[0]);
        $('#filter_date').attr('min', beforeDays.toISOString().split('T')[0]);

    });
</script>
@endsection

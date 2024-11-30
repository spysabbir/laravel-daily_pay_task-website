@extends('layouts.template_master')

@section('title', 'Worked Task Report')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Worked Task Report</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="filter_status" class="form-label">Status</label>
                            <select class="form-select filter_data" id="filter_status">
                                <option value="">-- Select Status --</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Reviewed">Reviewed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="filter_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control filter_data" id="filter_start_date">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="filter_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control filter_data" id="filter_end_date">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="reportDataTable" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sl No</th>
                                <th>Worked Date</th>
                                <th>Pending Count</th>
                                <th>Approved Count</th>
                                <th>Rejected Count</th>
                                <th>Reviewed Count</th>
                                <th>Total Count</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                <th id="total_pending_count_sum"></th>
                                <th id="total_approved_count_sum"></th>
                                <th id="total_rejected_count_sum"></th>
                                <th id="total_reviewed_count_sum"></th>
                                <th id="total_task_count_sum"></th>
                            </tr>
                        </tfoot>
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
        // Function to update dynamic message for messageTop
        function updateDynamicMessageTop() {
            var status = $('#filter_status').val() || 'All Status';
            var startDate = $('#filter_start_date').val() || 'No Start Date';
            var endDate = $('#filter_end_date').val() || 'No End Date';

            window.dynamicMessageTop = 'Filters Applied - Status: ' + status + '; Start Date: ' + startDate + '; End Date: ' + endDate + ';';
        }

        // Set the initial dynamic message
        updateDynamicMessageTop();

        // Report DataTable initialization
        var table = $('#reportDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.worked_task.report') }}",
                data: function(e) {
                    e.status = $('#filter_status').val();
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'worked_date', name: 'worked_date' },
                { data: 'pending_count', name: 'pending_count' },
                { data: 'approved_count', name: 'approved_count' },
                { data: 'rejected_count', name: 'rejected_count' },
                { data: 'reviewed_count', name: 'reviewed_count' },
                { data: 'total_task_count', name: 'total_task_count' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#total_pending_count_sum').html(response.total_pending_count_sum);
                $('#total_approved_count_sum').html(response.total_approved_count_sum);
                $('#total_rejected_count_sum').html(response.total_rejected_count_sum);
                $('#total_reviewed_count_sum').html(response.total_reviewed_count_sum);
                $('#total_task_count_sum').html(response.total_task_count_sum);
            },
            initComplete: function() {
                // Update messageTop every time filters change
                $('.filter_data').change(function() {
                    updateDynamicMessageTop();
                    table.ajax.reload();
                });
            }
        });

        // Ajax setup for CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection

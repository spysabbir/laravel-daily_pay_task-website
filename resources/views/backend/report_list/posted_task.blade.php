@extends('layouts.template_master')

@section('title', 'Posted Task Report')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Posted Task Report</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="filter_status" class="form-label">Status</label>
                            <select class="form-select filter_data" id="filter_status">
                                <option value="">-- Select Status --</option>
                                <option value="Pending">Pending</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Running">Running</option>
                                <option value="Canceled">Canceled</option>
                                <option value="Paused">Paused</option>
                                <option value="Completed">Completed</option>
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
                                <th>Posted Date</th>
                                <th>Pending Count</th>
                                <th>Rejected Count</th>
                                <th>Running Count</th>
                                <th>Canceled Count</th>
                                <th>Paused Count</th>
                                <th>Completed Count</th>
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
                                <th id="total_rejected_count_sum"></th>
                                <th id="total_running_count_sum"></th>
                                <th id="total_canceled_count_sum"></th>
                                <th id="total_paused_count_sum"></th>
                                <th id="total_completed_count_sum"></th>
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
                url: "{{ route('backend.posted_task.report') }}",
                data: function(e) {
                    e.status = $('#filter_status').val();
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'posted_date', name: 'posted_date' },
                { data: 'pending_count', name: 'pending_count' },
                { data: 'rejected_count', name: 'rejected_count' },
                { data: 'running_count', name: 'running_count' },
                { data: 'canceled_count', name: 'canceled_count' },
                { data: 'paused_count', name: 'paused_count' },
                { data: 'completed_count', name: 'completed_count' },
                { data: 'total_task_count', name: 'total_task_count' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#total_pending_count_sum').html(response.total_pending_count_sum);
                $('#total_rejected_count_sum').html(response.total_rejected_count_sum);
                $('#total_running_count_sum').html(response.total_running_count_sum);
                $('#total_canceled_count_sum').html(response.total_canceled_count_sum);
                $('#total_paused_count_sum').html(response.total_paused_count_sum);
                $('#total_completed_count_sum').html(response.total_completed_count_sum);
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

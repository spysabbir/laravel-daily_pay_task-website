@extends('layouts.template_master')

@section('title', 'Top Posted Task User')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Top Posted Task User</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-3 col-lg-6 mb-3">
                        <div class="form-group">
                            <label for="filter_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control filter_data" id="filter_start_date">
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 mb-3">
                        <div class="form-group">
                            <label for="filter_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control filter_data" id="filter_end_date">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="getDataTableData" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Pending</th>
                                <th>Rejected</th>
                                <th>Running</th>
                                <th>Canceled</th>
                                <th>Paused</th>
                                <th>Completed</th>
                                <th>Total Tasks</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                <th id="total_pending"></th>
                                <th id="total_rejected"></th>
                                <th id="total_running"></th>
                                <th id="total_canceled"></th>
                                <th id="total_paused"></th>
                                <th id="total_completed"></th>
                                <th id="total_tasks_sum"></th>
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
            var startDate = $('#filter_start_date').val() || 'No Start Date';
            var endDate = $('#filter_end_date').val() || 'No End Date';

            window.dynamicMessageTop = 'Filters Applied - Start Date: ' + startDate + '; End Date: ' + endDate + ';';
        }

        // Set the initial dynamic message
        updateDynamicMessageTop();

        // DataTable initialization
        var table = $('#getDataTableData').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.top.posted_task.user') }}",
                data: function (e) {
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'pending_count', name: 'pending_count' },
                { data: 'rejected_count', name: 'rejected_count' },
                { data: 'running_count', name: 'running_count' },
                { data: 'canceled_count', name: 'canceled_count' },
                { data: 'paused_count', name: 'paused_count' },
                { data: 'completed_count', name: 'completed_count' },
                { data: 'total_tasks', name: 'total_tasks' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#total_pending').html(response.total_pending);
                $('#total_rejected').html(response.total_rejected);
                $('#total_running').html(response.total_running);
                $('#total_canceled').html(response.total_canceled);
                $('#total_paused').html(response.total_paused);
                $('#total_completed').html(response.total_completed);
                $('#total_tasks_sum').html(response.total_tasks_sum);
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

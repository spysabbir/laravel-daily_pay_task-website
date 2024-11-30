@extends('layouts.template_master')

@section('title', 'Top Deposit User')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Top Deposit User</h3>
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
                                <th>Active Count</th>
                                <th>Inactive Count</th>
                                <th>Blocked Count</th>
                                <th>Banned Count</th>
                                <th>Referred Count</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                <th id="active_count"></th>
                                <th id="inactive_count"></th>
                                <th id="blocked_count"></th>
                                <th id="banned_count"></th>
                                <th id="total_user"></th>
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
                url: "{{ route('backend.top.referred.user') }}",
                data: function (e) {
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'active_count', name: 'active_count' },
                { data: 'inactive_count', name: 'inactive_count' },
                { data: 'blocked_count', name: 'blocked_count' },
                { data: 'banned_count', name: 'banned_count' },
                { data: 'total_referred', name: 'total_referred' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#active_count').html(response.active_count);
                $('#inactive_count').html(response.inactive_count);
                $('#blocked_count').html(response.blocked_count);
                $('#banned_count').html(response.banned_count);
                $('#total_user').html(response.total_user);
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

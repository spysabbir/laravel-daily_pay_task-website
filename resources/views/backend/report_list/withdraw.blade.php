@extends('layouts.template_master')

@section('title', 'Withdraw Report')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Withdraw Report</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-2 col-lg-6 mb-3">
                        <div class="form-group">
                            <label for="filter_method" class="form-label">Method</label>
                            <select class="form-select filter_data" id="filter_method">
                                <option value="">-- Select Method --</option>
                                <option value="Bkash">Bkash</option>
                                <option value="Nagad">Nagad</option>
                                <option value="Rocket">Rocket</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-6 mb-3">
                        <div class="form-group">
                            <label for="filter_status" class="form-label">Status</label>
                            <select class="form-select filter_data" id="filter_status">
                                <option value="">-- Select Status --</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-6 mb-3">
                        <div class="form-group">
                            <label for="filter_type" class="form-label">Type</label>
                            <select class="form-select filter_data" id="filter_type">
                                <option value="">-- Select Type --</option>
                                <option value="Ragular">Ragular</option>
                                <option value="Instant">Instant</option>
                            </select>
                        </div>
                    </div>
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
                    <table id="reportDataTable" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sl No</th>
                                <th>Withdraw Date</th>
                                <th>Bkash Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Nagad Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Rocket Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Pending Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Approved Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Rejected Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Ragular Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Instant Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                <th id="bkash_total"></th>
                                <th id="nagad_total"></th>
                                <th id="rocket_total"></th>
                                <th id="pending_total"></th>
                                <th id="approved_total"></th>
                                <th id="rejected_total"></th>
                                <th id="ragular_total"></th>
                                <th id="instant_total"></th>
                                <th id="total_amount_sum"></th>
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
            var type = $('#filter_type').val() || 'All Types';
            var method = $('#filter_method').val() || 'All Methods';
            var status = $('#filter_status').val() || 'All Statuses';
            var startDate = $('#filter_start_date').val() || 'No Start Date';
            var endDate = $('#filter_end_date').val() || 'No End Date';

            window.dynamicMessageTop = 'Filters Applied - Type: ' + type + '; Method: ' + method + '; Status: ' + status + '; Start Date: ' + startDate + '; End Date: ' + endDate + ';';
        }

        // Set the initial dynamic message
        updateDynamicMessageTop();

        // Report DataTable initialization
        var table = $('#reportDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.withdraw.report') }}",
                data: function (e) {
                    e.method = $('#filter_method').val();
                    e.status = $('#filter_status').val();
                    e.type = $('#filter_type').val();
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'withdraw_date', name: 'withdraw_date' },
                { data: 'bkash_amount', name: 'bkash_amount' },
                { data: 'nagad_amount', name: 'nagad_amount' },
                { data: 'rocket_amount', name: 'rocket_amount' },
                { data: 'pending_amount', name: 'pending_amount' },
                { data: 'approved_amount', name: 'approved_amount' },
                { data: 'rejected_amount', name: 'rejected_amount' },
                { data: 'ragular_amount', name: 'ragular_amount' },
                { data: 'instant_amount', name: 'instant_amount' },
                { data: 'total_amount', name: 'total_amount' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#bkash_total').html(response.total_bkash_amount_sum);
                $('#nagad_total').html(response.total_nagad_amount_sum);
                $('#rocket_total').html(response.total_rocket_amount_sum);
                $('#pending_total').html(response.total_pending_amount_sum);
                $('#approved_total').html(response.total_approved_amount_sum);
                $('#rejected_total').html(response.total_rejected_amount_sum);
                $('#ragular_total').html(response.total_ragular_amount_sum);
                $('#instant_total').html(response.total_instant_amount_sum);
                $('#total_amount_sum').html(response.total_amount_sum);
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

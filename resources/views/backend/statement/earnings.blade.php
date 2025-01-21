@extends('layouts.template_master')

@section('title', 'Earnings Statement')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Earnings Statement</h3>
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
                    <table id="reportDataTable" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sl No</th>
                                <th>Earnings Date</th>
                                <th>Total Withdraw Charge ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Post Task Charge ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Proof Task Reviewed Charge ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Balance Transfer Charge ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Instant Blocked Resolved Charge ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                <th id="total_withdraw_charge_sum"></th>
                                <th id="total_post_task_charge_sum"></th>
                                <th id="total_proof_task_reviewed_charge_sum"></th>
                                <th id="total_balance_transfer_charge_sum"></th>
                                <th id="total_instant_blocked_resolved_charge_sum"></th>
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
            var startDate = $('#filter_start_date').val() || 'No Start Date';
            var endDate = $('#filter_end_date').val() || 'No End Date';

            window.dynamicMessageTop = 'Filters Applied - Start Date: ' + startDate + '; End Date: ' + endDate + ';';
        }

        // Set the initial dynamic message
        updateDynamicMessageTop();

        // Report DataTable initialization
        var table = $('#reportDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.earnings.statement') }}",
                data: function (e) {
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'earnings_date', name: 'earnings_date' },
                { data: 'total_withdraw_charge', name: 'total_withdraw_charge' },
                { data: 'total_post_task_charge', name: 'total_post_task_charge' },
                { data: 'total_proof_task_reviewed_charge', name: 'total_proof_task_reviewed_charge' },
                { data: 'total_balance_transfer_charge', name: 'total_balance_transfer_charge' },
                { data: 'total_instant_blocked_resolved_charge', name: 'total_instant_blocked_resolved_charge' },
                { data: 'total_amount', name: 'total_amount' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                // Update the totals in the respective HTML elements
                $('#total_withdraw_charge_sum').html(response.total_withdraw_charge_sum);
                $('#total_post_task_charge_sum').html(response.total_post_task_charge_sum);
                $('#total_proof_task_reviewed_charge_sum').html(response.total_proof_task_reviewed_charge_sum);
                $('#total_balance_transfer_charge_sum').html(response.total_balance_transfer_charge_sum);
                $('#total_instant_blocked_resolved_charge_sum').html(response.total_instant_blocked_resolved_charge_sum);
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

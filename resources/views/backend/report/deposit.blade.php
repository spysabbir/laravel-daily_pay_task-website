@extends('layouts.template_master')

@section('title', 'Deposit Report')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Deposit Report</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="filter_method" class="form-label">Method</label>
                            <select class="form-select filter_data" id="filter_method">
                                <option value="">-- Select Method --</option>
                                <option value="Bkash">Bkash</option>
                                <option value="Nagad">Nagad</option>
                                <option value="Rocket">Rocket</option>
                                <option value="Withdrawal Balance">Withdrawal Balance</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
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
                                <th>Deposit Date</th>
                                <th>Bkash Amount</th>
                                <th>Nagad Amount</th>
                                <th>Rocket Amount</th>
                                <th>Withdrawal Balance Amount</th>
                                <th>Pending Amount</th>
                                <th>Approved Amount</th>
                                <th>Rejected Amount</th>
                                <th>Total Payable Amount</th>
                                <th>Deposit Charge</th>
                                <th>Total Amount</th>
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
                                <th id="withdrawal_balance_total"></th>
                                <th id="pending_total"></th>
                                <th id="approved_total"></th>
                                <th id="rejected_total"></th>
                                <th id="total_payable_amount_sum"></th>
                                <th id="deposit_charge_sum"></th>
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
            var method = $('#filter_method').val() || 'All Methods';
            var status = $('#filter_status').val() || 'All Statuses';
            var startDate = $('#filter_start_date').val() || 'No Start Date';
            var endDate = $('#filter_end_date').val() || 'No End Date';

            window.dynamicMessageTop = 'Filters Applied - Method: ' + method + '; Status: ' + status + '; Start Date: ' + startDate + '; End Date: ' + endDate + ';';
        }

        // Set the initial dynamic message
        updateDynamicMessageTop();

        // Report DataTable initialization
        var table = $('#reportDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.deposit.report') }}",
                data: function (e) {
                    e.method = $('#filter_method').val();
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                    e.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'deposit_date', name: 'deposit_date' },
                { data: 'bkash_amount', name: 'bkash_amount' },
                { data: 'nagad_amount', name: 'nagad_amount' },
                { data: 'rocket_amount', name: 'rocket_amount' },
                { data: 'withdrawal_balance_amount', name: 'withdrawal_balance_amount' },
                { data: 'pending_amount', name: 'pending_amount' },
                { data: 'approved_amount', name: 'approved_amount' },
                { data: 'rejected_amount', name: 'rejected_amount' },
                { data: 'total_payable_amount', name: 'total_payable_amount' },
                { data: 'deposit_charge', name: 'deposit_charge' },
                { data: 'total_amount', name: 'total_amount' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#bkash_total').html(response.total_bkash_amount_sum);
                $('#nagad_total').html(response.total_nagad_amount_sum);
                $('#rocket_total').html(response.total_rocket_amount_sum);
                $('#withdrawal_balance_total').html(response.total_withdrawal_balance_amount_sum);
                $('#pending_total').html(response.total_pending_amount_sum);
                $('#approved_total').html(response.total_approved_amount_sum);
                $('#rejected_total').html(response.total_rejected_amount_sum);
                $('#total_payable_amount_sum').html(response.total_payable_amount_sum);
                $('#deposit_charge_sum').html(response.deposit_charge_sum);
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

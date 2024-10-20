@extends('layouts.template_master')

@section('title', 'Bonus Report')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Bonus Report</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="filter_type" class="form-label">Type</label>
                            <select class="form-select filter_data" id="filter_type">
                                <option value="">-- Select Type --</option>
                                <option value="Referral Registration Bonus">Referral Registration Bonus</option>
                                <option value="Referral Withdrawal Bonus">Referral Withdrawal Bonus</option>
                                <option value="Proof Task Approved Bonus">Proof Task Approved Bonus</option>
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
                                <th>Bonus Date</th>
                                <th>Referral Registration Bonus Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Referral Withdrawal Bonus Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Proof Task Approved Bonus Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Site Bonus Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>User Bonus Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                <th id="total_referral_registration_bonus_amount_sum"></th>
                                <th id="total_referral_withdrawal_bonus_amount_sum"></th>
                                <th id="total_proof_task_approved_bonus_amount_sum"></th>
                                <th id="total_site_bonus_amount_sum"></th>
                                <th id="total_user_bonus_amount_sum"></th>
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
            var startDate = $('#filter_start_date').val() || 'No Start Date';
            var endDate = $('#filter_end_date').val() || 'No End Date';

            window.dynamicMessageTop = 'Filters Applied - Type: ' + type + '; Start Date: ' + startDate + '; End Date: ' + endDate + ';';
        }

        // Set the initial dynamic message
        updateDynamicMessageTop();

        // Report DataTable initialization
        var table = $('#reportDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.bonus.report') }}",
                data: function (e) {
                    e.type = $('#filter_type').val();
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'bonus_date', name: 'bonus_date' },
                { data: 'referral_registration_bonus_amount', name: 'referral_registration_bonus_amount' },
                { data: 'referral_withdrawal_bonus_amount', name: 'referral_withdrawal_bonus_amount' },
                { data: 'proof_task_approved_bonus_amount', name: 'proof_task_approved_bonus_amount' },
                { data: 'site_bonus_amount', name: 'site_bonus_amount' },
                { data: 'user_bonus_amount', name: 'user_bonus_amount' },
                { data: 'total_amount', name: 'total_amount' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#total_referral_registration_bonus_amount_sum').html(response.total_referral_registration_bonus_amount_sum);
                $('#total_referral_withdrawal_bonus_amount_sum').html(response.total_referral_withdrawal_bonus_amount_sum);
                $('#total_proof_task_approved_bonus_amount_sum').html(response.total_proof_task_approved_bonus_amount_sum);
                $('#total_site_bonus_amount_sum').html(response.total_site_bonus_amount_sum);
                $('#total_user_bonus_amount_sum').html(response.total_user_bonus_amount_sum);
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

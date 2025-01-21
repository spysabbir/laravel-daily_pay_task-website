@extends('layouts.template_master')

@section('title', 'Expenses Statement')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Expenses Statement</h3>
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
                                <th>Expenses Date</th>
                                <th>Total Expense Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Bonus Given Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                <th id="total_expense_amount_sum"></th>
                                <th id="total_bonus_amount_sum"></th>
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
                url: "{{ route('backend.expenses.statement') }}",
                data: function(e) {
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'expenses_date', name: 'expenses_date' },
                { data: 'total_expense_amount', name: 'total_expense_amount' },
                { data: 'total_bonus_amount', name: 'total_bonus_amount' },
                { data: 'total_amount', name: 'total_amount' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                $('#total_expense_amount_sum').html(response.total_expense_amount_sum);
                $('#total_bonus_amount_sum').html(response.total_bonus_amount_sum);
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

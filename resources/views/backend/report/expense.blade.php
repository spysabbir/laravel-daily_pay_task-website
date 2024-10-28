@extends('layouts.template_master')

@section('title', 'Expense Report')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Expense Report</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-3 col-lg-6 mb-3">
                        <div class="form-group">
                            <label for="filter_category" class="form-label">Category</label>
                            <select class="form-select filter_data" id="filter_category">
                                <option value="">-- Select Category --</option>
                                @foreach($expenseCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 mb-3">
                        <div class="form-group">
                            <label for="filter_status" class="form-label">Status</label>
                            <select class="form-select filter_data" id="filter_status">
                                <option value="">-- Select Status --</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
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
                                <th>Expense Date</th>
                                @foreach($expenseCategories as $category)
                                    <th>{{ $category->name }} Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                @endforeach
                                <th>Active Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Inactive Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                                <th>Total Amount ( {{ get_site_settings('site_currency_symbol') }} )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-center">Total</th>
                                @foreach($expenseCategories as $category)
                                    <th id="total_{{ Str::slug($category->name, '_') }}_amount_sum"></th>
                                @endforeach
                                <th id="total_active_amount_sum"></th>
                                <th id="total_inactive_amount_sum"></th>
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
            var category = $('#filter_category option:selected').text() || 'All Category';
            var status = $('#filter_status').val() || 'All Status';
            var startDate = $('#filter_start_date').val() || 'No Start Date';
            var endDate = $('#filter_end_date').val() || 'No End Date';

            window.dynamicMessageTop = 'Filters Applied - Category: ' + category + '; Status: ' + status + '; Start Date: ' + startDate + '; End Date: ' + endDate + ';';
        }

        // Set the initial dynamic message
        updateDynamicMessageTop();

        // Report DataTable initialization
        var table = $('#reportDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.expense.report') }}",
                data: function(e) {
                    e.expense_category_id = $('#filter_category').val();
                    e.status = $('#filter_status').val();
                    e.start_date = $('#filter_start_date').val();
                    e.end_date = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'expense_date', name: 'expense_date' },
                @foreach($expenseCategories as $category)
                    { data: '{{ Str::slug($category->name, '_') }}_amount', name: '{{ Str::slug($category->name, '_') }}_amount' },
                @endforeach
                { data: 'active_amount', name: 'active_amount' },
                { data: 'inactive_amount', name: 'inactive_amount' },
                { data: 'total_amount', name: 'total_amount' },
            ],
            drawCallback: function(settings) {
                var response = settings.json;

                @foreach($expenseCategories as $category)
                    $('#total_{{ Str::slug($category->name, '_') }}_amount_sum').html(response.category_totals.find(c => c.category == '{{ $category->name }}').total);
                @endforeach
                $('#total_active_amount_sum').html(response.total_active_amount_sum);
                $('#total_inactive_amount_sum').html(response.total_inactive_amount_sum);
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

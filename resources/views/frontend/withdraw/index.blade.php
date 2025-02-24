@extends('layouts.template_master')

@section('title', 'Withdraw')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="text">
                    <h3 class="card-title">Withdraw List</h3>
                    <p class="mb-0">Note: You can Withdraw money by using Bkash, Nagad, Rocket. Minimum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('min_withdraw_amount') }} and maximum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('max_withdraw_amount') }}. Withdraw charge percentage is {{ get_default_settings('withdraw_charge_percentage') }} %. Instant withdraw charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('instant_withdraw_charge') }}. After submitting the withdraw request, the admin will verify your request then send the money to your account number. If you have any problem, please contact with us.</p>
                    <div class="mt-3 d-flex align-items-center justify-content-between flex-wrap">
                        <div class="alert alert-info" role="alert">
                            <span class="alert-heading text-center">
                                <i class="link-icon" data-feather="credit-card"></i>
                                Total Withdraw Amount: <span id="total_withdraw_amount">0</span>
                            </span>
                        </div>
                        <button type="button" class="btn btn-primary m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".createModel">
                            Withdraw <i data-feather="dollar-sign"></i>
                        </button>
                        <!-- Withdraw Modal -->
                        <div class="modal fade createModel" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModelLabel">Withdraw</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                    </div>
                                    <form class="forms-sample" id="createForm">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Withdraw Type <span class="text-danger">*</span></label>
                                                <select class="form-select" id="type" name="type" required>
                                                    <option value="">-- Select Withdraw Type --</option>
                                                    <option value="Ragular">Ragular</option>
                                                    <option value="Instant">Instant</option>
                                                </select>
                                                <small class="text-info d-block">
                                                    <strong id="ragular">Note: Withdraw request will be processed within 24 hours.</strong>
                                                    <strong id="instant">Note: Withdraw request will be processed in 30 minutes. But you will be charged an extra {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('instant_withdraw_charge') }}.</strong>
                                                </small>
                                                <span class="text-danger error-text type_error"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="method" class="form-label">Withdraw Method <span class="text-danger">*</span></label>
                                                <select class="form-select" id="method" name="method" required>
                                                    <option value="">-- Select Withdraw Method --</option>
                                                    <option value="Bkash">Bkash</option>
                                                    <option value="Nagad">Nagad</option>
                                                    <option value="Rocket">Rocket</option>
                                                </select>
                                                <span class="text-danger error-text method_error"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="number" class="form-label">Withdraw Number <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="number" name="number" placeholder="Withdraw Number" required>
                                                <small class="text-info d-block">Note: The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).</small>
                                                <span class="text-danger error-text number_error"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="amount" class="form-label">Withdraw Amount <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="amount" name="amount" min="{{ get_default_settings('min_withdraw_amount') }}" max="{{ get_default_settings('max_withdraw_amount') }}" placeholder="Withdraw Amount" required>
                                                    <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                </div>
                                                <small class="text-info d-block">Note: Minimum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('min_withdraw_amount') }} and maximum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('max_withdraw_amount') }}</small>
                                                <span class="text-danger error-text amount_error"></span>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Withdraw Charge Percentage</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="{{ get_default_settings('withdraw_charge_percentage') }}" disabled>
                                                    <span class="input-group-text input-group-addon">%</span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Payable Withdraw Amount</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="payable_withdraw_amount" value="0" placeholder="Payable Withdraw Amount" disabled>
                                                    <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Withdraw</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Withdraw Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Withdraw Type --</option>
                                    <option value="Ragular">Ragular</option>
                                    <option value="Instant">Instant</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_method" class="form-label">Withdraw Method</label>
                                <select class="form-select filter_data" id="filter_method">
                                    <option value="">-- Select Withdraw Method --</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Rocket">Rocket</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Withdraw Type</th>
                                <th>Withdraw Method</th>
                                <th>Withdraw Number</th>
                                <th>Withdraw Amount</th>
                                <th>Payable Amount</th>
                                <th>Submitted Date</th>
                                <th>Approved / Rejected Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#ragular').hide();
        $('#instant').hide();
        $('#type').change(function(){
            if ($('#type').val() == 'Ragular') {
                $('#ragular').show();
                $('#instant').hide();
                var amount = $('#amount').val();
                var charge = {{ get_default_settings('withdraw_charge_percentage') }};
                var payable_amount = amount - (amount * charge / 100);
                $('#payable_withdraw_amount').val(payable_amount);
            }else if ($('#type').val() == 'Instant') {
                $('#ragular').hide();
                $('#instant').show();
                var amount = $('#amount').val();
                var charge = {{ get_default_settings('withdraw_charge_percentage') }};
                var instant_withdraw_charge = {{ get_default_settings('instant_withdraw_charge') }};
                var payable_amount = amount - (amount * charge / 100);
                if(amount == ''){
                        $('#payable_withdraw_amount').val(payable_amount);
                    }else{
                        $('#payable_withdraw_amount').val(payable_amount - instant_withdraw_charge);
                    }
            }else{
                $('#ragular').hide();
                $('#instant').hide();
            }
        });

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('withdraw') }}",
                data: function (e) {
                    e.status = $('#filter_status').val();
                    e.method = $('#filter_method').val();
                    e.type = $('#filter_type').val();
                },
                dataSrc: function (json) {
                    var currencySymbol = '{{ get_site_settings('site_currency_symbol') }}';
                    $('#total_withdraw_amount').text(currencySymbol + ' ' + json.totalWithdrawAmount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'type', name: 'type' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number'},
                { data: 'amount', name: 'amount' },
                { data: 'payable_amount', name: 'payable_amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'approved_or_rejected_at', name: 'approved_or_rejected_at' },
                { data: 'status', name: 'status' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Calculate Payable Amount
        $('#amount, #type').on('keyup change', function(){
            var amount = $('#amount').val();
            var instant_withdraw_charge = {{ get_default_settings('instant_withdraw_charge') }};
            var charge = {{ get_default_settings('withdraw_charge_percentage') }};
            var payable_amount = amount - (amount * charge / 100);

            if ($('#type').val() == 'Instant') {
                if(amount == ''){
                    $('#payable_withdraw_amount').val(payable_amount);
                } else {
                    $('#payable_withdraw_amount').val(payable_amount - instant_withdraw_charge);
                }
            } else {
                $('#payable_withdraw_amount').val(payable_amount);
            }
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('withdraw.store') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        if (response.status == 200) {
                            $('.createModel').modal('hide');
                            $('#createForm')[0].reset();
                            $('#allDataTable').DataTable().ajax.reload();
                            $("#withdraw_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.withdraw_balance);
                            toastr.success('Withdraw request sent successfully.');
                        }else{
                            toastr.error(response.error);
                        }
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>
@endsection

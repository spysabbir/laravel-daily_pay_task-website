@extends('layouts.template_master')

@section('title', 'Balance Transfer')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Balance Transfer</h3>
                <h3>Total Amount: <span id="totalBalanceTransferAmount">0</span></h3>
                <div>
                    {{-- @can('balance.transfer.store') --}}
                    <!-- Balance Transfer Modal -->
                    <button type="button" class="btn btn-primary m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".createModel">
                        Transfer <i data-feather="dollar-sign"></i>
                    </button>
                    {{-- @endcan --}}
                    <div class="modal fade createModel select2Model" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModelLabel">Transfer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="createForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">User Name <span class="text-danger">*</span></label>
                                            <select class="form-select js-select2-single" id="user_id" name="user_id" required data-width="100%">
                                                <option value="">-- Select User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }} </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text user_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sendMethod" class="form-label">Send Method <span class="text-danger">*</span></label>
                                            <select class="form-select" id="sendMethod" name="send_method" required>
                                                <option value="">-- Select Send Method --</option>
                                                <option value="Withdraw Balance">Withdraw Balance</option>
                                                <option value="Deposit Balance">Deposit Balance</option>
                                            </select>
                                            <span class="text-danger error-text send_error"></span>
                                        </div>
                                        <div class="mb-3" id="receive_method_div" style="display: none;">
                                            <label for="receiveMethod" class="form-label">Receive Method <span class="text-danger">*</span></label>
                                            <select class="form-select" id="receiveMethod" name="receive_method">
                                                <option value="">-- Select Receive Method --</option>
                                                <option value="Withdraw Balance">Withdraw Balance</option>
                                                <option value="Deposit Balance">Deposit Balance</option>
                                            </select>
                                            <span class="text-danger error-text receive_error"></span>
                                        </div>
                                        <div class="mb-3" id="deposit_balance_check_div">
                                            <label for="deposit_balance_check" class="form-label">Deposit Balance</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="deposit_balance_check" value="{{ Auth::user()->deposit_balance }}" disabled>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="withdraw_balance_check_div">
                                            <label for="withdraw_balance_check" class="form-label">Withdraw Balance</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="withdraw_balance_check" value="{{ Auth::user()->withdraw_balance }}" disabled>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="transferAmount" class="form-label">Transfer Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="transferAmount" name="amount" min="1" max="10000" placeholder="Transfer Amount" required>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <small class="text-info d-block">Note: Minimum transfer  amount is {{ get_site_settings('site_currency_symbol') }} 1 and maximum transfer amount is {{ get_site_settings('site_currency_symbol') }} 10000.</small>
                                            <span class="text-danger error-text amount_error"></span>
                                        </div>
                                        <div class="mb-3" id="withdraw_balance_transfer_charge_div">
                                            <label for="withdraw_balance_transfer_charge_percentage" class="form-label">Withdraw Balance Transfer Charge Percentage</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="withdraw_balance_transfer_charge_percentage" name="charge_percentage" value="{{ get_default_settings('withdraw_balance_transfer_charge_percentage') }}" placeholder="Withdraw Balance Transfer Charge Percentage">
                                                <span class="input-group-text input-group-addon">%</span>
                                            </div>
                                            <span class="text-danger error-text charge_percentage_error"></span>
                                        </div>
                                        <div class="mb-3" id="deposit_balance_transfer_charge_div">
                                            <label for="deposit_balance_transfer_charge_percentage" class="form-label">Deposit Balance Transfer Charge Percentage</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="deposit_balance_transfer_charge_percentage" name="charge_percentage" value="{{ get_default_settings('deposit_balance_transfer_charge_percentage') }}" placeholder="Deposit Balance Transfer Charge Percentage">
                                                <span class="input-group-text input-group-addon">%</span>
                                            </div>
                                            <span class="text-danger error-text charge_percentage_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="payable_amount" class="form-label">Payable Amount</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="payable_amount" value="0" placeholder="Payable Amount" disabled>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Deposit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_send_method" class="form-label">Send Method</label>
                                <select class="form-select filter_data" id="filter_send_method">
                                    <option value="">-- Select Send Method --</option>
                                    <option value="Deposit Balance">Deposit Balance</option>
                                    <option value="Withdraw Balance">Withdraw Balance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_receive_method" class="form-label">Receive Method</label>
                                <select class="form-select filter_data" id="filter_receive_method">
                                    <option value="">-- Select Receive Method --</option>
                                    <option value="Deposit Balance">Deposit Balance</option>
                                    <option value="Withdraw Balance">Withdraw Balance</option>
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
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Send Method</th>
                                <th>Receive Method</th>
                                <th>Amount</th>
                                <th>Payable Amount</th>
                                <th>Submitted Date</th>
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

        function updateDropdowns() {
            const sendMethod = $('#filter_send_method').val();
            const receiveMethod = $('#filter_receive_method').val();

            $('#filter_receive_method option').prop('disabled', false);
            $('#filter_send_method option').prop('disabled', false);

            if (sendMethod) {
                $('#filter_receive_method option[value="' + sendMethod + '"]').prop('disabled', true);
            }

            if (receiveMethod) {
                $('#filter_send_method option[value="' + receiveMethod + '"]').prop('disabled', true);
            }
        }

        // Attach event listeners
        $('#filter_send_method, #filter_receive_method').on('change', updateDropdowns);

        // Initialize on page load
        updateDropdowns();

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.balance.transfer.history') }}",
                data: function (e) {
                    e.user_id = $('#filter_user_id').val();
                    e.send_method = $('#filter_send_method').val();
                    e.receive_method = $('#filter_receive_method').val();
                },
                dataSrc: function (json) {
                    // Update total balance transfer amount
                    var currencySymbol = '{{ get_site_settings('site_currency_symbol') }}';
                    $('#totalBalanceTransferAmount').text(currencySymbol + ' ' + json.totalBalanceTransferAmount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'send_method', name: 'send_method' },
                { data: 'receive_method', name: 'receive_method' },
                { data: 'amount', name: 'amount' },
                { data: 'payable_amount', name: 'payable_amount' },
                { data: 'created_at', name: 'created_at' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });
        // Filter Data
        $('.filter_data').keyup(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        const withdrawChargePercentage = {{ get_default_settings("withdraw_balance_transfer_charge_percentage") }};
        const depositChargePercentage = {{ get_default_settings("deposit_balance_transfer_charge_percentage") }};

        // Initially hide all relevant divs
        $('#deposit_balance_check_div, #withdraw_balance_check_div, #withdraw_balance_transfer_charge_div, #deposit_balance_transfer_charge_div').hide();
        $('#receive_method_div').hide();

        // Get user-wise withdraw balance
        $('#user_id').change(function () {
            var user_id = $(this).val();
            var url = "{{ route('backend.withdraw.request.store.user.withdraw.balance', ':id') }}";
            url = url.replace(':id', user_id);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#amount').val(response.withdraw_balance);
                    $('#amount').attr('max', response.withdraw_balance);
                    $('#max_withdraw_amount').text(' and maximum withdraw amount is ' + '{{ get_site_settings('site_currency_symbol') }} ' + response.withdraw_balance);

                    calculatePayableAmount();
                }
            });
        });

        // Handle Send Method change
        $('#sendMethod').on('change', function () {
            const method = $(this).val();

            // Clear Receive Method and hide its div initially
            $('#receiveMethod').val('');
            $('#receive_method_div').hide();

            // Toggle visibility of corresponding divs based on Send Method
            if (method === "Withdraw Balance") {
                $('#deposit_balance_check_div, #deposit_balance_transfer_charge_div').hide();
                $('#withdraw_balance_check_div, #withdraw_balance_transfer_charge_div').show();
            } else if (method === "Deposit Balance") {
                $('#withdraw_balance_check_div, #withdraw_balance_transfer_charge_div').hide();
                $('#deposit_balance_check_div, #deposit_balance_transfer_charge_div').show();
            } else {
                $('#deposit_balance_check_div, #withdraw_balance_check_div, #withdraw_balance_transfer_charge_div, #deposit_balance_transfer_charge_div').hide();
            }

            // Enable Receive Method div only if a valid Send Method is selected
            if (method) {
                $('#receive_method_div').show();

                // Disable the corresponding option in Receive Method
                $('#receiveMethod option').prop('disabled', false); // Enable all options
                if (method === "Withdraw Balance") {
                    $('#receiveMethod option[value="Withdraw Balance"]').prop('disabled', true);
                } else if (method === "Deposit Balance") {
                    $('#receiveMethod option[value="Deposit Balance"]').prop('disabled', true);
                }
            }

            // Recalculate payable amount
            calculatePayableAmount();
        });

        // Handle Transfer Amount input
        $('#transferAmount').on('input', function () {
            calculatePayableAmount();
        });

        // Function to calculate payable amount
        function calculatePayableAmount() {
            const transferAmount = parseFloat($('#transferAmount').val());
            const sendMethod = $('#sendMethod').val();

            if (transferAmount > 0 && sendMethod) {
                let chargePercentage = 0;

                // Determine charge percentage based on send method
                if (sendMethod === "Withdraw Balance") {
                    chargePercentage = withdrawChargePercentage;
                } else if (sendMethod === "Deposit Balance") {
                    chargePercentage = depositChargePercentage;
                }

                const chargeAmount = (transferAmount * chargePercentage) / 100;
                const payableAmount = transferAmount - chargeAmount;

                $('#payable_amount').val(payableAmount.toFixed(2));
            } else {
                $('#payable_amount').val(0);
            }
        }

        // Calculate Payable Amount on input change
        $('#amount, #charge_percentage').on('input change', function () {
            calculatePayableAmount();
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('balance.transfer.store') }}",
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
                        if (response.status == 401) {
                            toastr.error(response.error);
                        }else{
                            $('.createModel').modal('hide');
                            $('#createForm')[0].reset();
                            $("#deposit_balance_check").val(response.deposit_balance);
                            $("#withdraw_balance_check").val(response.withdraw_balance);
                            $("#deposit_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                            $("#withdraw_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.withdraw_balance);
                            $('#allDataTable').DataTable().ajax.reload();
                            toastr.success('Deposit request sent successfully.');
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


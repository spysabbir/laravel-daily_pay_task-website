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
                    @can('balance.transfer.store')
                    <!-- Balance Transfer Modal -->
                    <button type="button" class="btn btn-primary m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".createModel">
                        Transfer <i data-feather="dollar-sign"></i>
                    </button>
                    @endcan
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
                                            <select class="form-select user-select2-single" id="user_id" name="user_id" required data-width="100%">
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
                                            <span class="text-danger error-text send_method_error"></span>
                                        </div>
                                        <div class="mb-3" id="receive_method_div" style="display: none">
                                            <label for="receiveMethod" class="form-label">Receive Method</label>
                                            <input type="text" class="form-control" id="receiveMethod" name="receive_method" readonly>
                                            <span class="text-danger error-text receive_method_error"></span>
                                        </div>
                                        <div class="mb-3" id="deposit_balance_check_div" style="display: none">
                                            <label for="deposit_balance_check" class="form-label">Deposit Balance</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="deposit_balance_check" value="{{ Auth::user()->deposit_balance }}" disabled>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="withdraw_balance_check_div" style="display: none">
                                            <label for="withdraw_balance_check" class="form-label">Withdraw Balance</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="withdraw_balance_check" value="{{ Auth::user()->withdraw_balance }}" disabled>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="transfer_amount_div" style="display: none">
                                            <label for="transferAmount" class="form-label">Transfer Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="transferAmount" name="amount" min="1" placeholder="Transfer Amount" required>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <small class="text-info d-block">Note: Minimum transfer amount is {{ get_site_settings('site_currency_symbol') }} 1<span id="max_transfer_amount"></span>.</small>
                                            <span class="text-danger error-text amount_error"></span>
                                        </div>
                                        <div class="mb-3" id="withdraw_balance_transfer_charge_div" style="display: none">
                                            <label for="withdraw_balance_transfer_charge_percentage" class="form-label">Withdraw Balance Transfer Charge Percentage</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="withdraw_balance_transfer_charge_percentage" name="charge_percentage" min="0" value="{{ get_default_settings('withdraw_balance_transfer_charge_percentage') }}" placeholder="Charge Percentage">
                                                <span class="input-group-text input-group-addon">%</span>
                                            </div>
                                            <small class="text-info d-block">Note: Default transfer charge percentage is {{ get_default_settings('withdraw_balance_transfer_charge_percentage') }}%.</small>
                                            <span class="text-danger error-text charge_percentage_error"></span>
                                        </div>
                                        <div class="mb-3" id="deposit_balance_transfer_charge_div" style="display: none">
                                            <label for="deposit_balance_transfer_charge_percentage" class="form-label">Deposit Balance Transfer Charge Percentage</label>
                                            <div class="input-group">
                                            <input type="number" class="form-control" id="deposit_balance_transfer_charge_percentage" name="charge_percentage" min="0" value="{{ get_default_settings('deposit_balance_transfer_charge_percentage') }}" placeholder="Charge Percentage">
                                                <span class="input-group-text input-group-addon">%</span>
                                            </div>
                                            <small class="text-info d-block">Note: Default transfer charge percentage is {{ get_default_settings('deposit_balance_transfer_charge_percentage') }}%.</small>
                                            <span class="text-danger error-text charge_percentage_error"></span>
                                        </div>
                                        <div class="mb-3" id="payable_amount_div" style="display: none">
                                            <label for="payable_amount" class="form-label">Payable Amount</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="payable_amount" value="0" placeholder="Payable Amount" disabled>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Transfer</button>
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

        // Update dropdowns
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
        $('#filter_send_method, #filter_receive_method').on('change', updateDropdowns);
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

        // Select2
        $.fn.select2.defaults.set("dropdownParent", $(document.body));
        if ($(".user-select2-single").length) {
            $(".user-select2-single").select2({
                dropdownParent: $('.createModel'),
                placeholder: "-- Select User --",
            });
        }

        /// Default all div hide when click on modal open
        $('.createModel').on('show.bs.modal', function (e) {
            $('#receive_method_div, #deposit_balance_check_div, #withdraw_balance_check_div, #transfer_amount_div, #withdraw_balance_transfer_charge_div, #deposit_balance_transfer_charge_div, #payable_amount_div').hide();

            $('#createForm')[0].reset();
            $(document).find('span.error-text').text('');

            if ($(".user-select2-single").length) {
                $(".user-select2-single").val('').trigger('change');
            }
        });

        // Handle Send Method change
        function handleBalanceTransfer() {
            const user_id = $('#user_id').val();
            const send_method = $('#sendMethod').val();

            // Check if both user_id and send_method are selected
            if (user_id && send_method) {
                var url = "{{ route('backend.balance.transfer.store.user.balance', ':id') }}";
                url = url.replace(':id', user_id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#deposit_balance_check").val(response.deposit_balance);
                        $("#withdraw_balance_check").val(response.withdraw_balance);

                        // Set max transfer amount text inside success callback after response is received
                        if (send_method === "Withdraw Balance") {
                            $('#deposit_balance_check_div, #deposit_balance_transfer_charge_div').hide();
                            $('#withdraw_balance_check_div, #withdraw_balance_transfer_charge_div').show();
                            $('#receiveMethod').val('Deposit Balance');
                            $("#max_transfer_amount").text(' and maximum transfer amount is ' + '{{ get_site_settings('site_currency_symbol') }} ' + response.withdraw_balance);
                            $('#transferAmount').attr('max', response.withdraw_balance);
                        } else if (send_method === "Deposit Balance") {
                            $('#withdraw_balance_check_div, #withdraw_balance_transfer_charge_div').hide();
                            $('#deposit_balance_check_div, #deposit_balance_transfer_charge_div').show();
                            $('#receiveMethod').val('Withdraw Balance');
                            $("#max_transfer_amount").text(' and maximum transfer amount is ' + '{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                            $('#transferAmount').attr('max', response.deposit_balance);
                        } else {
                            $('#deposit_balance_check_div, #withdraw_balance_check_div, #withdraw_balance_transfer_charge_div, #deposit_balance_transfer_charge_div').hide();
                        }

                        // Show relevant fields when valid selections are made
                        $('#receive_method_div, #transfer_amount_div, #payable_amount_div').show();

                        // Recalculate payable amount
                        calculatePayableAmount();
                    }
                });

                // Reset fields initially
                $('#receiveMethod, #transferAmount').val('');
                $('#receive_method_div, #transfer_amount_div, #payable_amount_div').hide();
            } else {
                // Hide all dependent fields if conditions are not met
                $('#receive_method_div, #transfer_amount_div, #payable_amount_div').hide();
                $('#deposit_balance_check_div, #withdraw_balance_check_div, #withdraw_balance_transfer_charge_div, #deposit_balance_transfer_charge_div').hide();
            }
        }

        // Trigger when either user_id or sendMethod changes
        $('#user_id, #sendMethod').on('change', handleBalanceTransfer);

        // Function to calculate payable amount
        function calculatePayableAmount() {
            const transferAmount = parseFloat($('#transferAmount').val());
            const sendMethod = $('#sendMethod').val();

            const withdrawChargePercentage = parseFloat($('#withdraw_balance_transfer_charge_percentage').val());
            const depositChargePercentage = parseFloat($('#deposit_balance_transfer_charge_percentage').val());

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
        $('#transferAmount, #deposit_balance_transfer_charge_percentage, #withdraw_balance_transfer_charge_percentage').on('input change', function () {
            calculatePayableAmount();
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('backend.balance.transfer.store') }}",
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


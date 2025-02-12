@extends('layouts.template_master')

@section('title', 'Deposit')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="text">
                    <h3 class="card-title">Deposit List</h3>
                    <p class="mb-0">Note: You can deposit money by using Bkash, Nagad, Rocket. After depositing money, you have to submit a deposit request with the transaction id. Minimum deposit amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('min_deposit_amount') }} and maximum deposit amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('max_deposit_amount') }}. After submitting the deposit request then admin will verify your request and add the money to your account. If you have any problem, please contact with us.</p>
                </div>
                <div class="mt-3 d-flex align-items-center justify-content-between flex-wrap">
                    <div class="alert alert-info" role="alert">
                        <span class="alert-heading text-center">
                            <i class="link-icon" data-feather="credit-card"></i>
                            Total Deposit Amount: <span id="total_deposit_amount">0</span>
                        </span>
                    </div>
                    <!-- Deposit Modal -->
                    <button type="button" class="btn btn-primary m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".createModel">
                        Deposit <i data-feather="dollar-sign"></i>
                    </button>
                    <div class="modal fade createModel" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModelLabel">Deposit</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="createForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="method" class="form-label">Deposit Method <span class="text-danger">*</span></label>
                                            <select class="form-select" id="method" name="method" required>
                                                <option value="">-- Select Deposit Method --</option>
                                                <option value="Bkash">Bkash</option>
                                                <option value="Nagad">Nagad</option>
                                                <option value="Rocket">Rocket</option>
                                            </select>
                                            <span class="text-danger error-text method_error"></span>
                                        </div>
                                        <div class="mb-3" id="deposit_account_div">
                                            <label for="deposit_account" class="form-label">Deposit Account Number</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="deposit_account" placeholder="Deposit Account Number" disabled>
                                                <button class="btn btn-outline-secondary" type="button" id="copyButton">Copy</button>
                                            </div>
                                            <small class="text-warning">Note: Please cash out money to this account number. Please do not send money to this account number.</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="number" class="form-label">Deposit Number <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="number" name="number" placeholder="Deposit Number" required>
                                            <small class="text-info d-block">Note: The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).</small>
                                            <span class="text-danger error-text number_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="transaction_id" class="form-label">Transaction Id <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Transaction Id" required>
                                            <span class="text-danger error-text transaction_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="deposit_amount" class="form-label">Deposit Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="deposit_amount" name="amount" min="{{ get_default_settings('min_deposit_amount') }}" max="{{ get_default_settings('max_deposit_amount') }}" placeholder="Deposit Amount" required>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <small class="text-info d-block">Note: Minimum deposit amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('min_deposit_amount') }} and maximum deposit amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('max_deposit_amount') }}.</small>
                                            <span class="text-danger error-text amount_error"></span>
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
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_method" class="form-label">Deposit Method</label>
                                <select class="form-select filter_data" id="filter_method">
                                    <option value="">-- Select Deposit Method --</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Rocket">Rocket</option>
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
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Deposit Method</th>
                                <th>Deposit Number</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
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

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('deposit') }}",
                data: function (e) {
                    e.status = $('#filter_status').val();
                    e.method = $('#filter_method').val();
                },
                dataSrc: function (json) {
                    var currencySymbol = '{{ get_site_settings('site_currency_symbol') }}';
                    $('#total_deposit_amount').text(currencySymbol + ' ' + json.totalDepositAmount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number'},
                { data: 'transaction_id', name: 'transaction_id' },
                { data: 'amount', name: 'amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'approved_or_rejected_at', name: 'approved_or_rejected_at' },
                { data: 'status', name: 'status' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Deposit Account Mapping
        const methodSettings = {
            Bkash: "{{ get_default_settings('deposit_bkash_account') }}",
            Nagad: "{{ get_default_settings('deposit_nagad_account') }}",
            Rocket: "{{ get_default_settings('deposit_rocket_account') }}",
        };
        $('#deposit_account_div').hide();
        $('#method').on('change', function () {
            const method = $(this).val();
            const account = methodSettings[method] || "";

            if (account) {
                $('#deposit_account_div').show();
            } else {
                $('#deposit_account_div').hide();
            }
            $('#deposit_account').val(account);
        });

        // Copy Account Number
        $('#copyButton').click(function() {
            var accountNumber = $('#deposit_account').val();
            navigator.clipboard.writeText(accountNumber).then(function() {
                toastr.success('Account number copied to clipboard');
            }, function(err) {
                toastr.error('Failed to copy account number');
            });
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('deposit.store') }}",
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

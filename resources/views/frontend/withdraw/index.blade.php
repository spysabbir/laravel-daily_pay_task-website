@extends('layouts.template_master')

@section('title', 'Withdraw')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Withdraw List</h3>
                    <p class="mb-0">You can Withdraw money by using Bkash, Nagad, Rocket. Minimum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('min_withdraw_amount') }} and maximum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('max_withdraw_amount') }}. Withdraw charge percentage is {{ get_default_settings('withdraw_charge_percentage') }}%. Instant withdraw charge is {{ get_default_settings('instant_withdraw_charge') }} {{ get_site_settings('site_currency_symbol') }}. After submitting the withdraw request, the admin will verify your request and send the money to your account number. If you have any problem, please contact the admin.</p>
                </div>
                <div class="action-btn">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".createModel"><i data-feather="plus-circle"></i></button>
                    <!-- Create Modal -->
                    <div class="modal fade createModel" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModelLabel">Create</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="createForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Withdraw Type</label>
                                            <select class="form-select" id="type" name="type">
                                                <option value="">-- Select Withdraw Type --</option>
                                                <option value="Ragular">Ragular</option>
                                                <option value="Instant">Instant</option>
                                            </select>
                                            <small class="text-info">
                                                <strong id="ragular">Withdraw request will be processed within 24 hours.</strong>
                                                <strong id="instant">Withdraw request will be processed in 20 minutes. But you will be charged an extra {{ get_default_settings('instant_withdraw_charge') }} {{ get_site_settings('site_currency_symbol') }}.</strong>
                                            </small>
                                            <span class="text-danger error-text type_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="method" class="form-label">Withdraw Method</label>
                                            <select class="form-select" id="method" name="method">
                                                <option value="">-- Select Withdraw Method --</option>
                                                <option value="Bkash">Bkash</option>
                                                <option value="Nagad">Nagad</option>
                                                <option value="Rocket">Rocket</option>
                                            </select>
                                            <span class="text-danger error-text method_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="number" class="form-label">Withdraw Number</label>
                                            <input type="text" class="form-control" id="number" name="number" placeholder="Withdraw Number">
                                            <span class="text-danger error-text number_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Withdraw Amount</label>
                                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Withdraw Amount">
                                            <small class="text-info">Minimum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('min_withdraw_amount') }} and maximum withdraw amount is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('max_withdraw_amount') }}</small>
                                            <span class="text-danger error-text amount_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Withdraw Charge Percentage</label>
                                            <input type="text" class="form-control" value="{{ get_default_settings('withdraw_charge_percentage') }} %" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Payable Amount</label>
                                            <input type="number" class="form-control" id="payable_amount" placeholder="Payable Amount" disabled>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading text-center">
                            <i class="link-icon" data-feather="credit-card"></i>
                            Total Withdraw: {{ get_site_settings('site_currency_symbol') }} {{ $total_withdraw }}
                        </h4>
                    </div>
                </div>
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
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
                                <th>Withdraw Amount</th>
                                <th>Withdraw Method</th>
                                <th>Withdraw Number</th>
                                <th>Payable Amount</th>
                                <th>Created At</th>
                                <td>Status</td>
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
                $('#payable_amount').val(payable_amount);
            }else if ($('#type').val() == 'Instant') {
                $('#ragular').hide();
                $('#instant').show();
                var amount = $('#amount').val();
                var charge = {{ get_default_settings('withdraw_charge_percentage') }};
                var instant_withdraw_charge = {{ get_default_settings('instant_withdraw_charge') }};
                var payable_amount = amount - (amount * charge / 100);
                if(amount == ''){
                        $('#payable_amount').val(payable_amount);
                    }else{
                        $('#payable_amount').val(payable_amount - instant_withdraw_charge);
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
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number'},
                { data: 'payable_amount', name: 'payable_amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Calculate Payable Amount
        $('#amount').keyup(function(){
            var amount = $(this).val();
            var instant_withdraw_charge = {{ get_default_settings('instant_withdraw_charge') }};
            var charge = {{ get_default_settings('withdraw_charge_percentage') }};
            var payable_amount = amount - (amount * charge / 100);
            if ($('#type').val() == 'Instant') {
                $('#payable_amount').val(payable_amount - instant_withdraw_charge);
            }else{
                $('#payable_amount').val(payable_amount);
            }
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
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
                            toastr.success('Withdraw request sent successfully.');
                        }else{
                            toastr.error(response.error);
                        }
                    }
                }
            });
        });
    });
</script>
@endsection

@extends('layouts.template_master')

@section('title', 'Withdraw Request (Pending)')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Withdraw Request (Pending)</h3>
                <h3>Total: <span id="total_withdraws_count">0</span></h3>
                <div class="action-btn">
                    @can('withdraw.request.store')
                    <button type="button" class="btn btn-primary m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".createModel">Withdraw <i data-feather="plus-circle"></i></button>
                    @endcan
                    <!-- Normal Withdraw Modal -->
                    <div class="modal fade createModel select2Model" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
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
                                            <label for="user_id" class="form-label">User Name <span class="text-danger">*</span></label>
                                            <select class="form-select js-select2-single" id="user_id" name="user_id" required data-width="100%">
                                                <option value="">-- Select User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text user_id_error"></span>
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
                                                <input type="number" class="form-control" id="amount" name="amount" min="25" placeholder="Withdraw Amount" required>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <small class="text-info d-block">Note: Minimum withdraw amount is {{ get_site_settings('site_currency_symbol') }} 25<span id="max_withdraw_amount"></span>.</small>
                                            <span class="text-danger error-text amount_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="charge_percentage">Withdraw Charge Percentage <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="charge_percentage" name="charge_percentage" min="0" value="{{ get_default_settings('withdraw_charge_percentage') }}" placeholder="Withdraw Charge Percentage" required>
                                                <span class="input-group-text input-group-addon">%</span>
                                            </div>
                                            <small class="text-info d-block">Note: Default withdraw charge percentage is {{ get_default_settings('withdraw_charge_percentage') }}%.</small>
                                            <span class="text-danger error-text charge_percentage_error"></span>
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
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Withdraw Type --</option>
                                    <option value="Ragular">Ragular</option>
                                    <option value="Instant">Instant</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_method" class="form-label">Method</label>
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
                                <label for="filter_number" class="form-label">Number</label>
                                <input type="number" id="filter_number" class="form-control filter_data" placeholder="Search Number">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="pendingDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Type</th>
                                <th>Method</th>
                                <th>Number</th>
                                <th>Withdraw Amount</th>
                                <th>Payable Amount</th>
                                <th>Submitted Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">View</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="modalBody">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

        // Pending Data
        $('#pendingDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.withdraw.request') }}",
                data: function (e) {
                    e.method = $('#filter_method').val();
                    e.type = $('#filter_type').val();
                    e.user_id = $('#filter_user_id').val();
                    e.number = $('#filter_number').val();
                },
                dataSrc: function (json) {
                    // Update total deposit count
                    $('#total_withdraws_count').text(json.totalWithdrawsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'type', name: 'type' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number' },
                { data: 'amount', name: 'amount' },
                { data: 'payable_amount', name: 'payable_amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#pendingDataTable').DataTable().ajax.reload();
        });
        // Filter Data
        $('.filter_data').keyup(function(){
            $('#pendingDataTable').DataTable().ajax.reload();
        });

        function calculatePayableAmount() {
            var amount = parseFloat($('#amount').val()) || 0;
            var charge = parseFloat($('#charge_percentage').val()) || 0;
            var payable_amount = amount - (amount * charge / 100);

            if (payable_amount < 0) {
                payable_amount = 0;
            }
            $('#payable_withdraw_amount').val(payable_amount.toFixed(2));
        }

        // Calculate Payable Amount on input change
        $('#amount, #charge_percentage').on('input change', function () {
            calculatePayableAmount();
        });

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

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('backend.withdraw.request.store') }}",
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
                            $('#pendingDataTable').DataTable().ajax.reload();
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

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.withdraw.request.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);

                    $('.viewModal').modal('show');
                },
            });
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();

            var id = $('#withdraw_id').val();
            var url = "{{ route('backend.withdraw.request.status.change', ":id") }}";
            url = url.replace(':id', id)

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: url,
                type: "PUT",
                data: $(this).serialize(),
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        if (response.status == 401) {
                            $(".viewModal").modal('hide');
                            $('#pendingDataTable').DataTable().ajax.reload();
                            toastr.info(response.error);
                        } else {
                            $('#pendingDataTable').DataTable().ajax.reload();
                            $(".viewModal").modal('hide');
                            toastr.success('Withdraw status change successfully.');
                        }
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        })
    });
</script>
@endsection


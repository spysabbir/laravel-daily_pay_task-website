@extends('layouts.template_master')

@section('title', 'Bonus')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Bonus</h3>
                <h3>Total Amount: <span id="totalBonusAmount">0</span></h3>
                <div class="action-btn">
                    @can('bonus.store')
                    <!-- Normal Bonus Modal -->
                    <button type="button" class="btn btn-primary m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".createModel">Bonus <i data-feather="plus-circle"></i></button>
                    @endcan
                    <div class="modal fade createModel select2Model" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModelLabel">Bonus</h5>
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
                                            <label for="amount" class="form-label">Bonus Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="amount" name="amount" min="1" placeholder="Bonus Amount" required>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <span class="text-danger error-text amount_error"></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Bonus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @can('bonus.import')
                    <!-- Normal Bonus Modal -->
                    <button type="button" class="btn btn-info m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".importModel">Import <i data-feather="plus-circle"></i></button>
                    @endcan
                    <div class="modal fade importModel select2Model" tabindex="-1" aria-labelledby="importModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="importModelLabel">Import</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="importForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Import Bonus Sample File - </strong>
                                            <a href="{{ asset('uploads/bonus_import.xlsx') }}" class="text-primary" download>Download</a>
                                        </div>
                                        <div class="mb-3">
                                            <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="file" name="file" accept=".xls, .xlsx">
                                            <span class="text-danger error-text file_error"></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import</button>
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
                                <label for="filter_bonus_by_user_id" class="form-label">Bonus By User Id</label>
                                <input type="number" id="filter_bonus_by_user_id" class="form-control filter_data" placeholder="Search Bonus By User Id">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="Referral Registration Bonus">Referral Registration Bonus</option>
                                    <option value="Referral Withdrawal Bonus">Referral Withdrawal Bonus</option>
                                    <option value="Proof Task Approved Bonus">Proof Task Approved Bonus</option>
                                    <option value="Site Special Bonus">Site Special Bonus</option>
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
                                <th>User Name</th>
                                <th>Bonus By</th>
                                <th>Bonus By User Name</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
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
                url: "{{ route('backend.bonus.history') }}",
                data: function (e) {
                    e.user_id = $('#filter_user_id').val();
                    e.bonus_by = $('#filter_bonus_by_user_id').val();
                    e.type = $('#filter_type').val();
                },
                dataSrc: function (json) {
                    // Update total bonuses amount
                    var currencySymbol = '{{ get_site_settings('site_currency_symbol') }}';
                    $('#totalBonusAmount').text(currencySymbol + ' ' + json.totalBonusAmount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_name', name: 'user_name' },
                { data: 'bonus_by', name: 'bonus_by' },
                { data: 'bonus_by_user_name', name: 'bonus_by_user_name' },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
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

        // Reset Form
        $('.createModel').on('hidden.bs.modal', function () {
            $('#createForm')[0].reset();
            $(document).find('span.error-text').text('');

            if ($(".user-select2-single").length) {
                $(".user-select2-single").val('').trigger('change');
            }
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('backend.bonus.store') }}",
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
                        $('.createModel').modal('hide');
                        $('#createForm')[0].reset();
                        $('#allDataTable').DataTable().ajax.reload();
                        toastr.success('Bonus Sent Successfully');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // Import Data
        $('#importForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Importing...");

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('backend.bonus.import') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        $('.importModel').modal('hide');
                        $('#importForm')[0].reset();
                        $('#allDataTable').DataTable().ajax.reload();
                        toastr.success('Bonus Imported Successfully');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Something went wrong.');
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Import");
                }
            });
        });

    });
</script>
@endsection

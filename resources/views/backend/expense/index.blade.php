@extends('layouts.template_master')

@section('title', 'Expense')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Expense List</h3>
                <div class="action-btn">
                    @can('expense.create')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".createModel"><i data-feather="plus-circle"></i></button>
                    @endcan
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
                                            <label for="expense_category_id" class="form-label">Category</label>
                                            <select class="form-select" id="expense_category_id" name="expense_category_id">
                                                <option value="">-- Select Category --</option>
                                                @foreach ($expense_categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text expense_category_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Title">
                                            <span class="text-danger error-text title_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" placeholder="Description"></textarea>
                                            <span class="text-danger error-text description_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount">
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <span class="text-danger error-text amount_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="expense_date" class="form-label">Expense Date</label>
                                            <input type="date" class="form-control" id="expense_date" name="expense_date">
                                            <span class="text-danger error-text expense_date_error"></span>
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
                    @can('expense.trash')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target=".trashModel"><i data-feather="trash-2"></i></button>
                    @endcan
                    <!-- Trash Modal -->
                    <div class="modal fade trashModel" tabindex="-1" aria-labelledby="trashModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="trashModelLabel">Trash</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table id="trashDataTable" class="table w-100">
                                            <thead>
                                                <tr>
                                                    <th>Sl No</th>
                                                    <th>Category</th>
                                                    <th>Title</th>
                                                    <th>Amount</th>
                                                    <th>Expense Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <div class="form-group">
                                <label for="filter_status" class="form-label">Status</label>
                                <select class="form-select filter_data" id="filter_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="form-group">
                                <label for="filter_expense_category_id" class="form-label">Category</label>
                                <select class="form-select filter_data" id="filter_expense_category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($expense_categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="form-group">
                                <label for="filter_expense_date" class="form-label">Expense Date</label>
                                <input type="date" class="form-control filter_data" id="filter_expense_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Category</th>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Expense Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">View</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="viewBody">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Edit Modal -->
                            <div class="modal fade editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <form class="forms-sample" id="editForm">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" id="expense_id">
                                                <div class="mb-3">
                                                    <label for="update_expense_category_id" class="form-label">Category</label>
                                                    <select class="form-select" id="update_expense_category_id" name="expense_category_id">
                                                        <option value="">-- Select Category --</option>
                                                        @foreach ($expense_categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger error-text update_expense_category_id_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="update_title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="update_title" name="title" placeholder="Title">
                                                    <span class="text-danger error-text update_title_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="update_description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="update_description" name="description" placeholder="Description"></textarea>
                                                    <span class="text-danger error-text update_description_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="update_amount" class="form-label">Amount</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="update_amount" name="amount" placeholder="Amount">
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                    <span class="text-danger error-text update_amount_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="update_expense_date" class="form-label">Expense Date</label>
                                                    <input type="date" class="form-control" id="update_expense_date" name="expense_date">
                                                    <span class="text-danger error-text update_expense_date_error"></span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Edit</button>
                                            </div>
                                        </form>
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

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.expense.index') }}",
                data: function (e) {
                    e.status = $('#filter_status').val();
                    e.expense_category_id = $('#filter_expense_category_id').val();
                    e.expense_date = $('#filter_expense_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'expense_category_name', name: 'expense_category_name' },
                { data: 'title', name: 'title' },
                { data: 'amount', name: 'amount' },
                { data: 'expense_date', name: 'expense_date' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('backend.expense.store') }}",
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
                        toastr.success('Expense store successfully.');
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
            var url = "{{ route('backend.expense.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#viewBody').html(response);
                },
            });
        });

        // Edit Data
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.expense.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#expense_id').val(response.id);
                    $('#update_expense_category_id').val(response.expense_category_id);
                    $('#update_title').val(response.title);
                    $('#update_description').val(response.description);
                    $('#update_amount').val(response.amount);
                    $('#update_expense_date').val(response.expense_date);
                },
            });
        });

        // Update Data
        $('#editForm').submit(function (event) {
            event.preventDefault();

            var id = $('#expense_id').val();
            var url = "{{ route('backend.expense.update', ":id") }}";
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
                        $(".editModal").modal('hide');
                        $('#allDataTable').DataTable().ajax.reload();
                        toastr.success('Expense update successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // Soft Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.expense.destroy', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You can bring it back though!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        success: function(response) {
                            $('#allDataTable').DataTable().ajax.reload();
                            $('#trashDataTable').DataTable().ajax.reload();
                            toastr.warning('Expense soft delete successfully.');
                        }
                    });
                }
            })
        })

        // Trash Data
        @can('expense.trash')
        $('#trashDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.expense.trash') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'expense_category_name', name: 'expense_category_name' },
                { data: 'title', name: 'title' },
                { data: 'amount', name: 'amount' },
                { data: 'expense_date', name: 'expense_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
        @endcan

        // Restore Data
        $(document).on('click', '.restoreBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.expense.restore', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $(".trashModel").modal('hide');
                    $('#allDataTable').DataTable().ajax.reload();
                    $('#trashDataTable').DataTable().ajax.reload();
                    toastr.success('Expense restore successfully.');
                },
            });
        });

        // Force Delete Data
        $(document).on('click', '.forceDeleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.expense.delete', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(response) {
                            $(".trashModel").modal('hide');
                            $('#trashDataTable').DataTable().ajax.reload();
                            toastr.error('Expense force delete successfully.');
                        }
                    });
                }
            })
        })

        // Status Change Data
        $(document).on('click', '.statusBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.expense.status', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#allDataTable').DataTable().ajax.reload();
                    toastr.success('Expense status change successfully.');
                },
            });
        });
    });
</script>
@endsection


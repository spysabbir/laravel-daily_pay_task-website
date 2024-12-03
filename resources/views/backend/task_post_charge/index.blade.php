@extends('layouts.template_master')

@section('title', 'Task Post Charge')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Task Post Charge List</h3>
                <div class="action-btn">
                    @can('task_post_charge.create')
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
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select" id="category_id" name="category_id">
                                                <option value="">-- Select Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text category_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sub_category_id" class="form-label">Sub Category</label>
                                            <select class="form-select get_sub_categories" id="sub_category_id" name="sub_category_id">
                                                <option value="">-- Select Category First --</option>
                                            </select>
                                            <span class="text-danger error-text sub_category_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="child_category_id" class="form-label">Child Category</label>
                                            <select class="form-select get_child_categories" id="child_category_id" name="child_category_id">
                                                <option value="">-- Select Sub Category First --</option>
                                            </select>
                                            <span class="text-danger error-text child_category_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="min_charges" class="form-label">Min Charge</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="min_charges" name="min_charge" placeholder="Min Charge">
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <span class="text-danger error-text min_charge_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="max_charges" class="form-label">Max Charge</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="max_charges" name="max_charge" placeholder="Max Charge">
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <span class="text-danger error-text max_charge_error"></span>
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
                    @can('task_post_charge.trash')
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
                                                    <th>Category Name</th>
                                                    <th>Sub Category Name</th>
                                                    <th>Child Category Name</th>
                                                    <th>Min Charge</th>
                                                    <th>Max Charge</th>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select filter_data" id="filter_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
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
                                <th>Category Name</th>
                                <th>Sub Category Name</th>
                                <th>Child Category Name</th>
                                <th>Min Charge</th>
                                <th>Max Charge</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

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
                                                <input type="hidden" id="task_post_charge_id">
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">Category</label>
                                                    <select class="form-select category_id" id="category_id" name="category_id">
                                                        <option value="">-- Select Category --</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger error-text update_category_id_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="sub_category_id" class="form-label">Sub Category</label>
                                                    <select class="form-select sub_category_id get_sub_categories" id="sub_category_id" name="sub_category_id">
                                                        <option value="">-- Select Category First --</option>
                                                    </select>
                                                    <span class="text-danger error-text update_sub_category_id_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="child_category_id" class="form-label">Child Category</label>
                                                    <select class="form-select child_category_id get_child_categories" id="child_category_id" name="child_category_id">
                                                        <option value="">-- Select Sub Category First --</option>
                                                    </select>
                                                    <span class="text-danger error-text update_child_category_id_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="min_charge" class="form-label">Min Charge</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="min_charge" name="min_charge" placeholder="Min Charge">
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                    <span class="text-danger error-text update_min_charge_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="max_charge" class="form-label">Max Charge</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="max_charge" name="max_charge" placeholder="Max Charge">
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                    <span class="text-danger error-text update_max_charge_error"></span>
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

    // Initialize DataTables
    var table = $('#allDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('backend.task_post_charge.index') }}",
            data: function(d) {
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'category_name', name: 'category_name' },
            { data: 'sub_category_name', name: 'sub_category_name' },
            { data: 'child_category_name', name: 'child_category_name' },
            { data: 'min_charge', name: 'min_charge' },
            { data: 'max_charge', name: 'max_charge' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Filter data
    $('.filter_data').change(function() {
        table.ajax.reload();
    });

    // Load subcategories based on the selected category
    function loadSubCategories(category_id, target, selectedSubCategory = null) {
        $.ajax({
            url: "{{ route('backend.task_post_charge.get_sub_categories') }}",
            type: 'GET',
            data: { category_id: category_id },
            success: function(response) {
                $(target).html(response.html);
                if (selectedSubCategory) {
                    $(target).val(selectedSubCategory);
                }
            }
        });
    }

    // Load child categories based on the selected subcategory
    function loadChildCategories(category_id, sub_category_id, target, selectedChildCategory = null) {
        $.ajax({
            url: "{{ route('backend.task_post_charge.get_child_categories') }}",
            type: 'GET',
            data: { category_id: category_id, sub_category_id: sub_category_id },
            success: function(response) {
                $(target).html(response.html);
                if (selectedChildCategory) {
                    $(target).val(selectedChildCategory);
                }
            }
        });
    }

    // Create form submission
    $('#createForm').submit(function(event) {
        event.preventDefault();

        var submitButton = $(this).find("button[type='submit']");
        submitButton.prop("disabled", true).text("Submitting...");

        $.ajax({
            url: "{{ route('backend.task_post_charge.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $(document).find('span.error-text').text('');
            },
            success: function(response) {
                if (response.status === 400) {
                    $.each(response.error, function(prefix, val) {
                        $('span.' + prefix + '_error').text(val[0]);
                    });
                } else {
                    $('.createModel').modal('hide');
                    $('#createForm')[0].reset();
                    table.ajax.reload();
                    toastr.success('Task Post Charge created successfully.');
                }
            },
            complete: function() {
                submitButton.prop("disabled", false).text("Submit");
            }
        });
    });

    // Edit button click event
    $(document).on('click', '.editBtn', function() {
        var id = $(this).data('id');
        var url = "{{ route('backend.task_post_charge.edit', ":id") }}".replace(':id', id);
        $.get(url, function(response) {
            $('#task_post_charge_id').val(response.id);
            $('.category_id').val(response.category_id).change();
            loadSubCategories(response.category_id, '.sub_category_id', response.sub_category_id);
            loadChildCategories(response.category_id, response.sub_category_id, '.child_category_id', response.child_category_id);
            $('#min_charge').val(response.min_charge);
            $('#max_charge').val(response.max_charge);
        });
    });

    // Update form submission
    $('#editForm').submit(function(event) {
        event.preventDefault();

        var id = $('#task_post_charge_id').val();
        var url = "{{ route('backend.task_post_charge.update', ":id") }}".replace(':id', id);

        var submitButton = $(this).find("button[type='submit']");
        submitButton.prop("disabled", true).text("Submitting...");

        $.ajax({
            url: url,
            type: "PUT",
            data: $(this).serialize(),
            beforeSend: function() {
                $(document).find('span.error-text').text('');
            },
            success: function(response) {
                if (response.status === 400) {
                    $.each(response.error, function(prefix, val) {
                        $('span.update_' + prefix + '_error').text(val[0]);
                    });
                } else {
                    $(".editModal").modal('hide');
                    table.ajax.reload();
                    toastr.success('Task Post Charge updated successfully.');
                }
            },
            complete: function() {
                submitButton.prop("disabled", false).text("Submit");
            }
        });
    });

    // Soft delete
    $(document).on('click', '.deleteBtn', function() {
        var id = $(this).data('id');
        var url = "{{ route('backend.task_post_charge.destroy', ":id") }}".replace(':id', id);
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
                    type: 'DELETE',
                    success: function(response) {
                        table.ajax.reload();
                        $('#trashDataTable').DataTable().ajax.reload();
                        toastr.warning('Task Post Charge soft deleted successfully.');
                    }
                });
            }
        });
    });

    // Trash Data
    @can('task_post_charge.trash')
    $('#trashDataTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ajax: {
            url: "{{ route('backend.task_post_charge.trash') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'category_name', name: 'category_name' },
            { data: 'sub_category_name', name: 'sub_category_name' },
            { data: 'child_category_name', name: 'child_category_name' },
            { data: 'min_charge', name: 'min_charge' },
            { data: 'max_charge', name: 'max_charge' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
    @endcan

    // Restore Data
    $(document).on('click', '.restoreBtn', function() {
        var id = $(this).data('id');
        var url = "{{ route('backend.task_post_charge.restore', ":id") }}".replace(':id', id);
        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                $(".trashModel").modal('hide');
                table.ajax.reload();
                $('#trashDataTable').DataTable().ajax.reload();
                toastr.success('Task Post Charge restored successfully.');
            }
        });
    });

    // Force Delete Data
    $(document).on('click', '.forceDeleteBtn', function() {
        var id = $(this).data('id');
        var url = "{{ route('backend.task_post_charge.delete', ":id") }}".replace(':id', id);
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
                    type: 'DELETE',
                    success: function(response) {
                        $(".trashModel").modal('hide');
                        $('#trashDataTable').DataTable().ajax.reload();
                        toastr.error('Task Post Charge force deleted successfully.');
                    }
                });
            }
        });
    });

    // Status change
    $(document).on('click', '.statusBtn', function() {
        var id = $(this).data('id');
        var url = "{{ route('backend.task_post_charge.status', ":id") }}".replace(':id', id);
        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                table.ajax.reload();
                toastr.success('Task Post Charge status changed successfully.');
            }
        });
    });

    // Load subcategories and child categories on category change in create form
    $(document).on('change', '#category_id', function() {
        var category_id = $(this).val();
        loadSubCategories(category_id, '#sub_category_id');
        $('#child_category_id').html('<option value="">-- Select Sub Category First --</option>');
    });

    $(document).on('change', '#sub_category_id', function() {
        var category_id = $('#category_id').val();
        var sub_category_id = $(this).val();
        loadChildCategories(category_id, sub_category_id, '#child_category_id');
    });

    // Load subcategories and child categories on category change in edit form
    $(document).on('change', '.category_id', function() {
        var category_id = $(this).val();
        loadSubCategories(category_id, '.sub_category_id');
        $('.child_category_id').html('<option value="">-- Select Sub Category First --</option>');
    });

    $(document).on('change', '.sub_category_id', function() {
        var category_id = $('.category_id').val();
        var sub_category_id = $(this).val();
        loadChildCategories(category_id, sub_category_id, '.child_category_id');
    });
});
</script>
@endsection


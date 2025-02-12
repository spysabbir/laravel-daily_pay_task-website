@extends('layouts.template_master')

@section('title', 'Child Category')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Child Category List</h3>
                <div class="action-btn">
                    @can('child_category.create')
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
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select class="form-select category-select2-single" id="category_id" name="category_id" required data-width="100%">
                                                <option value="">-- Select Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text category_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sub_category_id" class="form-label">Sub Category <span class="text-danger">*</span></label>
                                            <select class="form-select get_sub_categories subcategory-select2-single" id="sub_category_id" name="sub_category_id" required data-width="100%">
                                                <option value="">-- Select Category First --</option>
                                            </select>
                                            <span class="text-danger error-text sub_category_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Child Category Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Child Category Name">
                                            <span class="text-danger error-text name_error"></span>
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
                    @can('child_category.trash')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target=".trashModel"><i data-feather="trash-2"></i></button>
                    @endcan
                    <!-- Trash Modal -->
                    <div class="modal fade trashModel" tabindex="-1" aria-labelledby="trashModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
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
                                <label for="filter_category_id" class="form-label">Category</label>
                                <select class="form-select filter_data" id="filter_category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_sub_category_id" class="form-label">Sub Category</label>
                                <select class="form-select get_filter_sub_categories filter_data" id="filter_sub_category_id">
                                    <option value="">-- Select Category First --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_status" class="form-label">Status</label>
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
                                                <input type="hidden" id="child_category_id">
                                                <div class="mb-3">
                                                    <label for="edit_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                                    <select class="form-select category-select2-single-edit" id="edit_category_id" name="category_id" required data-width="100%">
                                                        <option value="">-- Select Category --</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger error-text update_category_id_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_sub_category_id" class="form-label">Sub Category <span class="text-danger">*</span></label>
                                                    <select class="form-select get_edit_sub_categories subcategory-select2-single-edit" id="edit_sub_category_id" name="sub_category_id" required data-width="100%">
                                                        <option value="">-- Select Category First --</option>
                                                        @foreach ($sub_categories as $sub_category)
                                                            <option value="{{ $sub_category->id }}">{{ $sub_category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger error-text update_sub_category_id_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="child_category_name" class="form-label">Child Category Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="child_category_name" name="name" placeholder="Child Category Name">
                                                    <span class="text-danger error-text update_name_error"></span>
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
                url: "{{ route('backend.child_category.index') }}",
                data: function (e) {
                    e.category_id = $('#filter_category_id').val();
                    e.sub_category_id = $('#filter_sub_category_id').val();
                    e.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'category_name', name: 'category_name' },
                { data: 'sub_category_name', name: 'sub_category_name' },
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Select2
        $.fn.select2.defaults.set("dropdownParent", $(document.body));
        if ($(".category-select2-single").length) {
            $(".category-select2-single").select2({
                dropdownParent: $('.createModel'),
                placeholder: "-- Select Category --",
            });
        }
        if ($(".subcategory-select2-single").length) {
            $(".subcategory-select2-single").select2({
                dropdownParent: $('.createModel'),
                placeholder: "-- Select Subcategory --",
            });
        }
        if ($(".category-select2-single-edit").length) {
            $(".category-select2-single-edit").select2({
                dropdownParent: $('.editModal'),
                placeholder: "-- Select Category --",
            });
        }
        if ($(".subcategory-select2-single-edit").length) {
            $(".subcategory-select2-single-edit").select2({
                dropdownParent: $('.editModal'),
                placeholder: "-- Select Subcategory --",
            });
        }

        // Reset Form
        $('.createModel').on('hidden.bs.modal', function () {
            $('#createForm')[0].reset();
            $(document).find('span.error-text').text('');

            if ($(".category-select2-single").length) {
                $(".category-select2-single").val('').trigger('change');
            }
            if ($(".subcategory-select2-single").length) {
                $(".subcategory-select2-single").val('').trigger('change');
            }
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('backend.child_category.store') }}",
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
                        toastr.success('Child Category store successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // Load sub-categories when a category is selected in the filter
        $(document).on('change', '#filter_category_id', function () {
            $('#filter_sub_category_id').val(null).trigger('change');
            var category_id = $(this).val();
            loadSubCategories(category_id, '.get_filter_sub_categories');
        });

        // Load sub-categories when a category is selected in the create form
        $(document).on('change', '#category_id', function () {
            var category_id = $(this).val();
            loadSubCategories(category_id, '.get_sub_categories');
        });

        // Load sub-categories when a category is selected in the edit form
        $(document).on('change', '#edit_category_id', function () {
            var category_id = $(this).val();
            loadSubCategories(category_id, '.get_edit_sub_categories');
        });

        // Load sub-categories when a category is selected
        function loadSubCategories(category_id, target, selectedSubCategory = null) {
            $.ajax({
                url: "{{ route('backend.child_category.get_sub_categories') }}",
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

        // Edit Data
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.child_category.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#child_category_id').val(response.id);
                    $('#edit_category_id').val(response.category_id).change();
                    loadSubCategories(response.category_id, '#edit_sub_category_id', response.sub_category_id);
                    $('#child_category_name').val(response.name);

                    $(".editModal").modal('show');
                },
            });
        });

        // Update Data
        $('#editForm').submit(function (event) {
            event.preventDefault();

            var id = $('#child_category_id').val();
            var url = "{{ route('backend.child_category.update', ":id") }}";
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
                        toastr.success('Child Category update successfully.');
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
            var url = "{{ route('backend.child_category.destroy', ":id") }}";
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
                            if (response.status == 400) {
                                toastr.error(response.error);
                            }else{
                                $('#allDataTable').DataTable().ajax.reload();
                                $('#trashDataTable').DataTable().ajax.reload();
                                toastr.warning('Child Category soft delete successfully.');
                            }
                        }
                    });
                }
            })
        })

        // Trash Data
        @can('child_category.trash')
        $('#trashDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.child_category.trash') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'category_name', name: 'category_name' },
                { data: 'sub_category_name', name: 'sub_category_name' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
        @endcan

        // Restore Data
        $(document).on('click', '.restoreBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.child_category.restore', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $(".trashModel").modal('hide');
                    $('#allDataTable').DataTable().ajax.reload();
                    $('#trashDataTable').DataTable().ajax.reload();
                    toastr.success('Child Category restore successfully.');
                },
            });
        });

        // Force Delete Data
        $(document).on('click', '.forceDeleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.child_category.delete', ":id") }}";
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
                            toastr.error('Child Category force delete successfully.');
                        }
                    });
                }
            })
        })

        // Status Change Data
        $(document).on('click', '.statusBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.child_category.status', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#allDataTable').DataTable().ajax.reload();
                    toastr.success('Child Category status change successfully.');
                },
            });
        });
    });
</script>
@endsection


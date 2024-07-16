@extends('layouts.template_master')

@section('title', 'Sub Category')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Sub Category List</h3>
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
                                            <label for="name" class="form-label">Sub Category Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Sub Category Name">
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
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target=".trashModel"><i data-feather="trash-2"></i></button>
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
                                                    <td>Sl No</td>
                                                    <td>Category Name</td>
                                                    <th>Sub Category Name</th>
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
                                <label for="status">Status</label>
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
                                <td>Status</td>
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
                                                <input type="hidden" id="subcategory_id">
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
                                                    <label for="subcategory_name" class="form-label">Sub Category Name</label>
                                                    <input type="text" class="form-control" id="subcategory_name" name="name" placeholder="Sub Category Name">
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
                url: "{{ route('backend.sub_category.index') }}",
                data: function (e) {
                    e.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'category_name', name: 'category_name' },
                { data: 'name', name: 'name' },
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
            $.ajax({
                url: "{{ route('backend.sub_category.store') }}",
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
                        toastr.success('SubCategory store successfully.');
                    }
                }
            });
        });

        // Edit Data
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.sub_category.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#subcategory_id').val(response.id);
                    $('.category_id').val(response.category_id);
                    $('#subcategory_name').val(response.name);
                },
            });
        });

        // Update Data
        $('#editForm').submit(function (event) {
            event.preventDefault();
            var id = $('#subcategory_id').val();
            var url = "{{ route('backend.sub_category.update', ":id") }}";
            url = url.replace(':id', id)
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
                        toastr.success('SubCategory update successfully.');
                    }
                },
            });
        });

        // Soft Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.sub_category.destroy', ":id") }}";
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
                            toastr.warning('SubCategory soft delete successfully.');
                        }
                    });
                }
            })
        })

        // Trash Data
        $('#trashDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.sub_category.trash') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'category_name', name: 'category_name' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Restore Data
        $(document).on('click', '.restoreBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.sub_category.restore', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $(".trashModel").modal('hide');
                    $('#allDataTable').DataTable().ajax.reload();
                    $('#trashDataTable').DataTable().ajax.reload();
                    toastr.success('Sub Category restore successfully.');
                },
            });
        });

        // Force Delete Data
        $(document).on('click', '.forceDeleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.sub_category.delete', ":id") }}";
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
                            toastr.error('Sub Category force delete successfully.');
                        }
                    });
                }
            })
        })

        // Status Change Data
        $(document).on('click', '.statusBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.sub_category.status', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#allDataTable').DataTable().ajax.reload();
                    toastr.success('Sub Category status change successfully.');
                },
            });
        });
    });
</script>
@endsection


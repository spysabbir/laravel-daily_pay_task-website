@extends('layouts.template_master')

@section('title', 'Testimonial')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Testimonial List</h3>
                <div class="action-btn">
                    @can('testimonial.create')
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
                                <form class="forms-sample" id="createForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="photo" class="form-label">Photo <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="photo" name="photo">
                                            <span class="text-danger error-text photo_error"></span>
                                            <img src="" alt="Photo" id="testimonial_photo" style="max-width: 100px; max-height: 100px;" class="mt-2 d-block">
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                                            <span class="text-danger error-text name_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="designation" name="designation" placeholder="Designation">
                                            <span class="text-danger error-text designation_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comment" class="form-label">Comment <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="comment" name="comment" placeholder="Comment" rows="4"></textarea>
                                            <span class="text-danger error-text comment_error"></span>
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
                    @can('testimonial.trash')
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
                                                    <th>Name</th>
                                                    <th>Designation</th>
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
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Comment</th>
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
                                        <form class="forms-sample" id="editForm" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <input type="hidden" id="testimonial_id">
                                                <div class="mb-3">
                                                    <label for="photo" class="form-label">Photo <span class="text-danger">*</span></label>
                                                    <input type="file" class="form-control" id="update_photo" name="photo">
                                                    <span class="text-danger error-text update_photo_error"></span>
                                                    <img src="" alt="Photo" id="update_testimonial_photo" style="max-width: 100px; max-height: 100px;" class="mt-2">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="testimonial_name" class="form-label">Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="testimonial_name" name="name" placeholder="Name">
                                                    <span class="text-danger error-text update_name_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="testimonial_designation" class="form-label">Designation <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="testimonial_designation" name="designation" placeholder="Designation">
                                                    <span class="text-danger error-text update_designation_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="testimonial_comment" class="form-label">Comment <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="testimonial_comment" name="comment" placeholder="Comment" rows="4"></textarea>
                                                    <span class="text-danger error-text update_comment_error"></span>
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
                url: "{{ route('backend.testimonial.index') }}",
                data: function (e) {
                    e.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'designation', name: 'designation' },
                { data: 'comment', name: 'comment' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Store photo preview
        $('#photo').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#testimonial_photo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: "{{ route('backend.testimonial.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
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
                        toastr.success('Testimonial store successfully.');
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
            var url = "{{ route('backend.testimonial.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#viewBody').html(response);

                    $('.viewModal').modal('show');
                },
            });
        });

        // Edit Data
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.testimonial.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#testimonial_id').val(response.id);
                    $('#update_testimonial_photo').attr('src', '/uploads/testimonial_photo/'+response.photo);
                    $('#testimonial_name').val(response.name);
                    $('#testimonial_designation').val(response.designation);
                    $('#testimonial_comment').val(response.comment);

                    $('.editModal').modal('show');
                },
            });
        });

        // Update photo preview
        $('#update_photo').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#update_testimonial_photo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Update Data
        $("#editForm").submit(function(e) {
            e.preventDefault();

            var id = $('#testimonial_id').val();
            var url = "{{ route('backend.testimonial.update', ":id") }}";
            url = url.replace(':id', id)
            const form_data = new FormData(this);

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: url,
                method: 'POST',
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }
                    else{
                        toastr.success('Testimonial update successfully.');
                        $('#allDataTable').DataTable().ajax.reload();
                        $("#editForm")[0].reset();
                        $('.editModal').modal('hide');
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
            var url = "{{ route('backend.testimonial.destroy', ":id") }}";
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
                            toastr.warning('Testimonial soft delete successfully.');
                        }
                    });
                }
            })
        })

        // Trash Data
        @can('testimonial.trash')
        $('#trashDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.testimonial.trash') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'designation', name: 'designation' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
        @endcan

        // Restore Data
        $(document).on('click', '.restoreBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.testimonial.restore', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $(".trashModel").modal('hide');
                    $('#allDataTable').DataTable().ajax.reload();
                    $('#trashDataTable').DataTable().ajax.reload();
                    toastr.success('Testimonial restore successfully.');
                },
            });
        });

        // Force Delete Data
        $(document).on('click', '.forceDeleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.testimonial.delete', ":id") }}";
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
                            toastr.error('Testimonial force delete successfully.');
                        }
                    });
                }
            })
        })

        // Status Change Data
        $(document).on('click', '.statusBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.testimonial.status', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#allDataTable').DataTable().ajax.reload();
                    toastr.success('Testimonial status change successfully.');
                },
            });
        });
    });
</script>
@endsection


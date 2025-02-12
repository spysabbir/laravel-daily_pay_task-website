@extends('layouts.template_master')

@section('title', 'Permission')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Permission List</h3>
                @can('permission.create')
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target=".createModel"><i data-feather="plus-circle"></i></button>
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
                                        <label for="group_name" class="form-label">Permission Group Name <span class="text-danger">*</span></label>
										<div id="bloodhound">
                                            <input type="text" class="form-control typeahead" id="group_name" name="group_name" placeholder="Permission Group Name" size="100%">
                                        </div>
                                        <span class="text-danger error-text group_name_error"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Permission Name">
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
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_group_name" class="form-label">Group Name</label>
                                <select class="form-select filter_data" id="filter_group_name">
                                    <option value="">-- Select Group Name --</option>
                                    @foreach ($permissions as $permission)
                                    <option value="{{ $permission->group_name }}">{{ $permission->group_name }}</option>
                                    @endforeach
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
                                <th>Group Name</th>
                                <th>Permission Name</th>
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
                                                <input type="hidden" id="permission_id">
                                                <div class="mb-3">
                                                    <label for="permission_group_name" class="form-label">Permission Group Name <span class="text-danger">*</span></label>
                                                    <div id="bloodhound">
                                                        <input type="text" class="form-control typeahead" id="permission_group_name" name="group_name" placeholder="Permission Group Name" size="100%">
                                                    </div>
                                                    <span class="text-danger error-text update_group_name_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="permission_name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="permission_name" name="name" placeholder="Permission Name">
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

        // Typeahead
        var permissions = {!! json_encode($permissions) !!};
        var permission_group_name = $.map(permissions, function(item) {
            return item.group_name;
        });
        var bloodhound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: permission_group_name
        });
        $('#bloodhound .typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1,
        },
        {
            name: 'bloodhound',
            source: bloodhound
        });

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.permission.index') }}",
                type: 'GET',
                data: function (d) {
                    d.group_name = $('#filter_group_name').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'group_name', name: 'group_name'},
                { data: 'name', name: 'name' },
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
                url: "{{ route('backend.permission.store') }}",
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
                        toastr.success('Permission store successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // Edit Data
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.permission.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#permission_id').val(response.id);
                    $('#permission_group_name').val(response.group_name);
                    $('#permission_name').val(response.name);
                },
            });
        });

        // Update Data
        $('#editForm').submit(function (event) {
            event.preventDefault();
            var id = $('#permission_id').val();
            var url = "{{ route('backend.permission.update', ":id") }}";
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
                        toastr.success('Permission update successfully.');
                    }
                },
            });
        });

        // Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.permission.destroy', ":id") }}";
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
                            toastr.warning('Permission delete successfully.');
                        }
                    });
                }
            })
        })
    });
</script>
@endsection


@extends('layouts.template_master')

@section('title', 'Employee - Inactive')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title">Employee List - Inactive</h3>
                <h3>Total: <span id="total_users_count">0</span></h3>
                <div class="action-btn">
                    @can('employee.trash')
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target=".trashModel"><i data-feather="trash-2"></i></button>
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
                                                    <th>Email</th>
                                                    <th>Role</th>
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
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select filter_data" id="filter_role">
                                    <option value="">-- Select Role --</option>
                                    @foreach ($roles as $role)
                                        @if (auth()->user()->hasRole('Super Admin') || $role->name !== 'Super Admin')
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">Employee Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search Employee Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_last_activity" class="form-label">Last Activity</label>
                                <select class="form-select filter_data" id="filter_last_activity">
                                    <option value="">-- Select Last Activity --</option>
                                    <option value="Online">Online</option>
                                    <option value="Offline">Offline</option>
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
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Last Activity</th>
                                <th>Created At</th>
                                <th>Roles</th>
                                <th>Action</th>
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
                url: "{{ route('backend.employee.inactive') }}",
                data: function (e) {
                    e.role = $('#filter_role').val();
                    e.user_id = $('#filter_user_id').val();
                    e.last_activity = $('#filter_last_activity').val();
                },
                dataSrc: function (json) {
                    // Update total deposit count
                    $('#total_users_count').text(json.totalUsersCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'last_activity_at', name: 'last_activity_at' },
                { data: 'created_at', name: 'created_at' },
                { data: 'roles', name: 'roles' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Filter Data
        $('.filter_data').keyup(function() {
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Soft Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.employee.destroy', ":id") }}";
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
                            toastr.warning('Employee soft delete successfully.');
                        }
                    });
                }
            })
        })

        // Trash Data
        @can('employee.trash')
        $('#trashDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.employee.trash') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'roles', name: 'roles' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
        @endcan

        // Restore Data
        $(document).on('click', '.restoreBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.employee.restore', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $(".trashModel").modal('hide');
                    $('#allDataTable').DataTable().ajax.reload();
                    $('#trashDataTable').DataTable().ajax.reload();
                    toastr.success('Employee restore successfully.');
                },
            });
        });

        // Force Delete Data
        $(document).on('click', '.forceDeleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.employee.delete', ":id") }}";
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
                            toastr.error('Employee force delete successfully.');
                        }
                    });
                }
            })
        })

        // Status Change Data
        $(document).on('click', '.statusBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.employee.status', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#allDataTable').DataTable().ajax.reload();
                    toastr.success('Employee status change successfully.');
                },
            });
        });
    });
</script>
@endsection


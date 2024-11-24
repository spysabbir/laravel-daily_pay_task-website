@extends('layouts.template_master')

@section('title', 'Posted Task List - Paused')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Posted Task List (Paused)</h3>
                <div class="action-btn">
                    <a href="{{ route('backend.posted_task_list.pending') }}" class="btn btn-info btn-sm">Pending List</a>
                    <a href="{{ route('backend.posted_task_list.rejected') }}" class="btn btn-danger btn-sm">Rejected List</a>
                    <a href="{{ route('backend.posted_task_list.canceled') }}" class="btn btn-warning btn-sm">Canceled List</a>
                    <a href="{{ route('backend.posted_task_list.completed') }}" class="btn btn-success btn-sm">Completed List</a>
                    <a href="{{ route('backend.posted_task_list.running') }}" class="btn btn-primary btn-sm">Running List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_posted_task_id" class="form-label">Posted Task Id</label>
                                <input type="number" id="filter_posted_task_id" class="form-control filter_data" placeholder="Search Posted Task Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="pausedDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task Id</th>
                                <th>User Id</th>
                                <th>Title</th>
                                <th>Proof Submitted</th>
                                <th>Proof Status</th>
                                <th>Submited At</th>
                                <th>Paused At</th>
                                <th>Paused By</th>
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

        // Paused Data
        $('#pausedDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.posted_task_list.paused') }}",
                type: "GET",
                data: function (d) {
                    d.posted_task_id = $('#filter_posted_task_id').val();
                    d.user_id = $('#filter_user_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'user_id', name: 'user_id' },
                { data: 'title', name: 'title' },
                { data: 'proof_submitted', name: 'proof_submitted' },
                { data: 'proof_status', name: 'proof_status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'paused_at', name: 'paused_at' },
                { data: 'paused_by', name: 'paused_by' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').keyup(function(){
            $('#pausedDataTable').DataTable().ajax.reload();
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.paused.posted_task_view', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                },
            });
        });

        // Resume Data
        $(document).on('click', '.resumeBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.running.posted_task_paused_resume', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to resume this task!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Resume it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(response) {
                            $('#pausedDataTable').DataTable().ajax.reload();
                            toastr.success('Task Resumed Successfully');
                        }
                    });
                }
            })
        })
    });
</script>
@endsection


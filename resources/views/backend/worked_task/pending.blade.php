@extends('layouts.template_master')

@section('title', 'Worked Task List - Pending')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Worked Task List - Pending</h3>
                </div>
                <div class="action-btn">
                    <a href="{{ route('backend.worked_task_list.reviewed') }}" class="btn btn-warning btn-fw">Reviewed List</a>
                    <a href="{{ route('backend.worked_task_list.approved-rejected') }}" class="btn btn-primary btn-fw">Approved & Rejected List</a>
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
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task ID</th>
                                <th>User ID</th>
                                <th>Title</th>
                                <th>Proof Submitted</th>
                                <th>Pending Count</th>
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
                url: "{{ route('backend.worked_task_list.pending') }}",
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
                { data: 'pending_count', name: 'pending_count' },
                { data: 'action', name: 'action' }
            ]
        });

        // Filter Data
        $('.filter_data').keyup(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });
    });
</script>
@endsection

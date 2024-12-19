@extends('layouts.template_master')

@section('title', 'Report - False')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Report List - False</h3>
                </div>
                <div class="action-btn">
                    <a href="{{ route('backend.report.pending') }}" class="btn btn-primary">Pending List</a>
                    <a href="{{ route('backend.report.received') }}" class="btn btn-success">Received List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_report_id" class="form-label">Report Id</label>
                                <input type="number" id="filter_report_id" class="form-control filter_data" placeholder="Search Report Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_reported_user_id" class="form-label">Reported User Id</label>
                                <input type="number" id="filter_reported_user_id" class="form-control filter_data" placeholder="Search Reported User Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="User">User</option>
                                    <option value="Post Task">Post Task</option>
                                    <option value="Proof Task">Proof Task</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_reported_by_user_id" class="form-label">Reported By User Id</label>
                                <input type="number" id="filter_reported_by_user_id" class="form-control filter_data" placeholder="Search Reported By User Id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Report ID</th>
                                <th>Type</th>
                                <th>Reported User ID</th>
                                <th>Reported User Name</th>
                                <th>Report By User ID</th>
                                <th>Report By User Name</th>
                                <th>Submit Date</th>
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

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.report.false') }}",
                data: function (e) {
                    e.report_id = $('#filter_report_id').val();
                    e.user_id = $('#filter_reported_user_id').val();
                    e.type = $('#filter_type').val();
                    e.reported_by = $('#filter_reported_by_user_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'type', name: 'type' },
                { data: 'reported_user_id', name: 'reported_user_id' },
                { data: 'reported_user_name', name: 'reported_user_name' },
                { data: 'report_by_user_id', name: 'report_by_user_id' },
                { data: 'report_by_user_name', name: 'report_by_user_name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
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

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.report.view', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                },
            });
        });
    });
</script>
@endsection

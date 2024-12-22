@extends('layouts.template_master')

@section('title', 'Working Task List - Reviewed')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h3 class="card-title">Working Task List - Reviewed</h3>
                    <h3>Total: <span id="total_proofs_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi worker, below tasks list is waiting for approval from admin panel. If your review is approved you will see it in the Approved folder and if it is rejected then you will see it in the rejected folder. After review checking you will get notification from admin panel. If your review is here more than 48 hours then contact us. Tasks will removed from below list after 7 days so task removal countdown will start automatically. Also please contact us if you face any problem.
                    </p>
                </div>
                <div>
                    <a href="{{ route('worked_task.list.pending') }}" class="btn btn-primary btn-xs m-1">Pending List</a>
                    <a href="{{ route('worked_task.list.approved') }}" class="btn btn-success btn-xs m-1">Approved List</a>
                    <a href="{{ route('worked_task.list.rejected') }}" class="btn btn-danger btn-xs m-1">Rejected List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3 border p-2">
                    <div class="row">
                        <div class="col-xl-3 col-lg-5 mb-3">
                            <div class="form-group">
                                <label class="form-label" for="filter_date">Filter By Date</label>
                                <input type="date" class="form-control filter_data bg-white" id="filter_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task Title</th>
                                <th>Task Rate</th>
                                <th>Submit Date</th>
                                <th>Rejected Reason</th>
                                <th>Rejected Date</th>
                                <th>Reviewed Reason</th>
                                <th>Reviewed Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">Check</h5>
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
                url: "{{ route('worked_task.list.reviewed') }}",
                data: function (d) {
                    d.filter_date = $('#filter_date').val();
                },
                dataSrc: function (json) {
                    // Update total proof count
                    $('#total_proofs_count').text(json.totalProofsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'title', name: 'title' },
                { data: 'income_of_each_worker', name: 'income_of_each_worker' },
                { data: 'created_at', name: 'created_at' },
                { data: 'rejected_reason', name: 'rejected_reason' },
                { data: 'rejected_at', name: 'rejected_at' },
                { data: 'reviewed_reason', name: 'reviewed_reason' },
                { data: 'reviewed_at', name: 'reviewed_at' },
                { data: 'action', name: 'action' }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Check Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('reviewed.worked_task.view', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);

                    // Show Modal
                    $('.viewModal').modal('show');
                },
            });
        });

        // Set Date Range
        var today = new Date();
        var beforeDays = new Date();
        beforeDays.setDate(today.getDate() - 6);
        $('#filter_date').attr('max', today.toISOString().split('T')[0]);
        $('#filter_date').attr('min', beforeDays.toISOString().split('T')[0]);

    });
</script>
@endsection

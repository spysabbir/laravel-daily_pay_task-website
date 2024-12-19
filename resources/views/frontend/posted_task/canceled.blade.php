@extends('layouts.template_master')

@section('title', 'Posting Task List - Canceled')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Posting Task List - Canceled</h3>
                    <h3>Total: <span id="total_tasks_count">0</span></h3>
                    <p class="card-description text-warning">
                        Note: Hi user, below tasks list is your canceled task. These tasks will be automatically remove from here after 7 days then you can't see anymore. If you facing any problems please contact with us.
                    </p>
                </div>
                <div>
                    <a href="{{ route('posted_task.list.pending') }}" class="btn btn-primary btn-xs m-1">Pending List</a>
                    <a href="{{ route('posted_task.list.rejected') }}" class="btn btn-danger btn-xs m-1">Rejected List</a>
                    <a href="{{ route('posted_task.list.running') }}" class="btn btn-info btn-xs m-1">Running List</a>
                    <a href="{{ route('posted_task.list.paused') }}" class="btn btn-secondary btn-xs m-1">Paused List</a>
                    <a href="{{ route('posted_task.list.completed') }}" class="btn btn-success btn-xs m-1">Completed List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task ID</th>
                                <th>Title</th>
                                <th>Submited At</th>
                                <th>Proof Submitted</th>
                                {{-- <th>Proof Status</th> --}}
                                {{-- <th>Total Cost</th> --}}
                                {{-- <th>Cost Status</th> --}}
                                {{-- <th>Cancellation Reason</th> --}}
                                <th>Canceled At</th>
                                <th>Canceled By</th>
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
                url: "{{ route('posted_task.list.canceled') }}",
                dataSrc: function (json) {
                    // Update total task count
                    $('#total_tasks_count').text(json.totalTasksCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'created_at', name: 'created_at' },
                { data: 'proof_submitted', name: 'proof_submitted' },
                // { data: 'proof_status', name: 'proof_status' },
                // { data: 'total_cost', name: 'total_cost' },
                // { data: 'charge_status', name: 'charge_status' },
                // { data: 'cancellation_reason', name: 'cancellation_reason' },
                { data: 'canceled_at', name: 'canceled_at' },
                { data: 'canceled_by', name: 'canceled_by' },
                { data: 'action', name: 'action' },
            ]
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('posted_task.view', ":id") }}";
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
    });
</script>
@endsection

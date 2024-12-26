@extends('layouts.template_master')

@section('title', 'Working Task List - Approved')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h3 class="card-title">Working Task List - Approved</h3>
                    <h3>Total: <span id="total_proofs_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi user, below tasks list is your worked task and waiting for approval from buyer. If your task is approved you will see it in the Approved folder and if it is rejected then you will see it in the rejected folder. If your task is here more than 72 hours then contact us. Tasks will removed from below list after 7 days. Also please contact us if you face any problem.
                    </p>
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
                                <th>You Earn</th>
                                <th>Rating</th>
                                <th>Bonus</th>
                                <th>Submit Date</th>
                                <th>Approved Date</th>
                                <th>Approved By</th>
                                {{-- <th>Action</th> --}}
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
                url: "{{ route('worked_task.list.approved') }}",
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
                { data: 'rating', name: 'rating' },
                { data: 'bonus', name: 'bonus' },
                { data: 'created_at', name: 'created_at' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'approved_by', name: 'approved_by' },
                // { data: 'action', name: 'action' }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Check Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('worked_task.view.approved', ":id") }}";
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
        var beforeSixDays = new Date();
        beforeSixDays.setDate(today.getDate() - 6);
        $('#filter_date').attr('max', today.toISOString().split('T')[0]);
        $('#filter_date').attr('min', beforeSixDays.toISOString().split('T')[0]);

    });
</script>
@endsection

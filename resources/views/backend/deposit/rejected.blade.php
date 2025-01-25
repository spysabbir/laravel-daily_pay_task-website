@extends('layouts.template_master')

@section('title', 'Deposit Request (Rejected)')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Deposit Request (Rejected)</h3>
                <h3>Total: <span id="total_deposits_count">0</span></h3>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="method" class="form-label">Deposit Method</label>
                                <select class="form-select filter_data" id="filter_method">
                                    <option value="">-- Select Deposit Method --</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Rocket">Rocket</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_number" class="form-label">Number</label>
                                <input type="number" id="filter_number" class="form-control filter_data" placeholder="Search Number">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_transaction_id" class="form-label">Transaction Id</label>
                                <input type="text" id="filter_transaction_id" class="form-control filter_data" placeholder="Search Transaction Id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="rejectedDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Method</th>
                                <th>Number</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Submitted Date</th>
                                <th>Rejected By</th>
                                <th>Rejected At</th>
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

        // Rejected Data
        $('#rejectedDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.deposit.request.rejected') }}",
                data: function (e) {
                    e.method = $('#filter_method').val();
                    e.user_id = $('#filter_user_id').val();
                    e.number = $('#filter_number').val();
                    e.transaction_id = $('#filter_transaction_id').val();
                },
                dataSrc: function (json) {
                    // Update total deposit count
                    $('#total_deposits_count').text(json.totalDepositsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number' },
                { data: 'transaction_id', name: 'transaction_id' },
                { data: 'amount', name: 'amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'rejected_by', name: 'rejected_by' },
                { data: 'rejected_at', name: 'rejected_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#rejectedDataTable').DataTable().ajax.reload();
        });
        // Filter Data
        $('.filter_data').keyup(function(){
            $('#rejectedDataTable').DataTable().ajax.reload();
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.deposit.request.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);

                    $('.viewModal').modal('show');
                },
            });
        });
    });
</script>
@endsection


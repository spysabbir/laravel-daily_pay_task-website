@extends('layouts.template_master')

@section('title', 'Balance Transfer')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Balance Transfer</h3>
                <h3>Total Transfer: <span id="totalBalanceTransferAmount">0</span></h3>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_send_method" class="form-label">Send Method</label>
                                <select class="form-select filter_data" id="filter_send_method">
                                    <option value="">-- Select Send Method --</option>
                                    <option value="Deposit Balance">Deposit Balance</option>
                                    <option value="Withdraw Balance">Withdraw Balance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_receive_method" class="form-label">Receive Method</label>
                                <select class="form-select filter_data" id="filter_receive_method">
                                    <option value="">-- Select Receive Method --</option>
                                    <option value="Deposit Balance">Deposit Balance</option>
                                    <option value="Withdraw Balance">Withdraw Balance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="approvedDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Send Method</th>
                                <th>Receive Method</th>
                                <th>Amount</th>
                                <th>Payable Amount</th>
                                <th>Submitted Date</th>
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

        // Approved Data
        $('#approvedDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.balance.transfer') }}",
                data: function (e) {
                    e.user_id = $('#filter_user_id').val();
                    e.send_method = $('#filter_send_method').val();
                    e.receive_method = $('#filter_receive_method').val();
                },
                dataSrc: function (json) {
                    // Update total deposit balance transfer amount
                    $('#totalBalanceTransferAmount').text(json.totalBalanceTransferAmount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'send_method', name: 'send_method' },
                { data: 'receive_method', name: 'receive_method' },
                { data: 'amount', name: 'amount' },
                { data: 'payable_amount', name: 'payable_amount' },
                { data: 'created_at', name: 'created_at' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#approvedDataTable').DataTable().ajax.reload();
        });
        // Filter Data
        $('.filter_data').keyup(function(){
            $('#approvedDataTable').DataTable().ajax.reload();
        });
    });
</script>
@endsection


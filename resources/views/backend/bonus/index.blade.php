@extends('layouts.template_master')

@section('title', 'Bonus')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Bonus</h3>
                <h3>Total Amount: <span id="totalBonusAmount">0</span></h3>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_bonus_by_user_id" class="form-label">Bonus By User Id</label>
                                <input type="number" id="filter_bonus_by_user_id" class="form-control filter_data" placeholder="Search Bonus By User Id">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="Referral Registration Bonus">Referral Registration Bonus</option>
                                    <option value="Referral Withdrawal Bonus">Referral Withdrawal Bonus</option>
                                    <option value="Proof Task Approved Bonus">Proof Task Approved Bonus</option>
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
                                <th>User Name</th>
                                <th>Bonus By User Name</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
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
                url: "{{ route('backend.bonus.history') }}",
                data: function (e) {
                    e.user_id = $('#filter_user_id').val();
                    e.bonus_by = $('#filter_bonus_by_user_id').val();
                    e.type = $('#filter_type').val();
                },
                dataSrc: function (json) {
                    // Update total bonuses amount
                    $('#totalBonusAmount').text(json.totalBonusAmount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_name', name: 'user_name' },
                { data: 'bonus_by_user_name', name: 'bonus_by_user_name' },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'created_at', name: 'created_at' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });
        // Filter Data
        $('.filter_data').keyup(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });
    });
</script>
@endsection

@extends('layouts.template_master')

@section('title', 'Deposit Request (Approved)')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Deposit Request (Approved)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="approvedDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>User Email</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Number</th>
                                <th>Approved By</th>
                                <th>Approved At</th>
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
                url: "{{ route('backend.deposit.request.approved') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'user_email', name: 'user_email' },
                { data: 'amount', name: 'amount' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number' },
                { data: 'approved_by', name: 'approved_by' },
                { data: 'approved_at', name: 'approved_at' },
            ]
        });
    });
</script>
@endsection


@extends('layouts.template_master')

@section('title', 'Work List - Pending')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h3 class="card-title">Work List - Pending</h3>
                    <p class="text-info">
                        <strong>Note:</strong> You can see only the last 5 days of data.
                    </p>
                </div>
                <div>
                    <a href="{{ route('work.list.approved') }}" class="btn btn-success">Approved</a>
                    <a href="{{ route('work.list.rejected') }}" class="btn btn-danger">Rejected</a>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3 border p-2">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Submit Date</label>
                                <input type="date" class="form-control filter_data" id="filter_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Work Title</th>
                                <th>You Earn</th>
                                <th>Submit Date</th>
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
                url: "{{ route('work.list.pending') }}",
                data: function (d) {
                    d.filter_date = $('#filter_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'title', name: 'title' },
                { data: 'worker_charge', name: 'worker_charge' },
                { data: 'created_at', name: 'created_at' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Set Date Range
        var today = new Date();
        var beforeDays = new Date();
        beforeDays.setDate(today.getDate() - 4);
        $('#filter_date').attr('max', today.toISOString().split('T')[0]);
        $('#filter_date').attr('min', beforeDays.toISOString().split('T')[0]);

    });
</script>
@endsection

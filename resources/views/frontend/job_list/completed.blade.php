@extends('layouts.template_master')

@section('title', 'Job List - Completed')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Job List - Pending</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Job ID</th>
                                <th>Title</th>
                                <th>Need Worker</th>
                                <th>Worker Charge</th>
                                <th>Job Running Day</th>
                                <td>Status</td>
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
                url: "{{ route('job.list.completed') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'need_worker', name: 'need_worker' },
                { data: 'worker_charge', name: 'worker_charge' },
                { data: 'running_day', name: 'running_day' },
                { data: 'status', name: 'status' },
            ]
        });
    });
</script>
@endsection

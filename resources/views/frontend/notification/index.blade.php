@extends('layouts.template_master')

@section('title', 'Notification')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Notification List</h3>
                    <p class="mb-0">
                        This is the list of all notification.
                    </p>
                </div>
                <a href="{{ route('notification.read.all') }}" class="text-info m-3">Read all notification</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Time</th>
                                <th>Status</th>
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
                url: "{{ route('notification') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'type', name: 'type' },
                { data: 'title', name: 'title' },
                { data: 'message', name: 'message' },
                { data: 'created_at', name: 'created_at'},
                { data: 'status', name: 'status'},
            ]
        });
    });
</script>
@endsection

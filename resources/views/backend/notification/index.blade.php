@extends('layouts.template_master')

@section('title', 'Notification')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div class="text">
                    <h3 class="card-title">Notification List</h3>
                    <h3>Read: <span id="read_notifications_count">0</span>, Unread: <span id="unread_notifications_count">0</span></h3>
                </div>
                <a href="{{ route('backend.notification.read.all') }}" class="text-info m-3">Read all notification</a>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_status" class="form-label">Status</label>
                                <select class="form-select filter_data" id="filter_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="Unread">Unread</option>
                                    <option value="Read">Read</option>
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
                url: "{{ route('backend.notification') }}",
                type: 'GET',
                data: function (d) {
                    d.status = $('#filter_status').val();
                },
                dataSrc: function (json) {
                    // Update total notification count
                    $('#read_notifications_count').text(json.readNotificationsCount);
                    $('#unread_notifications_count').text(json.unreadNotificationsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'title', name: 'title' },
                { data: 'message', name: 'message' },
                { data: 'created_at', name: 'created_at'},
                { data: 'status', name: 'status'},
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });
    });
</script>
@endsection

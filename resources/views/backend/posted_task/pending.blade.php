@extends('layouts.template_master')

@section('title', 'Posted Task List - Pending')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Posted Task List (Pending)</h3>
                <div class="action-btn">
                    <a href="{{ route('backend.posted_task_list.running') }}" class="btn btn-primary btn-sm">Running List</a>
                    <a href="{{ route('backend.posted_task_list.paused') }}" class="btn btn-info btn-sm">Paused List</a>
                    <a href="{{ route('backend.posted_task_list.canceled') }}" class="btn btn-danger btn-sm">Canceled List</a>
                    <a href="{{ route('backend.posted_task_list.rejected') }}" class="btn btn-warning btn-sm">Rejected List</a>
                    <a href="{{ route('backend.posted_task_list.completed') }}" class="btn btn-success btn-sm">Completed List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_posted_task_id" class="form-label">Posted Task Id</label>
                                <input type="number" id="filter_posted_task_id" class="form-control filter_data" placeholder="Search Posted Task Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="pendingDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>Task Id</th>
                                <th>Title</th>
                                <th>Submited At</th>
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

        // Pending Data
        $('#pendingDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.posted_task_list.pending') }}",
                type: "GET",
                data: function (d) {
                    d.posted_task_id = $('#filter_posted_task_id').val();
                    d.user_id = $('#filter_user_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').keyup(function(){
            $('#pendingDataTable').DataTable().ajax.reload();
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.pending.posted_task_view', ":id") }}";
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

        // Rejected Data
        $(document).on('change', '#task_post_status', function () {
            var status = $(this).val();
            if (status == 'Rejected') {
                $('#rejection_reason_div').removeClass('d-none');
            } else {
                $('#rejection_reason_div').addClass('d-none');
            }
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();

            var id = $('#post_task_id').val();
            var url = "{{ route('backend.posted_task_status_update', ':id') }}";
            url = url.replace(':id', id);

            var form = $(this);
            var formData = new FormData(this);

            var submitButton = form.find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        $('#pendingDataTable').DataTable().ajax.reload();
                        $(".viewModal").modal('hide');
                        toastr.success('Task post update successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        })
    });
</script>
@endsection


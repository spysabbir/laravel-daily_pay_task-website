@extends('layouts.template_master')

@section('title', 'Job Post')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Job Post (Pending)</h3>
                <div class="action-btn">
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target=".rejectedData">
                        Rejected Request
                    </button>
                    <!-- Verification Request (Reject) Modal -->
                    <div class="modal fade rejectedData" tabindex="-1" aria-labelledby="rejectedDataLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectedDataLabel">Verification Reject</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table id="rejectedDataTable" class="table w-100">
                                            <thead>
                                                <tr>
                                                    <td>Sl No</td>
                                                    <th>User Id</th>
                                                    <th>Remarks</th>
                                                    <th>Rejected By</th>
                                                    <th>Rejected At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pendingDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>Created At</th>
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
                url: "{{ route('backend.job_post.request') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.job_post.request.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                },
            });
        });

        // Rejected Data
        $(document).on('change', '#job_post_status', function () {
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
            var id = $('#job_post_id').val();
            var url = "{{ route('backend.job_post.request.update', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "PUT",
                data: $(this).serialize(),
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        if (response.status == 401) {
                            $.each(response.error, function(prefix, val){
                                $('span.update_'+prefix+'_error').text(val[0]);
                            })
                        }else{
                            $('#pendingDataTable').DataTable().ajax.reload();
                            $('#rejectedDataTable').DataTable().ajax.reload();
                            $(".viewModal").modal('hide');
                            toastr.success('Job post update successfully.');
                        }
                    }
                },
            });
        })

        // Rejected Data
      

        // Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.verification.request.delete', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        success: function(response) {
                            $(".rejectedData").modal('hide');
                            $('#rejectedDataTable').DataTable().ajax.reload();
                            toastr.success('Verification request delete successfully.');
                        }
                    });
                }
            })
        })
    });
</script>
@endsection


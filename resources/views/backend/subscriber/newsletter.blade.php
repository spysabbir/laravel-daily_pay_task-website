@extends('layouts.template_master')

@section('title', 'Newsletter List')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Newsletter List</h3>
                <div class="action-btn">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".createModel"><i data-feather="plus-circle"></i></button>
                    <!-- Create Modal -->
                    <div class="modal fade createModel" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModelLabel">Create</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="createForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-6 mb-3">
                                                <label for="mail_type" class="form-label">Mail Type</label>
                                                <select class="form-select" name="mail_type" id="mail_type">
                                                    <option value="">-- Select Mail Type --</option>
                                                    <option value="Subscriber">Subscriber</option>
                                                    <option value="User">User</option>
                                                </select>
                                                <span class="text-danger error-text mail_type_error"></span>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" name="status" id="status">
                                                    <option value="">-- Select Status --</option>
                                                    <option value="Draft">Draft</option>
                                                    <option value="Sent">Sent</option>
                                                </select>
                                                <span class="text-danger error-text status_error"></span>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 mb-3" id="sent_at_div">
                                                <label for="sent_at" class="form-label">Send At</label>
                                                <input type="datetime-local" class="form-control" name="sent_at" id="sent_at">
                                                <span class="text-danger error-text sent_at_error"></span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Subject</label>
                                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject">
                                            <span class="text-danger error-text subject_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content" class="form-label">Content</label>
                                            <textarea class="form-control" name="content" id="content" placeholder="Content" rows="5"></textarea>
                                            <span class="text-danger error-text content_error"></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mail_type">Mail Type</label>
                                <select class="form-select filter_data" id="filter_mail_type">
                                    <option value="">-- Select Mail Type --</option>
                                    <option value="Subscriber">Subscriber</option>
                                    <option value="User">User</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-select filter_data" id="filter_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Sent">Sent</option>
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
                                <th>Mail Type</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Submit Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModelLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModelLabel">View</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="viewData">

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

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.subscriber.newsletter') }}",
                data: function (e) {
                    e.status = $('#filter_status').val();
                    e.mail_type = $('#filter_mail_type').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'mail_type', name: 'mail_type' },
                { data: 'subject', name: 'subject' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Show send at field
        $('#sent_at_div').hide();
        $('#status').change(function(){
            var status = $(this).val();
            if(status == 'Draft'){
                $('#sent_at_div').show();
            }else{
                $('#sent_at_div').hide();
            }
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('backend.subscriber.newsletter.send') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        $('.createModel').modal('hide');
                        $('#createForm')[0].reset();
                        $('#allDataTable').DataTable().ajax.reload();
                        toastr.success('Newsletter Send Successfully');
                    }
                }
            });
        });

        // View Data
        $(document).on('click', '.viewBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.subscriber.newsletter.view', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    $('#viewData').html(response);
                }
            });
        });

        // Force Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.subscriber.newsletter.delete', ":id") }}";
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
                        method: 'GET',
                        success: function(response) {
                            $('#allDataTable').DataTable().ajax.reload();
                            toastr.error('Newsletter Deleted Successfully');
                        }
                    });
                }
            })
        })
    });
</script>
@endsection


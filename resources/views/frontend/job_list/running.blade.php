@extends('layouts.template_master')

@section('title', 'Job List - Running')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Job List - Running</h3>
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
                                <th>Work Compleated</th>
                                <th>Worker Earning</th>
                                <th>Job Running Day</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Edit Modal -->
                            <div class="modal fade editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <form class="forms-sample" id="editForm">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" id="job_post_id">
                                                <div class="mb-3">
                                                    <label for="need_worker" class="form-label">Need Worker</label>
                                                    <input type="number" class="form-control" id="need_worker" value="0" name="need_worker" placeholder="Need Worker">
                                                    <span class="text-danger error-text update_need_worker_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="worker_charge" class="form-label">Worker Charge</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="worker_charge" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="job_charge" class="form-label">Job Charge</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="update_job_charge" value="0" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="site_charge" class="form-label">Site Charge <strong class="text-info">( {{ get_default_settings('job_posting_charge_percentage') }} % )</strong></label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="update_site_charge" value="0" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="total_job_charge" class="form-label">Total Job Charge</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="update_total_job_charge" value="0" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Edit</button>
                                            </div>
                                        </form>
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
                url: "{{ route('job.list.running') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'need_worker', name: 'need_worker' },
                { data: 'worker_charge', name: 'worker_charge' },
                { data: 'running_day', name: 'running_day' },
                { data: 'action', name: 'action' }
            ]
        });

        // Paused Data
        $(document).on('click', '.pausedBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('running_job.paused_resume', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to paused this job!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Paused it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(response) {
                            $('#allDataTable').DataTable().ajax.reload();
                            toastr.warning('Job Paused Successfully');
                        }
                    });
                }
            })
        })

        // Canceled Data
        $(document).on('click', '.canceledBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('running_job.canceled', ":id") }}";
            url = url.replace(':id', id)

            $.ajax({
                url: url,
                method: 'POST',
                data: { id: id, message: result.value },
                success: function(response) {
                    if (response.status == 404) {
                        toastr.error(response.error);
                    }else{
                        Swal.fire({
                            input: "textarea",
                            inputLabel: "Cancellation Reason",
                            inputPlaceholder: "Type cancellation reason here...",
                            inputAttributes: {
                                "aria-label": "Type cancellation reason here..."
                            },
                            title: 'Are you sure?',
                            text: "You want to canceled this job!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Canceled it!',
                            preConfirm: () => {
                                const message = Swal.getInput().value;
                                if (!message) {
                                    Swal.showValidationMessage('Cancellation Reason is required');
                                }
                                return message;
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                $.ajax({
                                    url: url,
                                    method: 'POST',
                                    data: { id: id, message: result.value },
                                    success: function(response) {
                                        if (response.status == 400) {
                                            toastr.error(response.error);
                                        }else{
                                            toastr.success('Job Canceled Successfully');
                                            $('#allDataTable').DataTable().ajax.reload();
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            });
        })

        // Edit Data
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('running_job.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#job_post_id').val(response.id);
                    $('#worker_charge').val(response.worker_charge);
                },
            });
        });

        // Update Data
        $('#editForm').submit(function (event) {
            event.preventDefault();
            var id = $('#job_post_id').val();
            var url = "{{ route('running_job.update', ":id") }}";
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
                            toastr.error(response.error);
                        }else{
                            $('#editForm')[0].reset();
                            $(".editModal").modal('hide');
                            $('#allDataTable').DataTable().ajax.reload();
                            toastr.success('Job Updated Successfully');
                        }
                    }
                },
            });
        });

        // Worker Charge Calculation
        $(document).on('keyup', '#need_worker', function(){
            var need_worker = $(this).val();
            var worker_charge = $('#worker_charge').val();
            var job_charge = need_worker * worker_charge;
            var site_charge = (job_charge * {{ get_default_settings('job_posting_charge_percentage') }}) / 100;
            var total_job_charge = job_charge + site_charge;
            $('#update_job_charge').val(job_charge);
            $('#update_site_charge').val(site_charge);
            $('#update_total_job_charge').val(total_job_charge);
        });
    });
</script>
@endsection

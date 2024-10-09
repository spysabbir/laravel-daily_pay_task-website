@extends('layouts.template_master')

@section('title', 'Posting Task List - Running')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Posting Task List - Running</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task ID</th>
                                <th>Title</th>
                                <th>Approved At</th>
                                <th>Proof Submitted</th>
                                <th>Proof Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Edit Modal -->
                            <div class="modal fade editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Update</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <form class="forms-sample" id="editForm">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" id="post_task_id">
                                                <div class="mb-3">
                                                    <label for="work_needed" class="form-label">Additional Work Needed </label>
                                                    <input type="number" class="form-control" id="work_needed" value="0" name="work_needed" min="1" placeholder="Work Needed">
                                                    <span class="text-danger error-text update_work_needed_error"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="earnings_from_work" class="form-label">Earnings From Work</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="earnings_from_work" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="task_charge" class="form-label">Task Charge</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="update_task_charge" value="0" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="site_charge" class="form-label">Site Charge <strong class="text-info">( {{ get_default_settings('task_posting_charge_percentage') }} % )</strong></label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="update_site_charge" value="0" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="total_task_charge" class="form-label">Total Task Charge</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="update_total_task_charge" value="0" readonly>
                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
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
                url: "{{ route('posting_task.list.running') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'proof_submitted', name: 'proof_submitted' },
                { data: 'proof_status', name: 'proof_status' },
                { data: 'action', name: 'action' }
            ]
        });

        // Paused Data
        $(document).on('click', '.pausedBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('running.posting_task.paused.resume', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to paused this task!",
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
                            toastr.warning('Task Paused Successfully');
                        }
                    });
                }
            })
        })

        // Canceled Data
        $(document).on('click', '.canceledBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('running.posting_task.canceled', ":id") }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                method: 'POST',
                data: { id: id, check: true },
                success: function(response) {
                    if (response.status == 400) {
                        toastr.error(response.error);
                    } else {
                        Swal.fire({
                            input: "textarea",
                            inputLabel: "Cancellation Reason",
                            inputPlaceholder: "Type cancellation reason here...",
                            inputAttributes: {
                                "aria-label": "Type cancellation reason here..."
                            },
                            title: 'Are you sure?',
                            text: "You want to cancel this task!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Cancel it!',
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
                                        if (response.status == 401) {
                                            toastr.error(response.error);
                                        } else {
                                            $('#allDataTable').DataTable().ajax.reload();
                                            $("#deposit_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                                            toastr.error('Task Canceled Successfully');
                                        }
                                    },
                                });
                            }
                        });
                    }
                },
            });
        });

        // Edit Data
        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('running.posting_task.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#post_task_id').val(response.id);
                    $('#earnings_from_work').val(response.earnings_from_work);
                },
            });
        });

        // Update Data
        $('#editForm').submit(function (event) {
            event.preventDefault();
            var id = $('#post_task_id').val();
            var url = "{{ route('running.posting_task.update', ":id") }}";
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
                            $("#deposit_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                            toastr.success('Task Updated Successfully');
                        }
                    }
                },
            });
        });

        // Earnings From Work Calculation keyup and change event
        $(document).on('change keyup', '#work_needed', function(){
            var work_needed = $(this).val();
            var earnings_from_work = $('#earnings_from_work').val();
            var task_charge = work_needed * earnings_from_work;
            var site_charge = (task_charge * {{ get_default_settings('task_posting_charge_percentage') }}) / 100;
            var total_task_charge = task_charge + site_charge;
            $('#update_task_charge').val(task_charge);
            $('#update_site_charge').val(site_charge);
            $('#update_total_task_charge').val(total_task_charge);
        });
    });
</script>
@endsection

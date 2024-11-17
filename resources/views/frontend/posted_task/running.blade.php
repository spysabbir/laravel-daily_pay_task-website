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
                                <th>Boosted Time</th>
                                <th>Proof Submitted</th>
                                <th>Proof Status</th>
                                <th>Total Charge</th>
                                <th>Charge Status</th>
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
                                                    <label for="work_needed" class="form-label">Additional Work Needed <small class="text-danger">* Required </small></label>
                                                    <input type="number" class="form-control" id="work_needed" value="0" name="work_needed" min="1" placeholder="Work Needed">
                                                    <span class="text-danger error-text update_work_needed_error"></span>
                                                </div>
                                                {{-- <div class="mb-3">
                                                    <label for="boosted_time" class="form-label">Boosted Time <small class="text-danger">* Required </small></label>
                                                    <select class="form-select" name="boosted_time" id="boosted_time" required>
                                                        <option value="0" selected>No Boost</option>
                                                        <option value="15">15 Minutes</option>
                                                        <option value="30">30 Minutes</option>
                                                        <option value="45">45 Minutes</option>
                                                        <option value="60">1 Hour</option>
                                                        <option value="120">2 Hours</option>
                                                        <option value="180">3 Hours</option>
                                                        <option value="240">4 Hours</option>
                                                        <option value="300">5 Hours</option>
                                                        <option value="360">6 Hours</option>
                                                    </select>
                                                    <span class="text-danger error-text update_boosted_time_error"></span>
                                                    <small class="text-info">* Every 15 minutes boost charges {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosted_time_charge') }}.</small>
                                                    <br>
                                                    <small class="text-info">* When the task is boosted, it will be shown at the top of the task list.</small>
                                                </div> --}}
                                                <div class="mb-3">
                                                    <input type="hidden" id="old_work_duration">
                                                    <label for="work_duration" class="form-label"> Work Duration <small class="text-danger">* Required </small></label>
                                                    <select class="form-select" name="work_duration" id="work_duration">
                                                        <option value="3">3 Days</option>
                                                        <option value="4">4 Days</option>
                                                        <option value="5">5 Days</option>
                                                        <option value="6">6 Days</option>
                                                        <option value="7">1 Week</option>
                                                    </select>
                                                    <span class="text-danger error-text update_work_duration_error"></span>
                                                    <small class="text-info">* Additional work duration charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_work_duration_charge') }} per day. Note: You get 3 days for free.</small>
                                                    <br>
                                                    <small class="text-info">* When work duration is over the task will be canceled automatically.</small>
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
                                                <button type="submit" id="updateBtn" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('posted_task.list.running') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'boosted_time', name: 'boosted_time' },
                { data: 'proof_submitted', name: 'proof_submitted' },
                { data: 'proof_status', name: 'proof_status' },
                { data: 'total_charge', name: 'total_charge' },
                { data: 'charge_status', name: 'charge_status' },
                { data: 'action', name: 'action' }
            ],
            drawCallback: function(settings) {
                updateCountdowns();
            }
        });

        // Function to update countdowns
        function updateCountdowns() {
            $(".countdown").each(function() {
                const $element = $(this);
                const endTime = new Date($element.data('end-time')).getTime();

                if (isNaN(endTime)) {
                    console.error("Error: Invalid end time format.");
                    $element.text("Invalid end time.");
                    return;
                }

                function updateCountdown() {
                    const now = new Date().getTime();
                    const remainingTime = endTime - now;

                    if (remainingTime <= 0) {
                        $element.text("Boosted time has expired.");
                    } else {
                        const hours = Math.floor(remainingTime / (1000 * 60 * 60));
                        const minutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

                        $element.text(`${hours}h ${minutes}m ${seconds}s remaining`);
                    }
                }
                setInterval(updateCountdown, 1000);
                updateCountdown();
            });
        }

        // Paused Data
        $(document).on('click', '.pausedBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('running.posted_task.paused.resume', ":id") }}";
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
            var url = "{{ route('running.posted_task.canceled', ":id") }}";
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
            var url = "{{ route('running.posted_task.edit', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#post_task_id').val(response.id);
                    $('#work_duration').val(response.work_duration);
                    $('#old_work_duration').val(response.work_duration);
                    $('#earnings_from_work').val(response.earnings_from_work);

                    // Disable lower options based on old_work_duration
                    disableLowerOptions(response.work_duration);
                },
            });
        });

        // Disable lower options based on oldWorkDuration
        function disableLowerOptions(oldWorkDuration) {
            // Enable all options first
            $('#work_duration option').prop('disabled', false);

            // Disable options with value less than oldWorkDuration
            $('#work_duration option').each(function () {
                if (parseInt($(this).val()) < oldWorkDuration) {
                    $(this).prop('disabled', true);
                }
            });
        }

        // Update Data
        $('#editForm').submit(function (event) {
            event.preventDefault();

            var submitButton = $('#updateBtn');
            submitButton.prop("disabled", true).text("Updating...");

            var id = $('#post_task_id').val();
            var url = "{{ route('running.posted_task.update', ":id") }}";
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
                complete: function() {
                    submitButton.prop("disabled", false).text("Update");
                }
            });
        });

        // Earnings From Work Calculation keyup and change event
        $(document).on('change keyup', '#work_needed, #work_duration', function () {
            var work_needed = parseInt($('#work_needed').val()) || 0;

            var old_work_duration = parseInt($('#old_work_duration').val()) || 0;
            var work_duration = parseInt($('#work_duration').val()) || 0;
            var work_duration_charge = {{ get_default_settings('task_posting_additional_work_duration_charge') }};

            var earnings_from_work = parseFloat($('#earnings_from_work').val()) || 0;

            var total_work_duration_charge = work_duration_charge * (work_duration - old_work_duration);

            var task_charge = (work_needed * earnings_from_work) + total_work_duration_charge;

            var site_charge = (task_charge * {{ get_default_settings('task_posting_charge_percentage') }}) / 100;
            var total_task_charge = task_charge + site_charge;
            $('#update_task_charge').val(task_charge.toFixed(2));
            $('#update_site_charge').val(site_charge.toFixed(2));
            $('#update_total_task_charge').val(total_task_charge.toFixed(2));
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('posted_task.view', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                },
            });
        });
    });
</script>
@endsection

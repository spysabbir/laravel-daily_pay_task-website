@extends('layouts.template_master')

@section('title', 'Job List - Running')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Job Details</h3>
                </div>
                <div class="action">
                    <a href="{{ route('job.list.running') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
            </div>
            <div class="card-body">
                <h4 class="border p-1 m-1"><strong class="text-info">Title:</strong> {{ $jobPost->title }}</h4>
                <p class="border p-1 m-1"><strong class="text-info">Description:</strong> {{ $jobPost->description }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Required Proof:</strong> {{ $jobPost->required_proof }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Additional Note:</strong> {{ $jobPost->additional_note }}</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Job List - Running</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <button type="button" class="btn btn-sm btn-success" id="approvedAll">Approved All</button>
                            <button type="button" class="btn btn-sm btn-info" id="selectedItemApproved">Selected Item Approved</button>
                            <button type="button" class="btn btn-sm btn-warning" id="selectedItemRejected">Selected Item Rejected</button>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select class="form-select filter_data" id="filter_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Reviewed">Reviewed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="checkAll">
                                </th>
                                <th>User</th>
                                <th>Proof Answer</th>
                                <th>Status</th>
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
                                            <h5 class="modal-title" id="viewModalLabel">Check</h5>
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
            // serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('running_job.show', encrypt($jobPost->id)) }}",
                type: 'GET',
                data: function(d) {
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox' },
                { data: 'user', name: 'user' },
                { data: 'proof_answer', name: 'proof_answer' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Check All
        $('#checkAll').change(function(){
            if(this.checked){
                $('#allDataTable').DataTable().rows().nodes().to$().find('.checkbox').each(function(){
                    if($(this).closest('tr').find('td:eq(3)').text() == 'Pending'){
                        this.checked = true;
                    }
                });
            }else{
                $('#allDataTable').DataTable().rows().nodes().to$().find('.checkbox').each(function(){
                    this.checked = false;
                });
            }
        });

        // Approved All
        $(document).on('click', '#approvedAll', function() {
            var table = $('#allDataTable').DataTable();
            var allData = table.rows().data();
            var approved = true;

            for (var i = 0; i < allData.length; i++) {
                var rowData = allData[i];
                if (rowData.status !== '<span class="badge bg-success">Approved</span>') {
                    approved = false;
                    break;
                }
            }

            if($('#allDataTable').DataTable().rows().data().length == 0){
                toastr.error('No data available');
                return false;
            }else if (approved) {
                toastr.warning('All data already approved');
                return false;
            }else{
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to approved all pending item!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approved it!'
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('running_job.approved_all', $jobPost->id) }}",
                            method: 'GET',
                            success: function(response) {
                                toastr.success('Approved All Successfully');
                                $('#allDataTable').DataTable().ajax.reload();
                            }
                        });
                    }
                })
            }
        });

        // Selected Item Approved
        $(document).on('click', '#selectedItemApproved', function(){
            var id = [];
            $('.checkbox:checked').each(function(){
                id.push($(this).val());
            });

            if(id.length > 0){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to approved selected item!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approved it!'
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('running_job.selected_item_approved') }}",
                            method: 'POST',
                            data: {id:id},
                            success: function(response) {
                                toastr.success('Selected Item Approved Successfully');
                                $('#allDataTable').DataTable().ajax.reload();
                            }
                        });
                    }
                })
            }else{
                toastr.error('Please select at least one checkbox');
            }
        });

        // Selected Item Rejected
        $(document).on('click', '#selectedItemRejected', function() {
            var id = [];
            $('.checkbox:checked').each(function() {
                id.push($(this).val());
            });

            if(id.length > 0) {
                Swal.fire({
                    input: "textarea",
                    inputLabel: "Rejected Reason",
                    inputPlaceholder: "Type rejected reason here...",
                    inputAttributes: {
                        "aria-label": "Type rejected reason here..."
                    },
                    title: 'Are you sure?',
                    text: "You want to rejected selected item!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, rejected it!',
                    preConfirm: () => {
                        const message = Swal.getInput().value;
                        if (!message) {
                            Swal.showValidationMessage('Rejected reason is required');
                        }
                        return message;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $.ajax({
                            url: "{{ route('running_job.selected_item_rejected') }}",
                            method: 'POST',
                            data: { id: id, message: result.value },
                            success: function(response) {
                                toastr.success('Selected Item Rejected Successfully');
                                $('#allDataTable').DataTable().ajax.reload();
                            }
                        });
                    }
                });
            } else {
                toastr.error('Please select at least one checkbox');
            }
        });

        // Check Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('running_job.proof.check', ":id") }}";
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

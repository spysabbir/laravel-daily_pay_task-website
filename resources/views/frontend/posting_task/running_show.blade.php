@extends('layouts.template_master')

@section('title', 'Proof Task List')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Post Task Details - ID: {{ $postTask->id }}</h3>
                </div>
                <div class="action">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-warning">Back</a>
                </div>
            </div>
            <div class="card-body">
                <p class="border p-1 m-1">
                    <strong class="text-info">User Id:</strong> {{ $postTask->user->id }},
                    <strong class="text-info">User Name:</strong> {{ $postTask->user->name }},
                    <strong class="text-info">User Email:</strong> {{ $postTask->user->email }}
                </p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Category:</strong> {{ $postTask->category->name }},
                    <strong class="text-info">Sub Category:</strong> {{ $postTask->subCategory->name }},
                    <strong class="text-info">Child Category:</strong> {{ $postTask->childCategory->name ?? 'N/A' }}
                </p>
                <p class="border p-1 m-1"><strong class="text-info">Title:</strong> {{ $postTask->title }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Description:</strong> {{ $postTask->description }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Required Proof:</strong> {{ $postTask->required_proof }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Additional Note:</strong> {{ $postTask->additional_note }}</p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Warnings From Work:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->earnings_from_work }},
                    <strong class="text-info">Screenshots:</strong> Free: 1 & Extra: {{ $postTask->extra_screenshots }} = Total: {{ $postTask->extra_screenshots + 1 }},
                    <strong class="text-info">Boosted Time:</strong> {{ $postTask->boosted_time ? $postTask->boosted_time . ' Minutes' : 0 }} ,
                    <strong class="text-info">Running Day:</strong> {{ $postTask->running_day }} Days
                </p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Charge:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }},
                    <strong class="text-info">Site Charge:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->site_charge }},
                    <strong class="text-info">Total Charge:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->total_charge }}
                </p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Submited At:</strong> {{ $postTask->created_at->format('d F, Y h:i:s A') }},
                    <strong class="text-info">Approved At:</strong> {{ date('d F, Y h:i:s A', strtotime($postTask->approved_at)) }}
                </p>
                <div class="my-3">
                    @php
                        $proofSubmittedCount = $proofSubmitted->count();
                        $proofStyleWidth = $proofSubmittedCount != 0 ? round(($proofSubmittedCount / $postTask->work_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmittedCount == 0 ? 'primary' : 'success';
                    @endphp
                    <p class="mb-1"><strong class="text-info">Proof Status: </strong> <span class="text-success">Submit: {{ $proofSubmittedCount }}</span>, Need: {{ $postTask->work_needed }}</p>
                    <div class="progress">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $proofStyleWidth }}%" aria-valuenow="{{ $proofSubmittedCount }}" aria-valuemin="0" aria-valuemax="{{ $postTask->work_needed }}">{{ $proofSubmittedCount }} / {{ $postTask->work_needed }}</div>
                    </div>
                </div>
                <div class="my-3">
                    @php
                        $pendingProofCount = $proofSubmitted->where('status', 'Pending')->count();
                        $approvedProofCount = $proofSubmitted->where('status', 'Approved')->count();
                        $rejectedProofCount = $proofSubmitted->where('status', 'Rejected')->count();
                        $reviewedProofCount = $proofSubmitted->where('status', 'Reviewed')->count();

                        $totalProof = $approvedProofCount + $rejectedProofCount + $reviewedProofCount + $pendingProofCount;

                        $pendingProofStyleWidth = $totalProof != 0 ? round(($pendingProofCount / $totalProof) * 100, 2) : 100;
                        $approvedProofStyleWidth = $totalProof != 0 ? round(($approvedProofCount / $totalProof) * 100, 2) : 100;
                        $rejectedProofStyleWidth = $totalProof != 0 ? round(($rejectedProofCount / $totalProof) * 100, 2) : 100;
                        $reviewedProofStyleWidth = $totalProof != 0 ? round(($reviewedProofCount / $totalProof) * 100, 2) : 100;
                    @endphp
                    <p class="mb-1"><strong class="text-info">Check Status: </strong> <span class="text-primary">Pending: {{ $pendingProofCount }}</span>, <span class="text-success">Approved: {{ $approvedProofCount }}</span>, <span class="text-danger">Rejected: {{ $rejectedProofCount }}</span>, <span class="text-warning">Reviewed: {{ $reviewedProofCount }}</span></p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $pendingProofStyleWidth }}%" aria-valuenow="{{ $pendingProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $pendingProofCount }} / {{ $totalProof }}</div>
                        <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: {{ $approvedProofStyleWidth }}%" aria-valuenow="{{ $approvedProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $approvedProofCount }} / {{ $totalProof }}</div>
                        <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" style="width: {{ $rejectedProofStyleWidth }}%" aria-valuenow="{{ $rejectedProofStyleWidth }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $rejectedProofCount }} / {{ $totalProof }}</div>
                        <div class="progress-bar bg-warning progress-bar-striped" role="progressbar" style="width: {{ $reviewedProofStyleWidth }}%" aria-valuenow="{{ $reviewedProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $reviewedProofCount }} / {{ $totalProof }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Proof Task List</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <button type="button" class="btn btn-sm btn-success" id="approvedAll">All Pending Item Approved</button>
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
                                <th>User Details</th>
                                <th>Proof Answer</th>
                                <th>Status</th>
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
                url: "{{ route('running.posting_task.show', encrypt($postTask->id)) }}",
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
                            url: "{{ route('running.posting_task.approved.all', $postTask->id) }}",
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
                            url: "{{ route('running.posting_task.selected.item.approved') }}",
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
                            url: "{{ route('running.posting_task.selected.item.rejected') }}",
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
            var url = "{{ route('running.posting_task.proof.check', ":id") }}";
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

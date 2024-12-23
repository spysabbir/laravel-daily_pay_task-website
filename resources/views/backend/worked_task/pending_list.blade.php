@extends('layouts.template_master')

@section('title', 'Task List - Pending')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Task Details - ID: {{ $postTask->id }}</h3>
                </div>
                <div class="action">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-warning">Back</a>
                </div>
            </div>
            <div class="card-body">
                <p class="border p-1 m-1">
                    <strong class="text-info">User Id: </strong>{{ $postTask->user->id }},
                    <strong class="text-info">User Name: </strong>{{ $postTask->user->name }},
                    <strong class="text-info">User Email: </strong>{{ $postTask->user->email }}
                </p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Category: </strong>{{ $postTask->category->name }},
                    <strong class="text-info">Sub Category: </strong>{{ $postTask->subCategory->name }},
                    <strong class="text-info">Child Category: </strong>{{ $postTask->childCategory->name ?? 'N/A' }}
                </p>
                <p class="border p-1 m-1"><strong class="text-info">Title: </strong>{{ $postTask->title }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Description: </strong>{{ $postTask->description }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Required Proof Answer: </strong>{{ $postTask->required_proof_answer }}</p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Required Proof Photo: </strong>
                    Total: {{ $postTask->required_proof_photo }} Required Proof Photo{{ $postTask->required_proof_photo > 1 ? 's' : '' }}
                </p>
                <p class="border p-1 m-1"><strong class="text-info">Additional Note: </strong>{{ $postTask->additional_note }}</p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Submited At: </strong>{{ $postTask->created_at->format('d F, Y h:i:s A') }},
                    <strong class="text-info">Approved At: </strong>{{ date('d F, Y h:i:s A', strtotime($postTask->approved_at)) }}
                </p>
                {{-- <div class="my-3">
                    @php
                        $proofSubmittedCount = $proofSubmitted->count();
                        $proofStyleWidth = $proofSubmittedCount != 0 ? round(($proofSubmittedCount / $postTask->worker_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmittedCount == 0 ? 'primary' : 'success';
                    @endphp
                    <p class="mb-1"><strong class="text-info">Proof Status: </strong> <span class="text-success">Submit: {{ $proofSubmittedCount }}</span>, Need: {{ $postTask->worker_needed }}</p>
                    <div class="progress position-relative">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $proofStyleWidth }}%" aria-valuenow="{{ $proofSubmittedCount }}" aria-valuemin="0" aria-valuemax="{{ $postTask->worker_needed }}"></div>
                        <span class="position-absolute w-100 text-center">{{ $proofSubmittedCount }} / {{ $postTask->worker_needed }}</span>
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
                </div> --}}
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
                    <h3>Total Pending: <span id="pending_proof_tasks_count">0</span></h3>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_proof_id" class="form-label">Proof Id</label>
                                <input type="number" id="filter_proof_id" class="form-control filter_data" placeholder="Search Proof Id">
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
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Proof Id</th>
                                <th>User Details</th>
                                <th>
                                    <!-- Header Button for Expand/Collapse All -->
                                    <i id="toggleAllRows" class="fas fa-plus-circle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                                    Proof Answer
                                </th>
                                <th>Submited Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal viewSingleTaskProofModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
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
        const table = $('#allDataTable').DataTable({
            processing: true,
            // serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.pending.worked_task_view', encrypt($postTask->id)) }}",
                type: "GET",
                data: function (d) {
                    d.proof_id = $('#filter_proof_id').val();
                    d.user_id = $('#filter_user_id').val();
                },
                dataSrc: function (json) {
                    // Update total count
                    $('#pending_proof_tasks_count').text(json.pendingProofTasksCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'user', name: 'user' },
                {
                    data: 'proof_answer',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <i class="fas fa-plus-circle row-toggle text-primary" style="cursor: pointer; margin-right: 5px;"></i>
                            <span>${data}</span>
                        `;
                    }
                },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
            ]
        });

        // Add click event for the header button to expand/collapse all rows
        let allRowsOpen = false;

        // Function to check if all rows are expanded
        function updateGlobalIcon() {
            const rows = table.rows();
            const totalRows = rows.count();
            const openRows = rows.nodes().filter(row => $(row).hasClass('shown')).length;

            if (openRows === totalRows) {
                $('#toggleAllRows').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                allRowsOpen = true;
            } else {
                $('#toggleAllRows').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                allRowsOpen = false;
            }
        }

        // Individual row expand/collapse
        $('#allDataTable tbody').on('click', '.row-toggle', function () {
            const tr = $(this).closest('tr');
            const row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                $(this).removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Fetch proof_answer or any extra data
                const proofAnswer = row.data().proof_answer_full;
                row.child(`<div class="nested-row">${proofAnswer}</div>`).show();
                tr.addClass('shown');
                $(this).removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }

            // Update the global expand/collapse button icon
            updateGlobalIcon();
        });

        // Global expand/collapse functionality
        $('#toggleAllRows').on('click', function () {
            const icon = $(this);
            const rows = table.rows();

            if (allRowsOpen) {
                // Collapse all rows
                rows.every(function () {
                    if (this.child.isShown()) {
                        this.child.hide();
                        $(this.node()).removeClass('shown');
                        $(this.node()).find('.row-toggle').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                    }
                });
                allRowsOpen = false;
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Expand all rows
                rows.every(function () {
                    const proofAnswer = this.data().proof_answer_full;
                    if (!this.child.isShown()) {
                        this.child(`<div class="nested-row">${proofAnswer}</div>`).show();
                        $(this.node()).addClass('shown');
                        $(this.node()).find('.row-toggle').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                    }
                });
                allRowsOpen = true;
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }
        });

        // Filter Data
        $('.filter_data').keyup(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Check Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.worked_task_check', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);

                    // Destroy the existing LightGallery instance if it exists
                    var lightGalleryInstance = $('#backend-single-lightgallery').data('lightGallery');
                    if (lightGalleryInstance) {
                        lightGalleryInstance.destroy(true); // Pass `true` to completely remove DOM bindings
                    }

                    // Reinitialize LightGallery
                    $('#backend-single-lightgallery').lightGallery({
                        share: false,
                        showThumbByDefault: false,
                        hash: false,
                        mousewheel: false,
                    });

                    $('.viewModal').modal('show');
                },
            });
        });
        $(document).on('onCloseAfter.lg', '#single-lightgallery', function () {
            // Remove hash fragment from the URL
            const url = window.location.href.split('#')[0];
            window.history.replaceState(null, null, url);
        });
    });
</script>
@endsection

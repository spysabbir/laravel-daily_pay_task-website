@extends('layouts.template_master')

@section('title', 'Task List - All')

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
                <p class="border p-1 m-1"><strong class="text-info">Required Proof Answer:</strong> {{ $postTask->required_proof_answer }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Additional Note:</strong> {{ $postTask->additional_note }}</p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Warnings From Work:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->earnings_from_work }},
                    <strong class="text-info">Required Proof Photo:</strong> Free: {{ $postTask->required_proof_photo >= 1 ? 1 : 0 }} & Additional: {{ $postTask->required_proof_photo >= 1 ? $postTask->required_proof_photo - 1 : 0 }} = Total: {{ $postTask->required_proof_photo }},
                    <strong class="text-info">Boosted Time:</strong> {{ $postTask->boosted_time ? $postTask->boosted_time . ' Minutes' : 0 }} ,
                    <strong class="text-info">Work Duration:</strong> Free: 3 Days & Additional: {{ $postTask->work_duration - 3 }} Days = Total {{ $postTask->work_duration }} Days
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
                    <p class="mb-1">
                        <strong>Proof Status: </strong>
                        <span class="text-success">Submit: {{ $proofSubmittedCount }}</span>,
                        <span class="text-primary">Need: {{ $postTask->work_needed }}</span>,
                    </p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $proofStyleWidth }}%" aria-valuenow="{{ $proofSubmittedCount }}" aria-valuemin="0" aria-valuemax="{{ $postTask->work_needed }}">{{ $proofSubmittedCount }} / {{ $postTask->work_needed }}</div>
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
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Details</th>
                                <th>Proof Answer</th>
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
                url: "{{ route('backend.all.worked_task_view', encrypt($postTask->id)) }}",
                type: 'GET',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user', name: 'user' },
                { data: 'proof_answer', name: 'proof_answer' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
            ]
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
                },
            });
        });
    });
</script>
@endsection

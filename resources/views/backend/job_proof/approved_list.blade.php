@extends('layouts.template_master')

@section('title', 'Job Proof List - Approved')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Job Details</h3>
                </div>
                <div class="action">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-info">Back</a>
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
                    <h3 class="card-title">Job Proof List - Approved</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Proof Answer</th>
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
                url: "{{ route('backend.job_proof.approved.list', encrypt($jobPost->id)) }}",
                type: 'GET',
            },
            columns: [
                { data: 'user', name: 'user' },
                { data: 'proof_answer', name: 'proof_answer' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
            ]
        });

        // Check Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.job_proof.check', ":id") }}";
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

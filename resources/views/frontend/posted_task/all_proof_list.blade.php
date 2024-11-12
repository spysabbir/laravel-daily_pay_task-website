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
                    <strong class="text-info">Proof Photo:</strong> Free: 1 & Additional: {{ $postTask->required_proof_photo - 1 }} = Total: {{ $postTask->required_proof_photo }},
                    <strong class="text-info">Boosted Time:</strong> {{ $postTask->boosted_time ? $postTask->boosted_time . ' Minutes' : 0 }} ,
                    <strong class="text-info">Work Duration:</strong> Free: 3 Days & Additional: {{ $postTask->work_duration - 3 }} Days = Total {{ $postTask->work_duration }} Days
                </p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Charge:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }},
                    <strong class="text-info">Site Charge:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->site_charge }},
                    <strong class="text-info">Total Charge:</strong> {{ get_site_settings('site_currency_symbol') }} {{ $postTask->total_charge }}
                </p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Charge Status</strong>,
                    <span class="text-secondary">Waiting: {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->total_charge / $postTask->work_needed) * ($postTask->work_needed - $proofSubmitted->count()) }}</span>,
                    <span class="text-primary">Pending: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->work_needed) * $pendingProof, 2) }}</span>,
                    <span class="text-success">Payment: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->work_needed) * $approvedProof, 2) }}</span>,
                    <span class="text-danger">Refund: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->work_needed) * $finallyRejectedProof, 2) }}</span>,
                    <span class="text-warning">Hold: {{ get_site_settings('site_currency_symbol') }} {{ round(($postTask->total_charge / $postTask->work_needed) *  $waitingRejectedProof, 2) }}</span>
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
                    <div class="progress position-relative">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $proofStyleWidth }}%" aria-valuenow="{{ $proofSubmittedCount }}" aria-valuemin="0" aria-valuemax="{{ $postTask->work_needed }}"></div>
                        <span class="position-absolute w-100 text-center">{{ $proofSubmittedCount }} / {{ $postTask->work_needed }}</span>
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
                        <div class="mb-3">
                            <strong class="text-danger">Warning: if you Reject the proof, the worker can request for review within 72 hours. The admin will check the proof and if it is correct then the worker will be paid or if the proof is wrong then the worker will not be paid. Because of this, only Rejected money will be on hold for 72 hours because you Proof Rejected. If the worker does not request for review within 72 hours, your money will be automatically add your deposit balance. If the worker request for review within 72 hours, the admin will review the proof within 72 hours. Both you and the worker will receive a notification with the results. Please be careful and work well with integrity and don't intentionally reject someone's work.</strong>
                        </div>
                        <div class="col-md-7">
                            <button type="button" class="btn btn-sm btn-success" id="approvedAll">All Pending Item Approved</button>
                            <button type="button" class="btn btn-sm btn-info" id="selectedItemApproved">Selected Item Approved</button>
                            <button type="button" class="btn btn-sm btn-warning" id="selectedItemRejected">Selected Item Rejected</button>
                            <button type="button" data-id="" class="btn btn-primary btn-xs checkAllPendingTaskProofBtn" data-bs-toggle="modal" data-bs-target=".checkAllPendingTaskProofModal">Check All Pending Task Proof</button>
                            <!-- View Modal -->
                            <div class="modal fade checkAllPendingTaskProofModal" tabindex="-1" aria-labelledby="checkAllPendingTaskProofModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="checkAllPendingTaskProofModalLabel">Check All Pending Task Proof</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="checkAllPendingTaskProofModalBodyDiv">
                                            <div class="row">
                                                <div class="col-lg-8" id="checkAllPendingTaskProofData">
                                                    <!-- Check All Pending Task Proof Content -->
                                                </div>
                                                <div class="col-lg-4 d-none" id="checkAllPendingTaskProofAction">
                                                    <div>
                                                        <h4>Update Proof Task Status:</h4>
                                                        <form class="forms-sample border mt-2 p-2" id="checkAllPendingTaskProofEditForm"  enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" id="set_proof_task_id" value="">
                                                            <div class="mb-3">
                                                                <label for="status" class="form-label">Status <span class="text-danger">* Required</span></label>
                                                                <div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input type="radio" class="form-check-input" name="status" id="approve" value="Approved">
                                                                        <label class="form-check-label" for="approve">
                                                                            Approved
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input type="radio" class="form-check-input" name="status" id="reject" value="Rejected">
                                                                        <label class="form-check-label" for="reject">
                                                                            Rejected
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger error-text update_status_error"></span>
                                                            </div>
                                                            <div id="approved_div">
                                                                <div class="mb-3">
                                                                    <label for="rating" class="form-label">Rating (1-5) <span class="text-info">* Optonal</span></label>
                                                                    <div class="rating-box">
                                                                        <div class="stars">
                                                                            <i class="fa-solid fa-star"></i>
                                                                            <i class="fa-solid fa-star"></i>
                                                                            <i class="fa-solid fa-star"></i>
                                                                            <i class="fa-solid fa-star"></i>
                                                                            <i class="fa-solid fa-star"></i>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="rating" id="rating" min="0" max="5">
                                                                    <span class="text-danger error-text update_rating_error"></span>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="bonus" class="form-label">Bonus <span class="text-info">* Optonal</span></label>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control" id="bonus" name="bonus" min="0" max="{{ get_default_settings('task_proof_max_bonus_amount') }}" placeholder="Bonus">
                                                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                                                    </div>
                                                                    <small class="text-info">The bonus field must not be greater than {{ get_default_settings('task_proof_max_bonus_amount') }} {{ get_site_settings('site_currency_symbol') }}.</small>
                                                                    <span class="text-danger error-text update_bonus_error"></span>
                                                                </div>
                                                            </div>
                                                            <div id="rejected_div">
                                                                <div class="mb-3">
                                                                    <label for="rejected_reason" class="form-label">Rejected Reason <span class="text-danger">* Required</span></label>
                                                                    <textarea class="form-control" id="rejected_reason" name="rejected_reason" rows="3" placeholder="Rejected Reason"></textarea>
                                                                    <span class="text-danger error-text update_rejected_reason_error"></span>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="rejected_reason_photo" class="form-label">Rejected Reason Photo <span class="text-info">* Optonal</span></label>
                                                                    <input type="file" class="form-control" id="rejected_reason_photo" name="rejected_reason_photo" accept=".jpg, .jpeg, .png">
                                                                    <small class="text-info d-block">The rejected reason photo must be jpg, jpeg or png format and less than 2MB.</small>
                                                                    <span class="text-danger error-text update_rejected_reason_photo_error"></span>
                                                                    <img id="rejected_reason_photoPreview" class="mt-2 d-block" style="max-height: 200px; max-width: 200px; display: none;">
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Submit</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-danger btn-block" id="clear_filters">Clear Filters</button>
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
                                <th>Checked At</th>
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

                            <!-- Report Proof Task Modal -->
                            <div class="modal fade reportProofTaskModal" tabindex="-1" aria-labelledby="reportProofTaskModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reportProofTaskModalLabel">Report Proof Task</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div id="reportSendProofTaskForm" style="display: none;">
                                            <form class="forms-sample" id="reportProofTaskForm" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" id="user_id">
                                                <input type="hidden" name="post_task_id" id="post_task_id">
                                                <input type="hidden" name="proof_task_id" id="report_proof_task_id">
                                                <input type="hidden" name="type" value="Proof Task">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="reason" name="reason" placeholder="Reason"></textarea>
                                                        <span class="text-danger error-text reason_error"></span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="photo" class="form-label">Photo <span class="text-info">* Optonal</span></label>
                                                        <input type="file" class="form-control" id="photo" name="photo" accept=".jpg, .jpeg, .png">
                                                        <span class="text-danger error-text photo_error d-block"></span>
                                                        <img src="" alt="Photo" id="photoPreview" class="mt-2" style="display: none; width: 100px; height: 100px;">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Report</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div id="reportSendProofTaskExitsDiv" style="display: none;">
                                            <div class="modal-body ">
                                                <div class="alert alert-info" role="alert" >
                                                    <strong>Warning!</strong> You have already reported this proof task.
                                                    <p id="pendingMessage" class="mt-2 text-warning"></p>
                                                    <p id='resolvedMessage' class="mt-2 text-success"></p>
                                                    <hr>
                                                    <strong>Reason:</strong> <span id="reportReason"></span>
                                                    <br>
                                                    <strong>Date:</strong> <span id="reportDate"></span>
                                                    <br>
                                                    <img src="" id="reportPhoto" class="img-fluid mt-2" alt="Report Photo">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
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

        // Get Check All Pending Task Proof
        function getCheckAllPendingTaskProof() {
            $.ajax({
                url: "{{ route('proof_task.all.pending.check', $postTask->id) }}",
                type: "GET",
                success: function(response) {
                    if (response.proofTaskListPending.length === 0) {
                        $('#checkAllPendingTaskProofModalBodyDiv').html(`
                            <div class="alert alert-info" role="alert">
                                No pending task proof found.
                            </div>
                        `);
                    } else {
                        $('#checkAllPendingTaskProofAction').removeClass('d-none');

                        let indicators = '';
                        let items = '';

                        response.proofTaskListPending.forEach((proofTask, index) => {
                            // Carousel Indicators
                            indicators += `
                                <li data-bs-target="#proofTaskListPendingCarousel" data-bs-slide-to="${index}" class="${index === 0 ? 'active' : ''}"></li>
                            `;

                            // Carousel Items
                            items += `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}" data-proof-id="${proofTask.id}">
                                    <div class="mb-3">
                                        <h4>User Information:</h4>
                                        <div class="mb-2 border p-2">
                                            <strong>User Id:</strong> ${proofTask.user_id},
                                            <strong>User Name:</strong> ${proofTask.user.name},
                                            <strong>User Ip:</strong> ${proofTask.user_detail.ip}
                                        </div>
                                        <h4>Proof Answer:</h4>
                                        <div class="mb-2 border p-2">
                                            ${proofTask.proof_answer}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <h4>Proof Image:</h4>
                                        <div class="my-2">
                                            <div class="d-flex justify-content-center align-items-center">
                                                ${
                                                    JSON.parse(proofTask.proof_photos).map(image => `
                                                        <a href="{{ asset('uploads/task_proof_photo') }}/${image}" target="_blank" class="m-1">
                                                            <img src="{{ asset('uploads/task_proof_photo') }}/${image}" style="max-height: 180px; max-width: 180px" alt="Proof Image">
                                                        </a>
                                                    `).join('')
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        // Insert HTML into the modal body
                        $('#checkAllPendingTaskProofData').html(`
                            <div id="proofTaskListPendingCarousel" class="carousel slide" data-bs-ride="carousel">
                                <ol class="carousel-indicators">${indicators}</ol>
                                <div class="carousel-inner">${items}</div>
                                <a class="carousel-control-prev" data-bs-target="#proofTaskListPendingCarousel" role="button" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </a>
                                <a class="carousel-control-next" data-bs-target="#proofTaskListPendingCarousel" role="button" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </a>
                            </div>
                        `);

                        // Get references to the carousel and hidden input
                        const carouselElement = document.getElementById('proofTaskListPendingCarousel');
                        const set_proof_task_id = document.getElementById('set_proof_task_id');

                        // Set the initial ID from the first active item
                        function setInitialProofTaskId() {
                            const initialActiveItem = carouselElement.querySelector('.carousel-item.active');
                            if (initialActiveItem) {
                                const proofId = initialActiveItem.getAttribute('data-proof-id');
                                set_proof_task_id.value = proofId;
                            }
                        }

                        // Update the ID whenever the carousel slides
                        function updateProofTaskId() {
                            const activeItem = carouselElement.querySelector('.carousel-item.active');
                            if (activeItem) {
                                const proofId = activeItem.getAttribute('data-proof-id');
                                set_proof_task_id.value = proofId;
                            }
                        }

                        // Set the initial ID when the page loads
                        setInitialProofTaskId();

                        // Attach the slid.bs.carousel event to update the input value
                        carouselElement.addEventListener('slid.bs.carousel', updateProofTaskId);

                        // carousel auto slide off
                        $('#proofTaskListPendingCarousel').carousel({
                            interval: false
                        });
                    }
                },
            });
        }

        // Check All Pending Task Proof
        $(document).on('click', '.checkAllPendingTaskProofBtn', function() {
            getCheckAllPendingTaskProof();
        });

        // Rating stars
        const stars = document.querySelectorAll(".stars i");
        const ratingInput = document.getElementById('rating');
        stars.forEach((star, index1) => {
            star.addEventListener("click", () => {
                stars.forEach((star, index2) => {
                    index1 >= index2 ? star.classList.add("active") : star.classList.remove("active");
                });
                ratingInput.value = index1 + 1;
            });
            star.addEventListener("dblclick", () => {
                stars.forEach((star) => {
                    star.classList.remove("active");
                });
                ratingInput.value = 0;
            });
        });

        // Hide rejected reason div initially
        $('#approved_div').hide();
        $('#rejected_div').hide();
        $('input[name="status"]').change(function() {
            $('.update_status_error').text('');
            if ($(this).val() == 'Rejected') {
                $('#approved_div').hide();
                $('#rejected_div').show();
                $('#bonus').val(0);
                // Reset rating stars
                stars.forEach((star) => {
                    star.classList.remove("active");
                });
                ratingInput.value = 0;
            } else {
                $('#approved_div').show();
                $('#rejected_div').hide();
                $('#rejected_reason').val('');
            }
        });

        // Photo Preview
        $(document).on('change', '#rejected_reason_photo', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#rejected_reason_photoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        $("body").on("submit", "#checkAllPendingTaskProofEditForm", function(e) {
            e.preventDefault();

            // Disable the submit button to prevent multiple submissions
            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var id = $('#set_proof_task_id').val();
            var url = "{{ route('proof_task.check.update', ':id') }}".replace(':id', id);
            var formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status === 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.update_' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        if (response.status === 401) {
                            toastr.error(response.error);
                        } else {
                            getCheckAllPendingTaskProof();
                            $("#deposit_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.deposit_balance);
                            $("#withdraw_balance_div strong").html('{{ get_site_settings('site_currency_symbol') }} ' + response.withdraw_balance);
                            $('#allDataTable').DataTable().ajax.reload();
                            toastr.success('Proof Task has been updated successfully.');

                            // Reset the form
                            $('#checkAllPendingTaskProofEditForm')[0].reset();
                            $('#approved_div').hide();
                            $('#rejected_div').hide();

                            // Reset rating stars
                            stars.forEach((star) => {
                                star.classList.remove("active");
                            });
                            ratingInput.value = 0;
                        }
                    }
                },
                complete: function() {
                    // Re-enable the submit button after the request completes
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // Store filters in localStorage
        function storeFilters() {
            localStorage.setItem('filter_status', $('#filter_status').val());
        }

        // Restore filters from localStorage
        function restoreFilters() {
            if (localStorage.getItem('filter_status')) {
                $('#filter_status').val(localStorage.getItem('filter_status'));
            }
        }

        // Restore filters before initializing DataTable
        restoreFilters();

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            // serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('proof_task.list', encrypt($postTask->id)) }}",
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
                { data: 'checked_at', name: 'checked_at' },
                { data: 'action', name: 'action' }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            storeFilters();
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
                            url: "{{ route('proof_task.approved.all', $postTask->id) }}",
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
                            url: "{{ route('proof_task.selected.item.approved') }}",
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
                            url: "{{ route('proof_task.selected.item.rejected') }}",
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
            var url = "{{ route('proof_task.check', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                },
            });
        });

        // Report Proof Task
        $(document).on('click', '.reportProofTaskBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('proof_task.report', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#report_proof_task_id').val(response.proofTask.id);
                    $('#post_task_id').val(response.proofTask.post_task_id);
                    $('#user_id').val(response.proofTask.user_id);

                    if (response.reportStatus) {
                        $('#reportSendProofTaskForm').hide();
                        $('#reportSendProofTaskExitsDiv').show();
                        $('#reportReason').text(response.reportStatus.reason);
                        $('#reportDate').text(response.reportStatus.created_at);
                        if (response.reportStatus.photo) {
                            $('#reportPhoto').show();
                            $('#reportPhoto').attr('src', '{{ asset('uploads/report_photo') }}/' + response.reportStatus.photo);
                        } else {
                            $('#reportPhoto').hide();
                        }
                        if (response.reportStatus.status == 'Resolved') {
                            $('#resolvedMessage').text('Your report has been resolved. Please check your report panel. Thank you.');
                        } else {
                            $('#pendingMessage').text('Please wait for the admin to review your report. Your report will be reviewed within 24 hours. If you have any questions, please contact the admin. Thank you.');
                        }
                    } else {
                        $('#reportSendProofTaskExitsDiv').hide();
                        $('#reportSendProofTaskForm').show();
                    }
                },
            });
        });

        // Report Proof Task Photo Preview
        $(document).on('change', '#photo', function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#photoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Report Proof Task Form
        $('#reportProofTaskForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var formData = new FormData(this);

            var user_id = $('#user_id').val();
            var url = "{{ route('report.send', ':id') }}";
            url = url.replace(':id', user_id);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status === 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        $('.reportProofTaskModal').modal('hide'); // Hide Report Proof Task modal
                        toastr.success('Proof Task reported successfully.');
                        $('#allDataTable').DataTable().ajax.reload();
                        $('#reportProofTaskForm')[0].reset();
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // Optionally, clear filters when needed
        $('#clear_filters').on('click', function() {
            localStorage.removeItem('filter_status');
            $('#filter_status').val('');
            $('#allDataTable').DataTable().ajax.reload();
        });
    });
</script>
@endsection
